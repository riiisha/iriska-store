<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class UserRegisterDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank(message: "Номер телефона не может быть пустым.")]
        #[Assert\Length(max: 16, maxMessage: "Номер телефона слишком длинный.")]
        #[Assert\Type('string')]
        public string $phone,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[PasswordStrength(minScore: PasswordStrength::STRENGTH_WEAK)]
        public string $password,
    ) {
    }
}
