<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateProductDTO
{
    /**
     * @param int $id
     * @param int $version
     * @param string $name
     * @param MeasurementDTO $measurements
     * @param string $description
     * @param int $cost
     * @param int $tax
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $id,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $version,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Valid]
        public MeasurementDTO $measurements,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $description,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $cost,
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $tax,
    ) {
    }

}
