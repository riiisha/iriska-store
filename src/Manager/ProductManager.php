<?php

namespace App\Manager;

use App\DTO\Product\CreateProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository
    ) {
    }

    public function createOrUpdateProduct(CreateProductDTO $createProductDTO): Product
    {
        $product = $this->productRepository->findByIdentifiers($createProductDTO->id, $createProductDTO->version);
        if (!$product) {
            $product = new Product();
            $product
                ->setVersion($createProductDTO->version)
                ->setId($createProductDTO->id);
        }
        $product
            ->setTax($createProductDTO->tax)
            ->setCost($createProductDTO->cost)
            ->setMeasurements((array)$createProductDTO->measurements)
            ->setName($createProductDTO->name)
            ->setDescription($createProductDTO->description);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }
}
