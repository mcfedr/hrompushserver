<?php

namespace mcfedr\Hromadske\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testStreams()
    {
        $client = static::createClient();

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
