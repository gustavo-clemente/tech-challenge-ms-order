<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Model\Eloquent;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentModel extends Model
{
    use HasUuids;
    protected $table = 'order_payment';

    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'external_payment_id',
        'status'
    ];
}
