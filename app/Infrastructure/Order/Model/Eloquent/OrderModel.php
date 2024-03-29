<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Model\Eloquent;

use Database\Factories\Order\OrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderModel extends Model
{
    use HasUlids, HasFactory;
    
    protected $table = 'orders';

    protected $fillable = [
        'id',
        'store_id',
        'customer_id',
        'prevision_delivery_date',
        'status',
        'total_in_cents'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }

    protected static function newFactory(): Factory
    {
        return OrderFactory::new();
    }
}
