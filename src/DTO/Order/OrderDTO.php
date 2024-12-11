<?php

declare(strict_types=1);

namespace App\DTO\Order;

use App\DTO\Address\AddressDTO;
use App\Enum\DeliveryMethod;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderDTO
{
    public const DELIVERY_TYPES = [
        DeliveryMethod::SELF_DELIVERY->value,
        DeliveryMethod::COURIER->value,
    ];

    /**
     * @param string $phone
     * @param string $deliveryMethod
     * @param ProductDTO[] $products
     * @param AddressDTO|null $address
     */
    public function __construct(
        #[Assert\NotBlank(message: "Phone cannot be empty.")]
        #[Assert\Length(max: 15, maxMessage: "Phone is too long.")]
        #[Assert\Type('string')]
        public string $phone,
        #[Assert\NotBlank(message: "DeliveryMethod cannot be empty.")]
        #[Assert\Choice(choices: self::DELIVERY_TYPES)]
        #[Assert\Type('string')]
        public string $deliveryMethod,
        #[Assert\NotBlank(message: "Products cannot be empty.")]
        #[Assert\All(
            new Assert\Collection(
                [
                'id' => [
                    new Assert\Type('integer'),
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(0),
                ],
                'quantity' => [
                    new Assert\Type('integer'),
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(0),
                ],

            ],
                allowExtraFields: false,
                allowMissingFields: false
            )
        )]
        public array $products,
        #[Assert\Valid]
        public ?AddressDTO $address,
    ) {
    }
}
