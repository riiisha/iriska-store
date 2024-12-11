<?php

namespace App\Consumer;

use App\Service\Report\ReportGenerateService;
use Psr\Log\LoggerInterface;
use RdKafka\Message;
use SimPod\KafkaBundle\Kafka\Configuration;

final class ReportConsumer extends AbstractConsumer
{
    public function __construct(
        Configuration           $configuration,
        LoggerInterface         $logger,
        readonly ReportGenerateService $reportGenerateService,
    ) {
        parent::__construct($configuration, $logger);
    }

    public function getName(): string
    {
        return 'report_consumer';
    }

    protected function getTopicName(): string
    {
        return 'report_generation';
    }

    protected function processMessage(Message $message): void
    {
        $reportId = json_decode($message->payload, true)['reportId'];
        $this->reportGenerateService->generateReport($reportId);
    }
}
