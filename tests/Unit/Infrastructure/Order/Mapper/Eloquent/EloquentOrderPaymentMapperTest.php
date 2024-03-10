<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Order\Mapper\Eloquent;

use App\Domain\Order\ValueObject\OrderPaymentDetails;
use App\Domain\Payment\Enum\PaymentStatus;
use App\Infrastructure\Order\Mapper\Eloquent\EloquentOrderPaymentMapper;
use App\Infrastructure\Order\Model\Eloquent\OrderPaymentModel;
use Tests\TestCase;

class EloquentOrderPaymentMapperTest extends TestCase
{
    /**
     * @dataProvider provide_payment_status
     */
    public function test_map_model_to_domain_return_correct_order_details(string $status): void
    {
            $orderPaymentModel = $this->createOrderPaymentModelMock(
                orderId: uniqid(),
                externalPaymentOrderId: uniqid(),
                status: $status
            );

            $orderPaymentDetails = app(EloquentOrderPaymentMapper::class)->mapToDomain($orderPaymentModel);

            $this->assertInstanceOf(OrderPaymentDetails::class, $orderPaymentDetails);
            $this->assertEquals(
                $orderPaymentModel->external_payment_id,
                $orderPaymentDetails->getExternalPaymentId()->getIdentifier()
            );
            $this->assertEquals($orderPaymentModel->status, $orderPaymentDetails->getPaymentStatus()->value);
    }

    public function provide_payment_status(): array
    {
        $testCases = [];

        foreach(PaymentStatus::cases() as $case){
            $testCases[$case->value] = [$case->value];
        }

        return $testCases;
    }

    private function createOrderPaymentModelMock(
        string $orderId,
        string $externalPaymentOrderId,
        string $status
    ): OrderPaymentModel {
        /** @var OrderPaymentModel */
        $orderPaymentModel = $this
                              ->getMockBuilder(OrderPaymentModel::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        
        $orderPaymentModel
          ->method('__get')
          ->willReturnCallback(function ($property) use(
            $orderId, $externalPaymentOrderId, $status
        ){
            return match($property){
                'order_id' => $orderId,
                'external_payment_id' => $externalPaymentOrderId,
                'status' => $status
            };
        });

        return $orderPaymentModel;
    }
}
