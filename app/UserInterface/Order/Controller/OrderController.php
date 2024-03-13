<?php

declare(strict_types=1);

namespace App\UserInterface\Order\Controller;

use App\Application\Order\CreateOrderApplication;
use App\Application\Order\Input\CreateOrderInput;
use App\Infrastructure\Laravel\Controller;
use App\UserInterface\Order\Request\CreateOrderRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private CreateOrderApplication $createOrderApplication
    ) {
        
    }

    public function store(CreateOrderRequest $createOrderRequest): JsonResponse
    {
        $createOrderInput = new CreateOrderInput($createOrderRequest->toArray());

        $output = $this->createOrderApplication->createOrder($createOrderInput);

        return new JsonResponse($output->jsonSerialize(), Response::HTTP_CREATED);
    }
}
