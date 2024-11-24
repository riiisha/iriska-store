<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;


class CartControllerTest extends BaseWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
        $this->endpoint = '/api/cart';
    }

    public function testAddItemActionSuccess()
    {
        $data = ['productId' => 1];
        $this->client->request(
            'POST', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testAddItemActionFailure()
    {
        $data = ['productId' => ''];
        $this->client->request(
            'POST', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShowCart()
    {
        $this->client->request(
            'GET', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testRemoveItemActionSuccess()
    {
        $data = ['productId' => 1];
        $this->client->request(
            'DELETE', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRemoveItemActionFailure()
    {
        $data = ['productId' => ''];
        $this->client->request(
            'DELETE', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
