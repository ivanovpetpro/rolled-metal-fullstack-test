<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderItemRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $productId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $quantity;
}
