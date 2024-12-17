<?php

namespace App\Service\Product;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\ProductFilter;
use App\DTO\Product\Response\ProductItemDTO;
use App\DTO\Product\Response\ProductsListDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository
    ) {
    }

    public function getList(ProductFilter $filter): ProductsListDTO
    {
        $products = $this->productRepository->findProductsWithLatestVersion($filter);
        $countProducts = $this->productRepository->countProductsWithMaxVersion();

        foreach ($products as $product) {
            $productItems[] = new ProductItemDTO(
                $product->getName(),
                $product->getDescription(),
                $product->getCost(),
                $product->getMeasurements(),
            );
        }

        return new ProductsListDTO(
            $countProducts,
            $filter->page,
            $filter->limit,
            $productItems ?? []
        );
    }

    public function createOrUpdateProduct(CreateProductDTO $createProductDTO): Product
    {
        $product = $this->productRepository->findByIdentifiers($createProductDTO->id, $createProductDTO->version);
        if (!$product) {
            $product = new Product($createProductDTO);
        } else {
            $product->update($createProductDTO);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }
}
