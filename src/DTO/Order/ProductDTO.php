<?php

declare(strict_types=1);

namespace App\DTO\Order;

use App\DTO\Cart\Response\CartItemDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductDTO
{
    /**
     * @param int $id
     * @param int $quantity
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $id,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $quantity,
    ) {
    }
}
