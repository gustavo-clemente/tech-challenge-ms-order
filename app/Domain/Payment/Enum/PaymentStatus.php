<?php

declare(strict_types=1);

namespace App\Domain\Payment\Enum;

enum PaymentStatus: string
{
    case PENDING = "PENDING";
    case PROCESSING = "PROCESSING";
    case SUCCESS = "SUCCESS";
    case FAILED = "FAILED";
    case EXPIRED = "EXPIRED";
}
