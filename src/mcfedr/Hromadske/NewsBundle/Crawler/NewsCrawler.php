<?php
/**
 * Created by mcfedr on 28/05/2014 22:03
 */

namespace mcfedr\Hromadske\NewsBundle\Crawler;

use GuzzleHttp\Message\ResponseInterface;
use mcfedr\Hromadske\NewsBundle\Model\News;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class NewsCrawler
{
    /** @var  LoggerInterface */
    private $logger;

    /** @var string */
    private $homepage;

    /**
     * @param string $homepage
     * @param LoggerInterface $logger
     */
    function __construct($homepage, LoggerInterface $logger)
    {
        $this->homepage = $homepage;
        $this->logger = $logger;
    }

    public function fetchNews()
    {
        $client = new \GuzzleHttp\Client();
        /** @var ResponseInterface $res */
        $res = $client->get($this->homepage);

        $news = [];

        $crawler = new Crawler((string) $res->getBody(), $this->homepage);
        $crawler->filter('.aside-news-list > li > a')
            ->each(function(Crawler $node, $i) use (&$news) {
                $news[] = new News(
                    $node->attr('href'),
                    new \DateTime(($date = $node->filter('.date')->text()) == 'щойно' ? 'now' : $date, new \DateTimeZone('Europe/Kiev')),
                    $node->filter('.content')->text()
                );
            });

        return $news;
    }
} 