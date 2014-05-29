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
        $res = \GuzzleHttp\get($this->homepage);

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
