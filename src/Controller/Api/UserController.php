<?php

namespace App\Controller\Api;

use App\DTO\User\UserRegisterDTO;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/user')]
#[AsController]
class UserController extends AbstractController
{
    public function __construct(private readonly UserManager $manager)
    {
    }

    #[Route(path: '/register', name: 'api_user_register', methods: ['POST'])]
    public function registerAction(#[MapRequestPayload] UserRegisterDTO $userRegisterDTO): Response
    {
        $this->manager->register($userRegisterDTO);
        return new JsonResponse([], Response::HTTP_CREATED);
    }
}
