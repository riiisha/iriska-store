<?php

namespace App\Controller\Api\Admin;

use App\DTO\Order\OrderUpdateStatusDTO;
use App\Service\Order\OrderUpdateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/admin/order')]
#[AsController]
class AdminOrderController extends AbstractController
{
    public function __construct(private readonly OrderUpdateService $orderUpdateService)
    {
    }

    #[Route(path: '/update-status', name: 'api_admin_order_update_status', methods: ['PATCH'])]
    public function updateOrderStatus(#[MapRequestPayload] OrderUpdateStatusDTO $orderUpdateStatusDTO): Response
    {
        $this->orderUpdateService->updateStatus($orderUpdateStatusDTO);
        return new JsonResponse([], Response::HTTP_OK);
    }
}
