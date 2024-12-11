<?php

declare(strict_types=1);

namespace App\DTO\Product\Response;

final class ProductItemDTO
{
    /**
     * @param string $name
     * @param string $description
     * @param int $cost
     * @param array $measurements
     */
    public function __construct(
        public string $name,
        public string $description,
        public int    $cost,
        public array  $measurements,
    ) {
    }
}
