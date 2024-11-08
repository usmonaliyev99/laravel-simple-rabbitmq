<?php

namespace Usmonaliyev\SimpleRabbit\MQ;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

class Definition
{
    private Connection $connection;

    private AMQPChannel $channel;

    /**
     * Passive - set to true for checking if the queue exists
     */
    private bool $passive = false;

    /**
     * Durable - makes sure the queue survives server restarts
     */
    private bool $durable = true;

    /**
     * Exclusive - used by only one connection and will be deleted when that connection closes
     */
    private bool $exclusive = false;

    /**
     * If set to true, the server will not send a response, meaning you won’t receive confirmation of the queue declaration.
     */
    private bool $nowait = false;

    /**
     * Auto-delete - queue is deleted when last consumer unsubscribes
     */
    private bool $auto_delete = false;

    /**
     * Allows you to pass additional arguments to the queue, such as configuring time-to-live (TTL), dead-letter exchanges, and other advanced settings.
     */
    private array $arguments = [];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->channel = $connection->getChannel();
    }

    public function defineQueue(string $name): ?array
    {
        $argTable = $this->getArguments();

        return $this->channel->queue_declare(
            $name,
            $this->passive,
            $this->durable,
            $this->exclusive,
            $this->auto_delete,
            $this->nowait,
            $argTable,
        );
    }

    public function defineExchange(string $name, $type = 'direct'): ?array
    {
        throw new Exception('The function does not have implementation.');
    }

    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    protected function getArguments(): AMQPTable
    {
        return new AMQPTable($this->arguments);
    }
}