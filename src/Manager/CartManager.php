<?php

namespace App\Manager;

use App\DTO\Cart\Request\AddToCartDTO;
use App\DTO\Cart\Request\UpdateCartDTO;
use App\DTO\Cart\Response\CartItemDTO;
use App\DTO\Cart\Response\ShowCartDTO;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class CartManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository
    ) {
    }

    /** Просмотр корзины пользователя */
    public function show(User $user): ShowCartDTO
    {
        $cart = $user->getCart();
        if (!$cart) {
            $cart = new Cart();
            $cart->setOwner($user);
        } else {
            $this->updateProductVersions($cart);
        }

        $cartItems = [];
        $totalCost = 0;
        foreach ($cart->getCartItems() as $cartItem) {
            $totalCost += $cartItem->getProduct()->getCost() * $cartItem->getQuantity();
            $cartItems[] = new CartItemDTO(
                $cartItem->getProduct()->getName(),
                $cartItem->getProduct()->getCost(),
                $cartItem->getQuantity()
            );
        }
        return new ShowCartDTO($cartItems, $totalCost);
    }

    /** Добавление одного товара в корзину пользователя */
    public function add(AddToCartDTO $addToCartDTO, User $user): void
    {
        $product = $this->productRepository->findProductWithLatestVersion($addToCartDTO->productId);
        if (!$product) {
            throw new NotFoundHttpException("Товар не найден");
        }

        $cart = $user->getCart();
        if (!$cart) {
            $cart = new Cart();
            $cart->setOwner($user);
        }

        $cartItem = $cart->getCartItems()->filter(function (CartItem $item) use ($product): bool {
            return $item->getProduct()->getId() === $product->getId();
        })->first();

        if (!$cartItem) {
            $cartItem = new CartItem();
            $cartItem
                ->setCart($cart)
                ->setProduct($product)
                ->setQuantity(1);
        } else {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        }

        $this->entityManager->persist($cart);
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    /** Удаление одного товара из корзины пользователя */
    public function remove(UpdateCartDTO $updateCartDTO, User $user): void
    {
        $cart = $user->getCart();

        if (!$cart) {
            return;
        }

        $cartItem = $cart->getCartItems()->filter(function (CartItem $item) use ($updateCartDTO): bool {
            return $item->getProduct()->getId() === $updateCartDTO->productId;
        })->first();

        if (!$cartItem) {
            return;
        }

        if ($cartItem->getQuantity() == 1) {
            $cart->removeCartItem($cartItem);
            $this->entityManager->persist($cart);
        } else {
            $cartItem->setQuantity($cartItem->getQuantity() - 1);
            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();
    }

    /** Обновление версий товаров в корзине*/
    private function updateProductVersions(Cart $cart): void
    {
        foreach ($cart->getCartItems() as $cartItem) {
            $product = $cartItem->getProduct();
            $newProduct = $this->productRepository->findProductWithLatestVersion($product->getId());
            $cartItem->setProduct($newProduct);
            $this->entityManager->persist($cartItem);
        }
        $this->entityManager->flush();
    }
}
