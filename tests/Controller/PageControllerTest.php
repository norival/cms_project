<?php

namespace App\Tests\Controller;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class PageControllerTest extends TestCase
{
    private static $client;

    static public function setUpBeforeClass()
    {
        static::$client = new Client([
            'base_uri' => 'https://127.0.0.1:8000',
            'timeout'  => 2.0,
        ]);
    }

    public function testList()
    {
        $response = self::$client->get('/pages');

        $json = (string)$response->getBody();
        $pages = \json_decode($json, true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($pages);
        $this->assertEquals('page_1', $pages['pages'][0]['name']);
        $this->assertCount(10, $pages['pages']);
    }
}
