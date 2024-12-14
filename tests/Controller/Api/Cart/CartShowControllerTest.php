<?php

namespace App\Tests\Controller\Api\Cart;

use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CartShowControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_cart_show');
    }

    public function testShowActionSuccess(): void
    {
        $this->loginUser();

        $this->getRequest($this->getUrl());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testShowActionFailureUnauthorized(): void
    {
        $this->getRequest($this->getUrl());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
