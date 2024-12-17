<?php

namespace App\Service\Report;

use App\Message\GenerateReportMessage;
use App\Service\UuidService;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ReportService
{
    public function __construct(
        private UuidService $uuidService,
        private MessageBusInterface $messageBus
    ) {
    }

    public function startReportGeneration(): string
    {
        $reportId = $this->uuidService->getUUID4();
        $this->messageBus->dispatch(new GenerateReportMessage($reportId));

        return $reportId;
    }
}
