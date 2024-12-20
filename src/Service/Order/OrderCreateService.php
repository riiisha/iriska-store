<?php

namespace App\Service\Order;

use App\DTO\Order\OrderDTO;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\User;
use App\Enum\DeliveryMethod;
use App\Event\Order\OrderCreateEvent;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Address\AddressService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class OrderCreateService
{
    public function __construct(
        private EntityManagerInterface   $entityManager,
        private ProductRepository        $productRepository,
        private OrderRepository          $orderRepository,
        private AddressService           $addressService,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface          $logger,
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
            // Обработка адреса для курьерской доставки
            if ($deliveryMethod == DeliveryMethod::COURIER) {
                $address = $this->addressService->getAddress($orderDTO->address, $user);
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

            $this->orderRepository->save($order);

            // Очистка корзины
            $this->cleanCart($orderDTO, $user);

            $this->entityManager->flush();

            $event = new OrderCreateEvent($order, $user);
            $this->eventDispatcher->dispatch($event, OrderCreateEvent::NAME);

            $this->logger->debug('[OrderCreateService] Order created', ['orderId' => $order->getId()]);
        } catch (Exception $e) {
            $this->logger->error('[OrderCreateService] Order was not created', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Очистка корзины
     */
    private function cleanCart(OrderDTO $orderDTO, User $user): void
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
    }
}
