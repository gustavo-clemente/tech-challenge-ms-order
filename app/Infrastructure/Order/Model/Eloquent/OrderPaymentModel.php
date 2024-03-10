<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Model\Eloquent;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentModel extends Model
{
    protected $table = 'order_payment';

    protected $fillable = [
        'order_id',
        'external_payment_id',
        'status'
    ];
}
