<?php

declare(strict_types= 1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Payment\Enum\PaymentStatus;
use App\Domain\Payment\ValueObject\ExternalPaymentId;

class OrderPaymentDetails
{
    public function __construct(
        private ExternalPaymentId $externalPaymentId,
        private PaymentStatus $paymentStatus
    ){
    }
}
