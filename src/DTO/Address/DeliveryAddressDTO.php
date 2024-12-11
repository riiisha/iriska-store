<?php

declare(strict_types=1);

namespace App\DTO\Address;

use Symfony\Component\Validator\Constraints as Assert;

final class DeliveryAddressDTO
{
    /**
     * @param string $fullAddress
     * @param int|null $kladrId
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $fullAddress,
        #[Assert\Type('integer')]
        public ?int $kladrId = null,
    ) {
    }
}
