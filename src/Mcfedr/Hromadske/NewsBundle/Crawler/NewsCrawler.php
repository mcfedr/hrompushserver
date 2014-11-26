<?php
/**
 * Created by Mcfedr on 28/05/2014 22:03
 */

namespace Mcfedr\Hromadske\NewsBundle\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Mcfedr\Hromadske\NewsBundle\Model\News;
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
    public function __construct($homepage, LoggerInterface $logger)
    {
        $this->homepage = $homepage;
        $this->logger = $logger;
    }

    /**
     * @return News[]
     */
    public function fetchNews()
    {
        $client = new Client();
        $res = $client->get($this->homepage);

        $news = [];

        $crawler = new Crawler((string) $res->getBody(), $this->homepage);
        $crawler->filter('ul.aside-news-list > li > a:first-child')
            ->each(function(Crawler $node, $i) use (&$news) {
                try {
                    $new = new News(
                        $node->attr('href'),
                        new \DateTime(
                            ($date = $node->filter('.date')->text()) == 'щойно' ? 'now' : $date,
                            new \DateTimeZone('Europe/Kiev')
                        ),
                        $node->filter('.content')->text()
                    );
                    $news[] = $new;
                    $this->logger->debug('Found news', [
                        'new' => $new
                    ]);
                }
                catch (\Exception $e) {
                    $this->logger->info('Error parsing news', [
                        'e' => $e,
                        'node' => $node->html()
                    ]);
                }
            });

        return $news;
    }
}