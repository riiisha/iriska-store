<?php

namespace App\MessageHandler;

use App\Message\GenerateReportMessage;
use App\Service\Report\ReportGenerateService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GenerateReportMessageHandler
{
    public function __construct(private ReportGenerateService $reportGenerateService)
    {
    }

    public function __invoke(GenerateReportMessage $message): void
    {
        $this->reportGenerateService->generateReport($message->getReportId());
    }

}
