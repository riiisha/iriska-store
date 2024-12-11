<?php

declare(strict_types=1);

namespace App\DTO\Cart\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateCartDTO
{
    /**
     * @param int $productId
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        #[Assert\Type('integer')]
        public int $productId
    ) {
    }
}
