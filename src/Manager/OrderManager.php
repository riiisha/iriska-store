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

class OrderManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository      $productRepository,
        private readonly AddressManager         $addressManager,
        private readonly CartManager            $cartManager
    )
    {
    }


    /*
     *  {
        "phone": "+79811732603",
        "deliveryMethod": "courier",
        "address": {
            "city" : "cityTest",
            "street" : "streetTest",
            "house": "houseTest"
        },
        "products": [
        {
          "id": 1,
          "version": 3,
          "quantity": 1
        },
        {
          "id": 1,
          "version": 3,
          "quantity": 1
        },

        {
          "id": 2,
          "version": 1,
          "quantity": 2
        },
        {
          "id": 3,
          "version": 2,
          "quantity": 1
        }
      ]
    }
     * */
    /*TODO Удалить из корзины те товары, которые пользователь уже купил*/
    /*TODO Сделать все в рамках транзакции, чтобы не очищать корзину, если заказ не был оформлен */
    /*TODO Добавить логирование  */
    /** Оформление заказа
     * @throws Exception
     */
    public function create(OrderDTO $orderDTO, User $user)
    {
        $cart = $user->getCart();

        /* TODO Не знаю, лучше добавить последние версии товаров или те, которые были в последний момент в корзине */
        $ids = [];
        $totalQuantity = 0;

        foreach ($orderDTO->products as $product) {
            if (in_array($product['id'], $ids, true)) {
                throw new Exception('Ошибка: повторяющийся идентификатор товара ' . $product['id']);
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
            throw new Exception("Адрес не может быть пустым.");
        }

        $order = new Order();
        $order->setPhone($orderDTO->phone);
        $order->setOwner($user);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setStatus(OrderStatus::PAID);

        foreach ($orderDTO->products as $item) {
            foreach ($products as $product) {
                // Находим соответствующий товар по id
                if ($product->getId() === $item['id']) {
                    $orderItem = new OrderItem();
                    $orderItem->setProduct($product);
                    $orderItem->setQuantity($item['quantity']);
                    $order->addOrderItem($orderItem);
                    break; // прерываем цикл, как только нашли соответствующий товар
                }
            }
        }

        /* Самовывоз пока без адреса, так как у нас маленький магазин с одной точкой продаж (: */
        if ($deliveryMethod == DeliveryMethod::COURIER) {
            $address = $this->addressManager->getAddress($orderDTO->address, $user);
            $order->setAddress($address);
            $this->entityManager->persist($address);

        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
//        dump("Заказ сформирован");

        foreach ($orderDTO->products as $product) {
            for ($i = 0; $i <= $product['quantity']; $i++) {
                $this->cartManager->remove(
                    new UpdateCartDTO($product['id']), $user
                );
            }
//            dump("Товар " . $product['id'] . "удален из корзины");
        }
    }


}
