<?php

declare(strict_types=1);

namespace App\DTO\Report;

use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "User ID cannot be empty.")]
        #[Assert\Type('integer')]
        public int $id
    ) {
    }
}
