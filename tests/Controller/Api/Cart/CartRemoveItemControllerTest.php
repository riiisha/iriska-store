<?php

namespace App\Tests\Controller\Api\Cart;

use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CartRemoveItemControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return '/api/cart';
    }

    public function testRemoveItemActionSuccess()
    {
        $this->loginUser();
        $this->deleteRequest($this->getUrl(), ['productId' => 1]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRemoveItemActionFailure()
    {
        $this->loginUser();
        $this->deleteRequest($this->getUrl(), ['productId' => '']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRemoveItemActionFailureUnauthorized()
    {
        $this->deleteRequest($this->getUrl(), ['productId' => '']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
