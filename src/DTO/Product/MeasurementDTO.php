<?php

namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;

final class MeasurementDTO
{
    /**
     * @param int $weight
     * @param int $height
     * @param int $width
     * @param int $length
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $weight,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $height,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $width,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $length
    ) {
    }
}
