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

    public function testAddItemActionSuccess(): void
    {
        $user = $this->loginUser();

        // Считаем общее количество товаров в корзине до запроса
        $cartItems = $user->getCart() ? $user->getCart()->getCartItems() : [];
        $quantityBefore = 0;
        foreach ($cartItems as $cartItem) {
            $quantityBefore += $cartItem->getQuantity();
        }

        $this->postRequest($this->getUrl(), ['productId' => 1]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Считаем общее количество товаров в корзине после запроса
        $connection = $this->client->getContainer()->get('database_connection');
        $cartItems = $connection->fetchAllAssociative(
            'SELECT * FROM cart_item WHERE cart_id = (SELECT id FROM cart WHERE owner_id = ' . $user->getId() . ')'
        );
        $quantityAfter = array_sum(array_map(fn($item) => $item['quantity'], $cartItems));
        $this->assertEquals($quantityBefore + 1, $quantityAfter);
    }


    public function testAddItemActionFailure(): void
    {
        $this->loginUser();

        $this->postRequest($this->getUrl(), ['productId' => '']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAddItemActionFailureUnauthorized(): void
    {
        $this->postRequest($this->getUrl(), ['productId' => 1]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
