<?php

namespace App\EventListener\User;

use App\DTO\Notification\RegistrationNotificationSmsDTO;
use App\Event\User\UserRegisterEvent;
use App\Service\NotificationService;
use App\Service\UuidService;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisterEventListener extends Event
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly UuidService         $uuidService
    ) {
    }

    public function onUserRegister(UserRegisterEvent $event): void
    {
        $user = $event->getUser();
        $notification = new RegistrationNotificationSmsDTO(
            $user->getPhone(),
            $this->uuidService->getUUID4()
        );

        $this->notificationService->sendSMS($notification);
    }
}
