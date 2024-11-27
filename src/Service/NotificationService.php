<?php

namespace App\Service;

use App\DTO\Notification\RegistrationNotificationEmailDTO;
use App\DTO\Notification\RegistrationNotificationSmsDTO;
use Exception;
use Psr\Log\LoggerInterface;

readonly class NotificationService
{
    public function __construct(
        private KafkaService    $kafkaService,
        private LoggerInterface $logger
    ) {

    }

    public function sendSMS(RegistrationNotificationSmsDTO $notification): void
    {
        try {
            $this->kafkaService->send('notification', $notification);
            $this->logger->info('SMS sent', ['notification' => json_encode($notification)]);
        } catch (Exception $e) {
            $this->logger->error('SMS not sent', [
                'notification' => json_encode($notification),
                'error' => $e->getMessage()
            ]);
        }
    }
    /* TODO
        public function sendEmail(RegistrationNotificationEmailDTO $notification): void
        {
        }
    */

}
