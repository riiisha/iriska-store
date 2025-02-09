<?php

declare(strict_types=1);

namespace App\DTO\Notification;

use App\Enum\MessageType;
use Symfony\Component\Validator\Constraints as Assert;

final class RegistrationNotificationEmailDTO implements NotificationDTOInterface
{
    public MessageType $type;

    /**
     * @param string $userEmail
     * @param string $promoId
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $userEmail,
        #[Assert\NotBlank]
        #[Assert\Regex(
            pattern: '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            message: 'promoId must be a valid UUID v4'
        )]
        public string $promoId,
    ) {
        $this->type = MessageType::EMAIL;
    }
}
