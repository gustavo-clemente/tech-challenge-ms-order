<?php

namespace App\UserInterface\Console\Commands;

use App\Application\Shared\Handler\RabbitMQMessageHandler;
use Illuminate\Console\Command;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumer extends Command
{
    protected $signature = 'rabbitmq:consume {queue}';

    protected $description = 'Command description';

    private string $queue;

    public function __construct(
        private AbstractConnection $connection
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->queue = $this->argument('queue');

        $handler = $this->getHandler();

        $channel = $this->createChannel();

        $this->declareQueue($channel);
        $this->listenForMessages($channel, $handler);

        $this->cleanUp($channel);
     
    }

    private function getHandler(): RabbitMQMessageHandler
    {
        $handler = config('rabbitmq-handlers.' . $this->queue);

        if (!class_exists($handler)) {
            throw new \RuntimeException("Handler not found for queue: $this->queue");
        }

        return app($handler);
    }

    private function createChannel(): AMQPChannel
    {
        $channel = $this->connection->channel();

        return $channel;
    }

    private function declareQueue(AMQPChannel $channel): void
    {
        $channel->queue_declare($this->queue, false, true, false, false);
    }

    private function listenForMessages(AMQPChannel $channel, RabbitMQMessageHandler $handler): void
    {
        $this->line(" [*] Waiting for messages on '{$this->queue}'. To exit press CTRL+C\n");

        $channel->basic_consume($this->queue, '', false, true, false, false, function(AMQPMessage $message) use($handler) {
            $response = $handler->handler($message);
            $this->line(json_encode($response->getResponse()));
        });

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
        }
    }

    private function cleanUp(AMQPChannel $channel): void
    {
        $channel->close();
        $this->connection->close();
    }
}
