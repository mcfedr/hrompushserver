<?php

namespace Tests\Mcfedr\Hromadske\ApiBundle\Controller;

use Mcfedr\YouTube\LiveStreamsBundle\Streams\YouTubeStreamsLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testStreams()
    {
        $client = static::createClient();

        $loaderMock = $this->getMockBuilder(YouTubeStreamsLoader::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStreams'])
            ->getMock();

        $loaderMock->expects($this->once())->method('getStreams')->willReturn([]);

        $client->getContainer()->set('mcfedr_you_tube_live_streams.loader', $loaderMock);

        $client->request('GET', '/streams');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $content = $client->getResponse()->getContent();
        $decoded = json_decode($content, true);

        $this->assertNotNull($decoded);
        $this->assertInternalType('array', $decoded);
        $this->assertArrayHasKey('streams', $decoded);
        $this->assertInternalType('array', $decoded['streams']);
        foreach ($decoded['streams'] as $stream) {
            $this->assertInternalType('array', $stream);
            $this->assertArrayHasKey('name', $stream);
            $this->assertArrayHasKey('thumb', $stream);
            $this->assertArrayHasKey('videoId', $stream);
        }

        $this->assertArrayHasKey('radio', $decoded);
        $this->assertInternalType('array', $decoded['radio']);
        foreach ($decoded['radio'] as $radio) {
            $this->assertInternalType('array', $radio);
            $this->assertArrayHasKey('host', $radio);
            $this->assertArrayHasKey('stream', $radio);
        }
    }
}
