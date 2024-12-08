<?php

declare(strict_types=1);

namespace App\DTO\Product\Response;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductsListDTO
{
    public function __construct(
        #[Assert\All([
            new Assert\Type(ProductItemDTO::class),
        ])]
        public int   $total,
        public int   $page,
        public int   $limit,
        public array $products,
    ) {
    }
}
