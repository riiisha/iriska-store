<?php

namespace App\Service\Report;

use App\DTO\Report\ReportDetailDTO;
use App\DTO\Report\ReportDTO;
use App\DTO\Report\ReportResponseDTO;
use App\DTO\Report\UserDTO;
use App\Repository\Order\OrderItemRepository;
use App\Service\Kafka\ReportReadyKafkaService;
use Throwable;

readonly class ReportGenerateService
{
    public function __construct(
        private ReportReadyKafkaService $kafkaService,
        private OrderItemRepository     $orderItemRepository
    )
    {
    }

    public function generateReport(string $reportId): void
    {
        try {
            $soldOrderItems = $this->orderItemRepository->findAll();
            $report = '';
            foreach ($soldOrderItems as $soldOrderItem) {
                $user = new UserDTO($soldOrderItem->getOrder()->getOwner()->getId());
                $reportDTO = new ReportDTO(
                    $soldOrderItem->getProduct()->getName(),
                    $soldOrderItem->getProduct()->getCost(),
                    $soldOrderItem->getQuantity(),
                    $user
                );
                $report .= json_encode($reportDTO) . PHP_EOL;
            }
            $dir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'reports';
            $filePath = $dir . DIRECTORY_SEPARATOR . $reportId . '.json';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            file_put_contents($filePath, $report);
            $reportResponse = new ReportResponseDTO($reportId, 'success');
        } catch (Throwable $exception) {
            $details = new ReportDetailDTO(
                $exception->getMessage(), $exception->getTraceAsString()
            );
            $reportResponse = new ReportResponseDTO($reportId, 'fail', $details);
        }
        $this->kafkaService->send($reportResponse);
    }
}