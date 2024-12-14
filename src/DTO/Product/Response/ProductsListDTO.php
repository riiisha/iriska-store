<?php

declare(strict_types=1);

namespace App\DTO\Product\Response;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductsListDTO
{
    /**
     * @param int $total
     * @param int $page
     * @param int $limit
     * @param ProductItemDTO[] $products
     */
    public function __construct(
        public int   $total,
        public int   $page,
        public int   $limit,
        #[Assert\All([
            new Assert\Type(ProductItemDTO::class),
        ])]
        public array $products,
    ) {
    }
}
