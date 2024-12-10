<?php

namespace App\Service\Kafka;

use SimPod\KafkaBundle\Kafka\Configuration;

class ReportReadyKafkaService extends AbstractKafkaService
{
    private const TOPIC_NAME = 'report_ready';

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration, self::TOPIC_NAME);
    }

    public function send(): void
    {
        /*TODO */
        /*$this->sendMessage();*/
    }
}
