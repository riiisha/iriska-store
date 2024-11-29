<?php

namespace App\Service\Order;

use App\DTO\Order\OrderDTO;
use App\Entity\CartItem;
use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\User;
use App\Enum\DeliveryMethod;
use App\Enum\OrderStatus;
use App\Repository\ProductRepository;
use App\Service\Address\AddressService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

readonly class OrderCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository,
        private AddressService         $addressService,
    ) {
    }

    /**
     * Оформление заказа
     * @throws Exception
     */
    public function create(OrderDTO $orderDTO, User $user): void
    {
        $ids = [];
        $totalQuantity = 0;
        foreach ($orderDTO->products as $product) {
            if (in_array($product['id'], $ids, true)) {
                throw new Exception(
                    'Error: Duplicate product ID: ' . $product['id'],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $ids[] = $product['id'];
            $totalQuantity += $product['quantity'];
        }

        if ($totalQuantity > 20) {
            throw new Exception("You cannot order more than 20 items.");
        }
        $products = $this->productRepository->findLatestVersionsByIdentifiers($ids);

        $deliveryMethod = DeliveryMethod::from($orderDTO->deliveryMethod);

        if (!$orderDTO->address && $deliveryMethod == DeliveryMethod::COURIER) {
            throw new Exception("Address cannot be empty.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $this->entityManager->beginTransaction();

            $order = new Order();
            $order->setPhone($orderDTO->phone);
            $order->setOwner($user);
            $order->setDeliveryMethod($deliveryMethod);
            $order->setStatus(OrderStatus::PAID);

            foreach ($orderDTO->products as $item) {
                foreach ($products as $product) {
                    if ($product->getId() === $item['id']) {
                        $orderItem = new OrderItem();
                        $orderItem->setProduct($product);
                        $orderItem->setQuantity($item['quantity']);
                        $order->addOrderItem($orderItem);
                        break;
                    }
                }
            }

            // Обработка адреса для курьерской доставки
            if ($deliveryMethod == DeliveryMethod::COURIER) {
                $address = $this->addressService->getAddress($orderDTO->address, $user);
                $order->setAddress($address);
                $this->entityManager->persist($address);
            }
            $this->entityManager->persist($order);

            // Очистка корзины
            $this->cleanCarts($orderDTO, $user);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Очистка корзины
     */
    private function cleanCarts(OrderDTO $orderDTO, User $user): void
    {
        $cart = $user->getCart();
        foreach ($orderDTO->products as $product) {
            $cartItem = $cart->getCartItems()->filter(function (CartItem $item) use ($product): bool {
                return $item->getProduct()->getId() === $product['id'];
            })->first();
            if ($cartItem) {
                $cart->removeCartItems($cartItem, $product['quantity']);
            }
        }
        $this->entityManager->persist($cart);
    }
}
