<?php

declare(strict_types=1);

namespace App\UserInterface\Controller\Order;

use App\Application\Order\CreateOrderApplication;
use App\Infrastructure\Laravel\Controller;

class OrderController extends Controller
{
    public function __construct(
        private CreateOrderApplication $createOrderApplication
    ) {

    }
}
