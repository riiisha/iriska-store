<?php

namespace App\Tests\Controller\Api\Cart;

use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CartRemoveItemControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_cart_remove_item');
    }

    public function testRemoveItemActionSuccess(): void
    {
        $this->loginUser();
        $this->deleteRequest($this->getUrl(), ['productId' => 1]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRemoveItemActionFailure(): void
    {
        $this->loginUser();
        $this->deleteRequest($this->getUrl(), ['productId' => '']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRemoveItemActionFailureUnauthorized(): void
    {
        $this->deleteRequest($this->getUrl(), ['productId' => '']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
