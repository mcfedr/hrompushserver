<?php
/**
 * Created by Mcfedr on 28/05/2014 22:03
 */

namespace Mcfedr\Hromadske\NewsBundle\Crawler;

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
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
     * @var Cache
     */
    protected $cache;

    /**
     * @var int
     */
    protected $cacheTimeout;

    /**
     * @param string $homepage
     * @param LoggerInterface $logger
     * @param Cache $cache
     * @param int $cacheTimeout
     */
    public function __construct($homepage, LoggerInterface $logger, Cache $cache = null, $cacheTimeout = 0)
    {
        $this->homepage = $homepage;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->cacheTimeout = $cacheTimeout;
    }

    /**
     * @return News[]
     */
    public function fetchNews()
    {
        if ($this->cache) {
            $data = $this->cache->fetch($this->getCacheKey());
            if ($data !== false) {
                return $data;
            }
        }

        $client = new Client();
        /** @var Response $res */
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

        if ($this->cache && $this->cacheTimeout > 0) {
            $this->cache->save($this->getCacheKey(), $news, $this->cacheTimeout);
        }

        return $news;
    }

    protected function getCacheKey()
    {
        return "mcfedr_hromadske_news.{$this->homepage}";
    }
}
