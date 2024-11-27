<?php

namespace App\Service;

use App\DTO\Notification\RegistrationNotificationEmailDTO;
use App\DTO\Notification\RegistrationNotificationSmsDTO;
use JsonException;

readonly class NotificationService
{
    public function __construct(
        private KafkaService $kafkaService
    ) {

    }

    public function sendSMS(RegistrationNotificationSmsDTO $notification): void
    {
        try {
            $this->kafkaService->send('notification', $notification);
        } catch (JsonException $e) {
            /*TODO - добавить логирование*/
        }
    }
    /* TODO
        public function sendEmail(RegistrationNotificationEmailDTO $notification): void
        {
        }
    */

}
