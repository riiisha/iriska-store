<?php

namespace App\Controller\Api\Admin;

use App\DTO\User\UserEditDTO;
use App\DTO\User\UserUpdatePasswordDTO;
use App\Repository\UserRepository;
use App\Service\User\UserUpdateService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/admin/user')]
#[AsController]
class AdminUserController extends AbstractController
{
    private Serializer $serializer;

    public function __construct(private readonly UserUpdateService $userUpdateService)
    {
        $this->serializer = (new SerializerBuilder())->build();
    }

    #[Route(path: '/list', name: 'api_admin_user_list', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], orderBy: ['id' => "ASC"]);
        $context =  (new SerializationContext())->setGroups('list');

        return new JsonResponse(
            $this->serializer->serialize($users, 'json', $context),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route(path: '/edit', name: 'api_admin_user_edit', methods: ['PUT'])]
    public function editAction(#[MapRequestPayload] UserEditDTO $userEditDTO): Response
    {
        $this->userUpdateService->update($userEditDTO);
        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route(path: '/update-password', name: 'api_admin_user_update_password', methods: ['PATCH'])]
    public function updatePasswordAction(#[MapRequestPayload] UserUpdatePasswordDTO $userUpdatePasswordDTO): Response
    {
        $this->userUpdateService->updatePassword($userUpdatePasswordDTO);
        return new JsonResponse([], Response::HTTP_OK);
    }
}
