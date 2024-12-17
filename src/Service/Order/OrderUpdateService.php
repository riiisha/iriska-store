<?php

namespace App\Service\Order;

use App\DTO\Order\OrderUpdateStatusDTO;
use App\Enum\OrderStatus;
use App\Repository\OrderRepository;

readonly class OrderUpdateService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {
    }

    public function updateStatus(OrderUpdateStatusDTO $orderUpdateStatusDTO): void
    {
        $order = $this->orderRepository->getById($orderUpdateStatusDTO->orderId);

        $newStatus = OrderStatus::from($orderUpdateStatusDTO->newStatus);
        $order->setStatus($newStatus);

        $this->orderRepository->save($order, true);
    }
}
