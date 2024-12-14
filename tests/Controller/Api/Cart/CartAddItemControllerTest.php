<?php

namespace App\Tests\Controller\Api\Cart;

use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CartAddItemControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_cart_add_item');
    }

    public function testAddItemActionSuccess()
    {
        $this->loginUser();

        $this->postRequest($this->getUrl(), ['productId' => 1]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testAddItemActionFailure()
    {
        $this->loginUser();

        $this->postRequest($this->getUrl(), ['productId' => '']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAddItemActionFailureUnauthorized()
    {
        $this->postRequest($this->getUrl(), ['productId' => 1]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
