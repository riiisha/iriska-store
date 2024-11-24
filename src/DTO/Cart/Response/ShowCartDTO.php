<?php

declare(strict_types=1);

namespace App\DTO\Cart\Response;

use Symfony\Component\Validator\Constraints as Assert;


final class ShowCartDTO
{
    public function __construct(

        #[Assert\All([
            new Assert\Type(CartItemDTO::class),
        ])]
        public array $cartItems,
        public int   $totalCost
    )
    {
    }
}