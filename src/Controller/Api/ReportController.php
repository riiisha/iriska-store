<?php

namespace App\Controller\Api;

use App\Service\Report\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
#[AsController]
class ReportController extends AbstractController
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    #[Route('/generate-report', name: 'generate_report')]
    public function generateReport(): JsonResponse
    {
        $reportId = $this->reportService->startReportGeneration();

        return new JsonResponse(['reportId' => $reportId]);
    }

}
