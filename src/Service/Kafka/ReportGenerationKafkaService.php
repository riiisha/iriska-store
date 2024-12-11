<?php

namespace App\Service\Kafka;

use SimPod\KafkaBundle\Kafka\Configuration;

class ReportGenerationKafkaService extends AbstractKafkaService
{
    private const TOPIC_NAME = 'report_generation';

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration, self::TOPIC_NAME);
    }

    public function send(string $reportId): void
    {
        $this->sendMessage(['reportId' => $reportId]);
    }
}
