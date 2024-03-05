<?php

declare(strict_types= 1);

namespace App\Domain\Order\Exception;

class OrderNotFoundException extends OrderException
{
    public function __construct(){
        parent::__construct("Order not found. Please verify if the order ID is correct.", 404);
    }
}
