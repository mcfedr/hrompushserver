<?php
/**
 * Created by mcfedr on 28/05/2014 22:31
 */

namespace Tests\Mcfedr\Hromadske\NewsBundle\Crawler;

use Mcfedr\Hromadske\NewsBundle\Crawler\NewsCrawler;
use Mcfedr\Hromadske\NewsBundle\Model\News;
use Psr\Log\LoggerInterface;

class NewsCrawlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var NewsCrawler */
    private $news;

    public function setUp()
    {
        $this->news = new NewsCrawler('http://www.hromadske.tv/', $this->getMockBuilder(LoggerInterface::class)->getMock());
    }

    public function testFetch()
    {
        $news = $this->news->fetchNews();
        $this->assertInternalType('array', $news);
        foreach ($news as $new) {
            $this->assertInstanceOf(News::class, $new);
        }
    }
}
