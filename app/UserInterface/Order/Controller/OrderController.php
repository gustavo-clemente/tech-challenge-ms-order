<?php

declare(strict_types=1);

namespace App\UserInterface\Order\Controller;

use App\Application\Order\CancelOrderApplication;
use App\Application\Order\CheckoutOrderApplication;
use App\Application\Order\CreateOrderApplication;
use App\Application\Order\FinishOrderApplication;
use App\Application\Order\GetOrderByIdApplication;
use App\Application\Order\Input\CreateOrderInput;
use App\Application\Order\Input\OrderIdInput;
use App\Infrastructure\Laravel\Controller;
use App\UserInterface\Order\Request\CreateOrderRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private CreateOrderApplication $createOrderApplication,
        private GetOrderByIdApplication $getOrderByIdApplication,
        private CheckoutOrderApplication $checkoutOrderApplication,
        private CancelOrderApplication $cancelOrderApplication,
        private FinishOrderApplication $finishOrderApplication
    ) {
        
    }

    public function store(CreateOrderRequest $createOrderRequest): JsonResponse
    {
        $createOrderInput = new CreateOrderInput($createOrderRequest->toArray());

        $output = $this->createOrderApplication->createOrder($createOrderInput);

        return new JsonResponse($output->jsonSerialize(), Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $orderidInput = new OrderIdInput($id);

        $output = $this->getOrderByIdApplication->getOrderById($orderidInput);

        return new JsonResponse($output->jsonSerialize(), Response::HTTP_OK);
    }

    public function checkout(string $id): JsonResponse
    {
        $orderidInput = new OrderIdInput($id);

        $output = $this->checkoutOrderApplication->checkoutOrder($orderidInput);

        return new JsonResponse($output->jsonSerialize(), Response::HTTP_OK);
    }

    public function destroy(string $id): JsonResponse
    {
        $orderidInput = new OrderIdInput($id);

        $output = $this->cancelOrderApplication->cancelOrder($orderidInput);

        return new JsonResponse($output->jsonSerialize(), Response::HTTP_OK);
    }

    public function finish(string $id): JsonResponse
    {
        $orderidInput = new OrderIdInput($id);

        $output = $this->finishOrderApplication->finishOrder($orderidInput);

        return new JsonResponse($output->jsonSerialize(), Response::HTTP_OK);
    }
}
