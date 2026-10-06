<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\OrderStatusEnum;

class UpdateOrderStatusRequest
{
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [OrderStatusEnum::class, 'cases'], message: 'Недопустимый статус заказа.')]
    public OrderStatusEnum $status;
}
