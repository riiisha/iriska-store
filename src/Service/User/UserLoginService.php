<?php

namespace App\Service\User;

use App\Entity\User;
use DateInterval;
use DateTime;
use Firebase\JWT\JWT;

readonly class UserLoginService
{
    public function __construct(
        private string $publicApiToken,
        private string $tokenLifetime,
    ) {
    }

    public function login(?User $user): string
    {
        $dateInterval = "PT". $this->tokenLifetime . "H";
        $expires = (new DateTime('now'))->add(new DateInterval($dateInterval))->format('U');

        return JWT::encode(
            [
                'exp' => $expires,
                'userId' => $user->getId(),
                'userIdentifier' => $user->getUserIdentifier()
            ],
            $this->publicApiToken,
            'HS256'
        );
    }
}
