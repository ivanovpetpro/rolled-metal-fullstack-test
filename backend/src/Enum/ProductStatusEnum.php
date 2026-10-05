<?php

namespace App\Enum;

enum ProductStatusEnum: string
{
    case ACTIVE = 'active';
    case ARCHIVE = 'archive';
    case OUT_OF_STOCK = 'out_of_stock';
}
