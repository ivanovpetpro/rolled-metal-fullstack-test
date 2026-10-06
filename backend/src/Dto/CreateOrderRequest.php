<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $dealerId;

    /**
     * @var CreateOrderItemRequest[]
     */
    #[Assert\All([
        new Assert\Type(type: CreateOrderItemRequest::class),
    ])]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    public array $items;
}
