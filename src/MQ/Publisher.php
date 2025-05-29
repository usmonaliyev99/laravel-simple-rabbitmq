<?php

namespace Usmonaliyev\SimpleRabbit\MQ;

use Exception;
use Illuminate\Support\Facades\App;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    private AMQPMessage $message;

    /**
     * Connection name which is in ~/config/simple-mq.php
     */
    private string $connection;

    /**
     * The action type ("QUEUE" or "EXCHANGE").
     */
    private string $type;

    /**
     * Queue or exchange name
     */
    private string $to;

    /**
     * Routing key used on exchanges
     */
    private string $routingKey;

    public function __construct(AMQPMessage $message, string $connection, string $type, string $to, string $routingKey)
    {
        $this->message = $message;
        $this->connection = $connection;
        $this->type = $type;
        $this->to = $to;
        $this->routingKey = $routingKey;
    }

    /**
     * Publish the message
     */
    public function publish(): void
    {
        $this->{$this->type}();
    }

    /**
     * Handle publishing to a Queue.
     *
     * @throws Exception
     */
    private function QUEUE(): void
    {
        $connection = $this->getConnection();
        $channel = $connection->getChannel();

        $channel->basic_publish($this->message, '', $this->to);
    }

    /**
     * Handle publishing to an Exchange.
     *
     * @throws Exception
     */
    private function EXCHANGE(): void
    {
        $connection = $this->getConnection();
        $channel = $connection->getChannel();

        $channel->basic_publish($this->message, $this->to, $this->routingKey);
    }

    /**
     * Retrieve the active RabbitMQ connection.
     *
     * @throws Exception if connection cannot be established.
     */
    private function getConnection(): Connection
    {
        $manager = App::make(ConnectionManager::class);

        return $manager->connection($this->connection);

    }
}
