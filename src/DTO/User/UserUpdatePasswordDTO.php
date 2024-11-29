<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class UserUpdatePasswordDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public int    $userId,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 6)]
        public string $newPassword,
    ) {
    }
}
