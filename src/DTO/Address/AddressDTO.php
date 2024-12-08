<?php

declare(strict_types=1);

namespace App\DTO\Address;

use Symfony\Component\Validator\Constraints as Assert;

final class AddressDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $city,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $street,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $house,
        #[Assert\Type('string')]
        public ?string $corpus = null,
    ) {
    }
}
