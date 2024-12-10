<?php

namespace App\Service\Kafka;

use App\DTO\Notification\NotificationDTOInterface;
use SimPod\KafkaBundle\Kafka\Configuration;

class NotificationKafkaService extends AbstractKafkaService
{
    private const TOPIC_NAME = 'notification';

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration, self::TOPIC_NAME);
    }

    public function send(NotificationDTOInterface $notification): void
    {
        $this->sendMessage($notification);
    }
}
