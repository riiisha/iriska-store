<?php

namespace App\Repository;

use App\Entity\Order\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getById(int $id): Order
    {
        $order = $this->findOneBy(['id' => $id]);

        if (!$order) {
            throw new NotFoundHttpException("Order not found.");
        }

        return $order;
    }
}
