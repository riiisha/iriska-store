<?php

namespace App\Repository;

use App\Entity\Order\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        $this->em = $em;
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

    public function save(Order $order, bool $flash = false): void
    {
        $this->em->persist($order);
        if ($flash) {
            $this->em->flush();
        }
    }
}
