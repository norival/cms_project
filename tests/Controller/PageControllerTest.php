<?php

namespace App\Tests\Controller;

use GuzzleHttp\Client;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    use FixturesTrait;

    private static $client;

    static public function setUpBeforeClass()
    {
        static::$client = new Client([
            'base_uri' => 'https://127.0.0.1:8000',
            'timeout'  => 2.0,
        ]);
    }

    public function setUp()
    {
        $this->loadFixtures([
            'App\DataFixtures\NodeFixtures',
            'App\DataFixtures\PageFixtures',
            'App\DataFixtures\UserFixtures',
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

    public function testNew()
    {
        $data = [
            'title'   => 'My new page',
            'content' => 'The content of the new page',
            'parent'  => 'pages',
            'path'    => '/pages/page_42',
            'name'    => 'page_42',
            'locale'  => 'fr',
        ];

        $response = self::$client->post('/pages', [
            'body' => \json_encode($data),
        ]);
        $responseData = \json_decode($response->getBody(true), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/pages/page_42', $response->getHeader('Location')[0]);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('page_42', $responseData['name']);
    }

    public function testShow()
    {
        $response = self::$client->get('/pages/page_1');

        $json = (string)$response->getBody();
        $page = \json_decode($json, true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($page);
        $this->assertArrayHasKey('title', $page);
        $this->assertCount(11, $page);
        /* $this->assertEquals('page_1', $pages['pages'][0]['name']); */
        /* $this->assertCount(10, $pages['pages']); */
    }

    public function testDelete()
    {
        $response = self::$client->delete('/pages/page_1');

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $data = [
            'title'   => 'My new page: updated',
            'content' => 'The content of the new page',
            'parent'  => '/pages',
            'path'    => "/pages/page_2",
            'name'    => "page_2",
            'locale'  => 'fr',
        ];

        $response = self::$client->put(
            '/pages/page_2',
            [
                'body' => \json_encode($data),
            ]
        );
        $json = (string)$response->getBody();
        $page = \json_decode($json, true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('My new page: updated', $page['title']);
    }

    public function testPatch()
    {
        $data = [
            'content' => 'updated',
        ];

        $response = self::$client->patch(
            '/pages/page_2',
            [
                'body' => \json_encode($data),
            ]
        );
        $json = (string)$response->getBody();
        $page = \json_decode($json, true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('updated', $page['content']);
    }

    public function testValidationErrors()
    {
        $data = [
            /* 'title'   => 'My new invalid page', */
            'content' => 'The content of the new page',
            'parent'  => 'pages',
            'path'    => '/pages/page_invalid',
            'name'    => 'page_invalid',
            'locale'  => 'fr',
        ];

        $response = self::$client->post('/pages', [
            'body' => \json_encode($data),
        ]);

        $responseData = \json_decode($response->getBody(true), true);

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertArrayHasKey('type', $responseData);
        $this->assertArrayHasKey('title', $responseData);
        $this->assertArrayHasKey('errors', $responseData);

        $this->assertArrayHasKey('title', $responseData['errors']);
        $this->assertEquals('The page must have a title', $responseData['errors']['title'][0]);
    }
}
