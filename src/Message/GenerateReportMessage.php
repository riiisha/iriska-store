<?php

namespace App\Message;

readonly class GenerateReportMessage
{
    public function __construct(private string $reportId)
    {
    }

    public function getReportId(): string
    {
        return $this->reportId;
    }
}
