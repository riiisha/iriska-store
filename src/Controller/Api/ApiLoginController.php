<?php

namespace App\Controller\Api;

use App\Entity\User;
use DateInterval;
use DateTime;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    private string $publicApiToken;

    public function __construct(string $publicApiToken)
    {
        $this->publicApiToken = $publicApiToken;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function loginAction(#[CurrentUser] ?User $user): JsonResponse
    {
        $expires = (new DateTime('now'))->add(new DateInterval('PT12H'))->format('U');

        $token = JWT::encode(
            [
                'exp' => $expires,
                'userId' => $user->getId(),
                'userIdentifier' => $user->getUserIdentifier()
            ],
            $this->publicApiToken,
            'HS256'
        );

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
