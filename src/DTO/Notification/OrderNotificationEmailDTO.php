<?php

declare(strict_types=1);

namespace App\DTO\Notification;

use App\DTO\Address\DeliveryAddressDTO;
use App\Enum\MessageType;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderNotificationEmailDTO implements NotificationDTOInterface
{
    public MessageType $type;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $userEmail,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['requires_payment', 'success_payment', 'completed'])]
        #[Assert\Type('string')]
        public string $notificationType,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $orderNum,
        #[Assert\NotBlank]
        #[Assert\All([new Assert\Type(OrderItemDTO::class)])]
        public array $orderItems,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['courier', 'selfdelivery'])]
        #[Assert\Type('string')]
        public string $deliveryType,
        #[Assert\Valid]
        public ?DeliveryAddressDTO $deliveryAddress,
    ) {
        $this->type = MessageType::EMAIL;
    }
}
