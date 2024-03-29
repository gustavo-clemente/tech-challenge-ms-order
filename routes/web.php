<?php

use App\UserInterface\Order\Controller\OrderController;
use Illuminate\Support\Facades\Route;

Route::resource("order", OrderController::class)->only(['store', 'show', 'destroy']);

Route::patch('/order/{id}/checkout', [OrderController::class, 'checkout']);