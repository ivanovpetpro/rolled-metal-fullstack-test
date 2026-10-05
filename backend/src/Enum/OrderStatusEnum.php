<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case DRAFT = 'draft';
    case PROCESSING = 'processing';
    case CONFIRMED = 'confirmed';
    case SHIPPED = 'shipped';
    case CANCELLED = 'cancelled';
}
