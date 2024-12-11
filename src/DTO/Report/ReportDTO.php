<?php

declare(strict_types=1);

namespace App\DTO\Report;

use Symfony\Component\Validator\Constraints as Assert;

class ReportDTO
{
    public function __construct(
    #[Assert\NotBlank(message: "ProductName cannot be empty.")]
    #[Assert\Type('string')]
    public string $productName,

    #[Assert\NotBlank(message: "Price cannot be empty.")]
    #[Assert\Type('integer')]
    public int $price,

    #[Assert\NotBlank(message: "Amount cannot be empty.")]
    #[Assert\Type('integer')]
    public int $amount,

    #[Assert\NotBlank(message: "User cannot be empty.")]
    public UserDTO $user
    ){
    }
}
