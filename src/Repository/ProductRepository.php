<?php

namespace App\Repository;

use App\DTO\Product\ProductFilter;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param int $id
     * @param int $version
     * @return Product|null
     */
    public function findByIdentifiers(int $id, int $version): ?Product
    {
        return $this->findOneBy(['id' => $id, 'version' => $version]);
    }

    /**
     * @param int $id
     * @return Product|null
     */
    public function findProductWithLatestVersion(int $id): ?Product
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->orderBy('p.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param ProductFilter $filter
     * @return Product[]
     */
    public function findProductsWithLatestVersion(ProductFilter $filter): array
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.version = (
                SELECT MAX(sub.version)
                FROM App\Entity\Product sub
                WHERE sub.id = p.id
            )')
            ->andWhere('LOWER(p.name) LIKE LOWER(:name)')
            ->setParameter('name', '%' . $filter->name . '%')
            ->orderBy('p.id', 'ASC')
            ->setFirstResult(($filter->page - 1) * $filter->limit)
            ->setMaxResults($filter->limit);

        if ($filter->minCost !== null) {
            $query
                ->andWhere('p.cost >= :minCost')
                ->setParameter('minCost', $filter->minCost);
        }
        if ($filter->maxCost !== null) {
            $query
                ->andWhere('p.cost <= :maxCost')
                ->setParameter('maxCost', $filter->maxCost);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param int[] $ids
     * @return Product[]
     */
    public function findLatestVersionsByIdentifiers(array $ids): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->andWhere('p.version = (
            SELECT MAX(sub.version)
            FROM App\Entity\Product sub
            WHERE sub.id = p.id
        )')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return int
     */
    public function countProductsWithMaxVersion(): int
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.version = (
            SELECT MAX(sub.version)
            FROM App\Entity\Product sub
            WHERE sub.id = p.id
        )');

        return (int) $query->getQuery()->getSingleScalarResult();
    }

}
