<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Model\Eloquent;

use Database\Factories\Order\OrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderModel extends Model
{
    use HasUuids, HasFactory;
    
    protected $table = 'orders';

    protected $fillable = [
        'id',
        'store_id',
        'customer_id',
        'prevision_delivery_date',
        'status',
        'total_in_cents',
    ];

    protected $casts = [
        'prevision_delivery_date' => 'datetime'
    ];

    protected $primaryKey = 'id';

    public function items(): HasMany
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }

    protected static function newFactory(): Factory
    {
        return OrderFactory::new();
    }
}
