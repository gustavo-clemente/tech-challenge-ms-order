<?php

use App\UserInterface\Order\Controller\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource("order", OrderController::class)->only(['store', 'show', 'destroy']);

Route::patch('/order/{id}/checkout', [OrderController::class, 'checkout']);

Route::patch('/order/{id}/finish', [OrderController::class, 'finish']);
