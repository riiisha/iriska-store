<?php

declare(strict_types=1);

namespace App\DTO\Notification;

use App\Enum\MessageType;
use Symfony\Component\Validator\Constraints as Assert;

final class RegistrationNotificationSmsDTO implements NotificationDTOInterface
{
    public MessageType $type;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 15)]
        public string $userPhone,
        #[Assert\NotBlank]
        #[Assert\Regex(
            pattern: '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            message: 'promoId must be a valid UUID v4'
        )]
        public string $promoId,
    ) {
        $this->type = MessageType::SMS;
    }
}
