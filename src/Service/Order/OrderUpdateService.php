<?php

namespace App\Service\Order;

use App\DTO\Order\OrderUpdateStatusDTO;
use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class OrderUpdateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository
    ) {
    }

    public function updateStatus(OrderUpdateStatusDTO $orderUpdateStatusDTO): void
    {
        $order = $this->orderRepository->getById($orderUpdateStatusDTO->orderId);

        $newStatus = OrderStatus::from($orderUpdateStatusDTO->newStatus);
        $order->setStatus($newStatus);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
