<?php

declare(strict_types=1);

namespace App\DTO\Order;

use App\DTO\Address\AddressDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "Phone cannot be empty.")]
        #[Assert\Length(max: 15, maxMessage: "Phone is too long.")]
        #[Assert\Type('string')]
        public string $phone,
        #[Assert\NotBlank(message: "DeliveryMethod cannot be empty.")]
        #[Assert\Choice(choices: ['courier', 'selfdelivery'])]
        #[Assert\Type('string')]
        public string $deliveryMethod,
        #[Assert\NotBlank(message: "Products cannot be empty.")]
        public array $products,
        #[Assert\Valid]
        public ?AddressDTO $address,
    ) {
    }
}
