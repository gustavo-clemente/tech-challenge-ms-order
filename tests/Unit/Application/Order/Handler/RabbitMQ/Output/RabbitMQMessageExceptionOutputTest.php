<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Handler\RabbitMQ\Output;

use App\Application\Order\Handler\RabbitMQ\Output\RabbitMQMessageExceptionOutput;
use Tests\TestCase;

class RabbitMQMessageExceptionOutputTest extends TestCase
{
    public function test_get_response_returns_correct_response(): void
    {
        $expectedMessage = 'test Message';

        $handlerOutput = new RabbitMQMessageExceptionOutput($expectedMessage);
        $response = $handlerOutput->getResponse();
        
        $this->assertEquals($expectedMessage, $response['error']);
    }
}
