<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Mapper\Eloquent;

use App\Domain\Order\ValueObject\OrderPaymentDetails;
use App\Domain\Payment\Enum\PaymentStatus;
use App\Domain\Payment\ValueObject\ExternalPaymentId;
use App\Infrastructure\Order\Model\Eloquent\OrderPaymentModel;

class EloquentOrderPaymentMapper
{
    public function mapToDomain(OrderPaymentModel $orderPaymentModel): OrderPaymentDetails
    {
        return new OrderPaymentDetails(
            externalPaymentId: new ExternalPaymentId($orderPaymentModel->external_payment_id),
            paymentStatus: PaymentStatus::from($orderPaymentModel->status)
        );
    }
}
