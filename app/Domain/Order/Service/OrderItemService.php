<?php

declare(strict_types= 1);

namespace App\Domain\Order\Service;

use App\Domain\Order\Port\Repository\OrderItemRepository;

class OrderItemService
{
    public function __construct(
        private OrderItemRepository $orderItemRepository
    ){
    }

    
}
