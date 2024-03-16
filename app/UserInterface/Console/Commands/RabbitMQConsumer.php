<?php

namespace App\UserInterface\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AbstractConnection;

class RabbitMQConsumer extends Command
{
    protected $signature = 'rabbitmq:consume {queue}';

    protected $description = 'Command description';

    public function handle()
    {
        /** @var AbstractConnection */
        $connection = app(AbstractConnection::class);
        $queue = $this->argument('queue');
        $handler = config('rabbitmq-handlers.' . $queue);

        if (!class_exists($handler)) {
            $this->error("Handler not found for queue: $queue");
            return;
        }

        $handlerClass = app($handler);

        $channel = $connection->channel();

        // Declare a fila da qual vamos consumir mensagens
        $channel->queue_declare($queue, false, true, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo ' [x] Received ', $msg->getBody(), "\n";
            sleep(substr_count($msg->getBody(), '.'));
            echo " [x] Done\n";
        };

        // Registre o consumidor no canal para começar a receber mensagens
        $channel->basic_consume($queue, '', false, true, false, false, [$handlerClass, 'handler']);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }

        // Feche o canal e a conexão ao finalizar o consumo
        $channel->close();
        $connection->close();
    }
}
