<?php

declare(strict_types=1);

namespace App\DTO\Report;

use Symfony\Component\Validator\Constraints as Assert;

class ReportDetailDTO
{
    /**
     * @param string $error
     * @param string|null $message
     */
    public function __construct(
        #[Assert\NotBlank(message: "Error cannot be empty.")]
        #[Assert\Type('string')]
        public string $error,
        #[Assert\Type('string')]
        public ?string $message = null
    ) {
    }
}
