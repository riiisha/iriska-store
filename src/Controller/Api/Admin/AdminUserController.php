<?php

namespace App\Controller\Api\Admin;

use App\DTO\User\UserEditDTO;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/api/admin/user')]
#[AsController]
class AdminUserController extends AbstractController
{
    public function __construct(private readonly UserManager $manager)
    {
    }

    #[Route(path: '/edit', name: 'api_admin_user_edit', methods: ['POST'])]
    public function editAction(
        #[MapRequestPayload] UserEditDTO $userEditDTO
    ): Response
    {
        try {
            /*TODO - заменить роль на админа (в секьюрити) и добавить метод смены пароля*/
            $this->manager->edit($userEditDTO);
            return new JsonResponse([], Response::HTTP_OK);
        }catch (Throwable $throwable){
            return new JsonResponse(['message' => $throwable->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
