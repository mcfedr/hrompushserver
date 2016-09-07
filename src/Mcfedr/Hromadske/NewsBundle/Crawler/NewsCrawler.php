<?php
/**
 * Created by Mcfedr on 28/05/2014 22:03
 */

namespace Mcfedr\Hromadske\NewsBundle\Crawler;

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Client;
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
        $res = $client->get($this->homepage);

        $news = [];

        $crawler = new Crawler($res->getBody()->getContents(), $this->homepage);

        $pre = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'uk_UA');

        $crawler->filter('.content-entity-thumb')
            ->each(function(Crawler $node, $i) use (&$news) {
                try {
                    $dateString = ($dateString = $node->filter('.publish-time')->text()) == 'щойно' ? 'now' : $dateString;

                    $dateComponents = strptime($dateString, '%e %B, %H:%M');
                    if ($dateComponents) {
                        $time = \DateTime::createFromFormat('n-j G:i', sprintf('%s-%s %s:%02s', $dateComponents['tm_mon'], $dateComponents['tm_mday'], $dateComponents['tm_hour'], $dateComponents['tm_min']), new \DateTimeZone('Europe/Kiev'));
                    } else {
                        $time = new \DateTime();
                    }

                    $new = new News(
                        $node->filter('.title a')->attr('href'),
                        $time,
                        $node->filter('.title a')->text()
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

        setlocale(LC_ALL, $pre);

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
