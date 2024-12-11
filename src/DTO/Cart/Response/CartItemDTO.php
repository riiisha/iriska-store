<?php

declare(strict_types=1);

namespace App\DTO\Cart\Response;

final class CartItemDTO
{
    /**
     * @param string $productName
     * @param int $productCost
     * @param int $quantity
     */
    public function __construct(
        public string $productName,
        public int    $productCost,
        public int    $quantity,
    ) {
    }
}
