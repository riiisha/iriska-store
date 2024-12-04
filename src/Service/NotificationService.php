<?php

namespace App\Service;

use App\DTO\Notification\NotificationDTOInterface;
use Exception;
use Psr\Log\LoggerInterface;

readonly class NotificationService
{
    public function __construct(
        private KafkaService    $kafkaService,
        private LoggerInterface $logger
    ) {

    }

    public function sendSMS(NotificationDTOInterface $notification): void
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

    public function sendEmail(NotificationDTOInterface $notification): void
    {
        try {
            $this->kafkaService->send('notification', $notification);
            $this->logger->info('Email sent', ['notification' => json_encode($notification)]);
        } catch (Exception $e) {
            $this->logger->error('Email not sent', [
                'notification' => json_encode($notification),
                'error' => $e->getMessage()
            ]);
        }
    }
}
