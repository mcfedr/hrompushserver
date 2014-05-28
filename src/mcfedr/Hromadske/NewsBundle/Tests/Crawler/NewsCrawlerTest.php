<?php
/**
 * Created by mcfedr on 28/05/2014 22:31
 */

namespace mcfedr\Hromadske\NewsBundle\Tests\Crawler;

use mcfedr\Hromadske\NewsBundle\Crawler\NewsCrawler;

class NewsCrawlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var NewsCrawler */
    private $news;

    public function setUp()
    {
        $this->news = new NewsCrawler('http://www.hromadske.tv/', $this->getMock('Monolog\Logger', null, ['main']));
    }

    public function testFetch()
    {
        $news = $this->news->fetchNews();
        $this->assertInternalType('array', $news);
        foreach ($news as $new) {
            $this->assertInstanceOf('\mcfedr\Hromadske\NewsBundle\Model\News', $new);
        }
    }
} 