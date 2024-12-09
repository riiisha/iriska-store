<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\User\UserLoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    public function __construct(
        readonly private UserLoginService $userLoginService
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function loginAction(#[CurrentUser] ?User $user): JsonResponse
    {
        $token = $this->userLoginService->login($user);

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
