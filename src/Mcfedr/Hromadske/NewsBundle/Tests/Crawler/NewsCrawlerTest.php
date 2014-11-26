<?php
/**
 * Created by mcfedr on 28/05/2014 22:31
 */

namespace Mcfedr\Hromadske\NewsBundle\Tests\Crawler;

use Mcfedr\Hromadske\NewsBundle\Crawler\NewsCrawler;

class NewsCrawlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var NewsCrawler */
    private $news;

    public function setUp()
    {
        $this->news = new NewsCrawler('http://www.hromadske.tv/', $this->getMock('Psr\Log\LoggerInterface'));
    }

    public function testFetch()
    {
        $news = $this->news->fetchNews();
        $this->assertInternalType('array', $news);
        foreach ($news as $new) {
            $this->assertInstanceOf('\Mcfedr\Hromadske\NewsBundle\Model\News', $new);
        }
    }
}
