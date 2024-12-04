<?php

declare(strict_types=1);

namespace App\DTO\Order;

use App\DTO\Address\AddressDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "Номер телефона не может быть пустым.")]
        #[Assert\Length(max: 16, maxMessage: "Номер телефона слишком длинный.")]
        #[Assert\Type('string')]
        public string $phone,
        #[Assert\NotBlank(message: "Способ доставки не может быть пустым.")]
        #[Assert\Choice(choices: ['courier', 'selfdelivery'])]
        #[Assert\Type('string')]
        public string $deliveryMethod,
        #[Assert\NotBlank(message: "Список товаров не может быть пустым.")]
        public array $products,
        #[Assert\Valid]
        public ?AddressDTO $address,
    ) {
    }
}
