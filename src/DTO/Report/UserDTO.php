<?php

declare(strict_types=1);

namespace App\DTO\Report;

use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    /**
     * @param int $id
     */
    public function __construct(
        #[Assert\NotBlank(message: "User ID cannot be empty.")]
        #[Assert\Type('integer')]
        public int $id
    ) {
    }
}
