<?php

declare(strict_types= 1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Payment\Enum\PaymentStatus;
use App\Domain\Payment\ValueObject\ExternalPaymentId;

class OrderPaymentDetails implements \JsonSerializable
{
    public function __construct(
        private ExternalPaymentId $externalPaymentId,
        private PaymentStatus $paymentStatus
    ){
    }

    public function getExternalPaymentId(): ExternalPaymentId
    {
        return $this->externalPaymentId;
    }

    public function getPaymentStatus(): PaymentStatus
    {
        return $this->paymentStatus;
    }

    public function jsonSerialize(): array
    {
        return [
            'externalPaymentId' => $this->externalPaymentId->getIdentifier(),
            'paymentStatus' => $this->paymentStatus->value
        ];
    }
}
