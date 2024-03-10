<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Model\Eloquent;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderModel extends Model
{
    use HasUlids;
    
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
    
}
