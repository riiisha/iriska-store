<?php

namespace App\EventListener\Order;

use App\DTO\Address\DeliveryAddressDTO;
use App\DTO\Notification\OrderItemDTO;
use App\DTO\Notification\OrderNotificationEmailDTO;
use App\Entity\Order\Order;
use App\Entity\User;
use App\Enum\DeliveryMethod;
use App\Enum\NotificationType;
use App\Event\Order\OrderCreateEvent;
use App\Service\NotificationService;
use Symfony\Contracts\EventDispatcher\Event;

class OrderCreateEventListener extends Event
{
    public function __construct(
        private readonly NotificationService $notificationService
    ){
    }

    public function onOrderCreate(OrderCreateEvent $event): void
    {
        $notification = $this->createNotification(
            $event->getOrder(),
            $event->getUser()
        );
        $this->notificationService->sendEmail($notification);
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
