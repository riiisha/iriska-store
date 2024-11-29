<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends BaseWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser();
        $this->endpoint = '/api/order';

        $this->data = [
            'phone' => 'phone',
            'deliveryMethod' => 'courier',
            'products' => [[
                'id' => 1,
                'quantity' => 1
            ]],
            'address' => [
                'city' => 'city',
                'street' => 'street',
                'house' => 'house',
            ],
        ];
    }

    public function testCreateActionSuccessCourier()
    {
        $this->client->request(
            'POST', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($this->data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }


    public function testCreateActionSuccessPickup()
    {
        $data = $this->data;
        $data['deliveryMethod'] = 'pickup';

        $this->client->request(
            'POST', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateActionFailure()
    {
        $data = $this->data;
        $data['phone'] = '79111111111111111';

        $this->client->request(
            'POST', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShowOrder()
    {
        $this->client->request(
            'GET', $this->endpoint, [], [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());

    }
}
