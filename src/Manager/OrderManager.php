<?php

namespace App\Manager;

use App\DTO\Cart\Request\UpdateCartDTO;
use App\DTO\Order\OrderDTO;
use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\User;
use App\Enum\DeliveryMethod;
use App\Enum\OrderStatus;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

readonly class OrderManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository,
        private AddressManager         $addressManager,
        private CartManager            $cartManager,
    ) {
    }

    /** Оформление заказа
     * @throws Exception
     */
    public function create(OrderDTO $orderDTO, User $user)
    {
        $ids = [];
        $totalQuantity = 0;
        foreach ($orderDTO->products as $product) {
            if (in_array($product['id'], $ids, true)) {
                throw new Exception(
                    'Ошибка: повторяющийся идентификатор товара ' . $product['id'],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $ids[] = $product['id'];
            $totalQuantity += $product['quantity'];
        }

        if ($totalQuantity > 20) {
            throw new Exception("Вы не можете заказать больше 20 товаров");
        }
        $products = $this->productRepository->findLatestVersionsByIdentifiers($ids);

        $deliveryMethod = DeliveryMethod::from($orderDTO->deliveryMethod);

        if (!$orderDTO->address && $deliveryMethod == DeliveryMethod::COURIER) {
            throw new Exception("Адрес не может быть пустым.", Response::HTTP_UNPROCESSABLE_ENTITY);
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
                $address = $this->addressManager->getAddress($orderDTO->address, $user);
                $order->setAddress($address);
                $this->entityManager->persist($address);
            }

            $this->entityManager->persist($order);
            $this->entityManager->flush();

            // Удаление товаров из корзины
            /* TODO Удаление из корзины тех товары, которые попали в заказ - переделать на массовое удаление */
            foreach ($orderDTO->products as $product) {

                for ($i = 0; $i <= $product['quantity']; $i++) {
                    $this->cartManager->remove(
                        new UpdateCartDTO($product['id']),
                        $user
                    );
                }
            }

            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
