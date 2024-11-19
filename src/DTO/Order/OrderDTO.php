<?php

declare(strict_types=1);

namespace App\DTO\Order;

use App\DTO\Address\AddressDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderDTO
{
    public function __construct(

        /*TODO - https://symfony.com/doc/current/reference/constraints/Compound.html*/
        #[Assert\NotBlank(message: "Номер телефона не может быть пустым.")]
        #[Assert\Length(max: 16, maxMessage: "Номер телефона слишком длинный.")]
        #[Assert\Type('string')]
        public string $phone,

        #[Assert\NotBlank(message: "Способ доставки не может быть пустым.")]
        #[Assert\Choice(choices: ['courier', 'pickup'])]
        #[Assert\Type('string')]
        public string $deliveryMethod,

//        #[Assert\Count(
//            min: 1,
//            max: 20,
//            minMessage: 'Для оформления заказа необходим хотя бы один товар.',
//            maxMessage: 'Вы не можете заказать более 20 товаров.',
//        )]
//        #[Assert\All([
//            new Assert\Type(ProductDTO::class),
//        ])]
        #[Assert\NotBlank(message: "Список товаров не может быть пустым.")]
        public array $products,


        #[Assert\Valid]
        public ?AddressDTO $address,
    )
    {
    }
}
