<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

final class UserEditDTO
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\Email]
        public string $email,
        #[Assert\Type('string')]
        public ?string $name,
        #[Assert\Type('string')]
        public ?string $phone,
        #[Assert\Type('string')]
        public ?string $password,
        #[Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'])]
        public ?string $role,
    ) {
    }
}
