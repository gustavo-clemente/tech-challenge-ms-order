<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Model\Eloquent;

use Illuminate\Database\Eloquent\Model;

class OrderItemModel extends Model
{
    protected string $table = 'order_items';

    protected array $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'price_in_cents',
        'discount_in_cents'
    ];
}
