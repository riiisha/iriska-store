<?php

declare(strict_types=1);

namespace App\DTO\Report;

use Symfony\Component\Validator\Constraints as Assert;

final class ReportResponseDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "Report ID cannot be empty.")]
        #[Assert\Type('string')]
        public string $reportId,
        #[Assert\NotBlank(message: "Result cannot be empty.")]
        #[Assert\Choice(choices: ['success', 'fail'], message: "Result must be either 'success' or 'fail'.")]
        public string $result,
        #[Assert\Valid]
        public ?ReportDetailDTO $detail = null
    ) {
    }
}
