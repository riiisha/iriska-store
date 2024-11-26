<?php

namespace App\Controller\Api\Admin;

use App\DTO\User\UserEditDTO;
use App\Service\User\UserUpdateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/api/admin/user')]
#[AsController]
class AdminUserController extends AbstractController
{
    public function __construct(private readonly UserUpdateService $userUpdateService)
    {
    }

    #[Route(path: '/edit', name: 'api_admin_user_edit', methods: ['PUT'])]
    public function editAction(
        #[MapRequestPayload] UserEditDTO $userEditDTO
    ): Response {
        try {
            $this->userUpdateService->update($userEditDTO);
            return new JsonResponse([], Response::HTTP_OK);
        } catch (NotFoundHttpException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            return new JsonResponse(['message' => $throwable->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
