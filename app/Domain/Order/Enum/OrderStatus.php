<?php

declare(strict_types=1);

namespace App\Domain\Order\Enum;

enum OrderStatus: string
{
    case CREATED = "CREATED";
    case IN_PREPARATION = 'IN_PREPARATION';
    case DELIVERED = 'DELIVERED';
    case CANCELLED = 'CANCELED';
    case RECEIVED = 'RECEIVED';
    case AWAITING_PAYMENT = 'AWAITING_PAYMENT';

    case PREPARATION_FINISHED = 'PREPARATION_FINISHED';

}
