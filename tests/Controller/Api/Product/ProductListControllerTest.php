<?php

namespace App\Tests\Controller\Api\Product;

use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductListControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return '/api/products';
    }

    public function testListSuccess()
    {
        $this->getRequest($this->getUrl());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
    }
}
