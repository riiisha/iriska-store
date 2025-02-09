<?php

declare(strict_types=1);

namespace App\DTO\Notification;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderItemDTO
{
    /**
     * @param string $name
     * @param int $cost
     * @param string|null $additionalInfo
     */
    public function __construct(
        #[Assert\Type('string')]
        public string  $name,
        #[Assert\Type('integer')]
        public int     $cost,
        #[Assert\Type('string')]
        public ?string $additionalInfo = null,
    ) {
    }
}
