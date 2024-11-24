<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends BaseWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->endpoint = '/api/products';
    }

    public function testListSuccess()
    {
        $this->client->request(
            'GET', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
    }
}
