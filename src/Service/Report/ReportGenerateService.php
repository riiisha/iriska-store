<?php

namespace App\Service\Report;

use App\Service\Kafka\ReportReadyKafkaService;

readonly class ReportGenerateService
{
    public function __construct(
        private ReportReadyKafkaService $kafkaService
    ){
    }

    public function generateReport(string $reportId): void
    {

        $this->kafkaService->send();
    }
}