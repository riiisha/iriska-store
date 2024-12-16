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
        $user = $this->loginUser();
        $connection = $this->client->getContainer()->get('database_connection');

        // Считаем общее количество товаров в корзине до запроса
        $cartItems = $connection->fetchAllAssociative(
            'SELECT * FROM cart_item WHERE cart_id = (SELECT id FROM cart WHERE owner_id = ' . $user->getId() . ')'
        );
        $quantityBefore = array_sum(array_map(fn($item) => $item['quantity'], $cartItems));

        $this->deleteRequest($this->getUrl(), ['productId' => 1]);

        // Считаем общее количество товаров в корзине после запроса
        $cartItems = $connection->fetchAllAssociative(
            'SELECT * FROM cart_item WHERE cart_id = (SELECT id FROM cart WHERE owner_id = ' . $user->getId() . ')'
        );
        $quantityAfter = array_sum(array_map(fn($item) => $item['quantity'], $cartItems));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals($quantityBefore - 1, $quantityAfter);
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
