<?php

namespace App\Service\Order;

use App\DTO\Address\DeliveryAddressDTO;
use App\DTO\Notification\OrderItemDTO;
use App\DTO\Notification\OrderNotificationEmailDTO;
use App\DTO\Order\OrderDTO;
use App\Entity\CartItem;
use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\User;
use App\Enum\DeliveryMethod;
use App\Enum\NotificationType;
use App\Repository\ProductRepository;
use App\Service\Address\AddressService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class OrderCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository,
        private AddressService         $addressService,
        private NotificationService    $notificationService,
        private LoggerInterface        $logger
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
                throw new HttpException(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    "Error: Duplicate product ID: {$product['id']}.",
                );
            }
            $ids[] = $product['id'];
            $totalQuantity += $product['quantity'];
        }

        if ($totalQuantity > Order::MAX_QUANTITY_ORDER_ITEMS) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                "You cannot order more than " . Order::MAX_QUANTITY_ORDER_ITEMS . " items."
            );
        }
        $products = $this->productRepository->findLatestVersionsByIdentifiers($ids);

        $deliveryMethod = DeliveryMethod::from($orderDTO->deliveryMethod);

        if (!$orderDTO->address && $deliveryMethod == DeliveryMethod::COURIER) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "Address cannot be empty.");
        }

        try {
            $this->entityManager->beginTransaction();

            // Обработка адреса для курьерской доставки
            if ($deliveryMethod == DeliveryMethod::COURIER) {
                $address = $this->addressService->getAddress($orderDTO->address, $user);
                $this->entityManager->persist($address);
            }

            $order = new Order($orderDTO->phone, $user, $deliveryMethod, $address ?? null);

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

            $this->entityManager->persist($order);

            // Очистка корзины
            $this->cleaningCart($orderDTO, $user);

            $this->entityManager->flush();
            $this->entityManager->commit();

            $notification = $this->createNotification($order, $user);
            $this->notificationService->sendEmail($notification);

            $this->logger->debug("Order created (ID: {$order->getId()})");
        } catch (Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Order was not created due to an error:' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Очистка корзины
     */
    private function cleaningCart(OrderDTO $orderDTO, User $user): void
    {
        $cart = $user->getCart();

        if (!$cart) {
            return;
        }

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

    private function createNotification(Order $order, User $user): OrderNotificationEmailDTO
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $orderItems[] = new OrderItemDTO(
                $product->getName(),
                $product->getCost() * $orderItem->getQuantity(),
                null
            );
        }
        if ($order->getDeliveryMethod() == DeliveryMethod::COURIER) {
            $deliveryAddress = new DeliveryAddressDTO(
                $order->getAddress()->__toString(),
                null
            );
        }
        return new OrderNotificationEmailDTO(
            $user->getEmail(),
            NotificationType::SUCCESS_PAYMENT->value,
            (string)$order->getId(),
            $orderItems ?? [],
            $order->getDeliveryMethod()->value,
            $deliveryAddress ?? null
        );
    }
}
