<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductFilter
{
    /**
     * @param int $page
     * @param int $limit
     * @param string|null $name
     * @param int|null $minCost
     * @param int|null $maxCost
     */
    public function __construct(
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int     $page,
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int     $limit,
        #[Assert\Type('string')]
        public ?string $name = null,
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public ?int    $minCost = null,
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public ?int    $maxCost = null,
    ) {
    }
}
