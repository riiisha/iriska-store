<?php

namespace App\Service\Kafka;

use App\DTO\Report\ReportResponseDTO;
use SimPod\KafkaBundle\Kafka\Configuration;

class ReportReadyKafkaService extends AbstractKafkaService
{
    private const TOPIC_NAME = 'report_ready';

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration, self::TOPIC_NAME);
    }

    public function send(ReportResponseDTO $reportResponseDTO): void
    {
        $this->sendMessage($reportResponseDTO);
    }
}
