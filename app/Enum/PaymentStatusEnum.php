<?php

namespace App\Enum;

enum PaymentStatusEnum: int
{
    case PAID = 1;
    case PENDING = 0;
}
