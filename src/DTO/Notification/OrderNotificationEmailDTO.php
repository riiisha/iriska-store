<?php

declare(strict_types=1);

namespace App\DTO\Notification;

use App\DTO\Address\DeliveryAddressDTO;
use App\Enum\DeliveryMethod;
use App\Enum\MessageType;
use App\Enum\NotificationType;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderNotificationEmailDTO implements NotificationDTOInterface
{
    public const NOTIFICATION_TYPES = [
        NotificationType::REQUIRES_PAYMENT->value,
        NotificationType::SUCCESS_PAYMENT->value,
        NotificationType::COMPLETED->value,
    ];
    public const DELIVERY_TYPES = [
        DeliveryMethod::SELF_DELIVERY->value,
        DeliveryMethod::COURIER->value,
    ];
    public MessageType $type;

    /**
     * @param string $userEmail
     * @param string $notificationType
     * @param string $orderNum
     * @param OrderItemDTO[]  $orderItems
     * @param string $deliveryType
     * @param DeliveryAddressDTO|null $deliveryAddress
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $userEmail,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: self::NOTIFICATION_TYPES)]
        #[Assert\Type('string')]
        public string $notificationType,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $orderNum,
        #[Assert\NotBlank]
        #[Assert\All([new Assert\Type(OrderItemDTO::class)])]
        public array $orderItems,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: self::DELIVERY_TYPES)]
        #[Assert\Type('string')]
        public string $deliveryType,
        #[Assert\Valid]
        public ?DeliveryAddressDTO $deliveryAddress,
    ) {
        $this->type = MessageType::EMAIL;
    }
}
