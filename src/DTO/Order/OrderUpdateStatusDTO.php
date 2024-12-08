<?php

declare(strict_types=1);

namespace App\DTO\Order;

use App\Enum\OrderStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderUpdateStatusDTO
{
    public const STATUES = [
        OrderStatus::PAID->value,
        OrderStatus::IN_ASSEMBLY->value,
        OrderStatus::READY_TO_PICKUP->value,
        OrderStatus::IN_DELIVERY->value,
        OrderStatus::CANCELED->value,
        OrderStatus::RECEIVED->value,
    ];

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public int    $orderId,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Choice(choices: self::STATUES)]
        public string $newStatus,
    ) {
    }
}
