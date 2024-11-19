<?php

declare(strict_types=1);

namespace App\DTO\Cart\Response;

use Symfony\Component\Validator\Constraints as Assert;

final class CartItemDTO
{
    public function __construct(
        public string $productName,
        public int    $productCost,
        public int    $quantity,
    )
    {
    }
}
