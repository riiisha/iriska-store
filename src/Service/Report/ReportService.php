<?php

namespace App\Service\Report;

use App\Service\Kafka\ReportGenerationKafkaService;
use App\Service\UuidService;

readonly class ReportService
{
    public function __construct(
        private UuidService $uuidService,
        private ReportGenerationKafkaService $kafkaService
    ) {
    }

    public function startReportGeneration(): string
    {
        $reportId = $this->uuidService->getUUID4();
        $this->kafkaService->send($reportId);

        return $reportId;
    }
}
