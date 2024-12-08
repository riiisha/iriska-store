<?php

namespace App\Tests\Controller\Api\Order;

use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderShowControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return '/api/order';
    }

    public function testShowActionSuccess()
    {
        $this->loginUser();

        $this->getRequest($this->getUrl());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testShowActionFailureUnauthorized()
    {
        $this->getRequest($this->getUrl());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
