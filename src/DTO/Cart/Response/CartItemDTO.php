<?php

declare(strict_types=1);

namespace App\DTO\Cart\Response;

final class CartItemDTO
{
    public function __construct(
        public string $productName,
        public int    $productCost,
        public int    $quantity,
    ) {
    }
}
