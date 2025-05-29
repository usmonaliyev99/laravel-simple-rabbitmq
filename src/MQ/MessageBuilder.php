<?php

namespace Usmonaliyev\SimpleRabbit\MQ;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class MessageBuilder
{
    /**
     * Connection name which is in ~/config/simple-mq.php
     */
    private string $connection;

    /**
     * The message will send to this queue or exchange
     */
    private string $to;

    /**
     * QUEUE or EXCHANGE
     */
    private string $type;

    /**
     * @var string routing key used in messages sent to exchanges
     */
    private string $routingKey = '';

    private array $body = [];

    // Properties array containing message properties with default values
    private array $properties = [
        'content_type' => 'application/json',
        'content_encoding' => 'utf-8',
        'application_headers' => [],
        'priority' => 0,
        'correlation_id' => null,
        'reply_to' => null,
        'timestamp' => null,
    ];

    public function __construct(string $connection, string $to, string $type)
    {
        $this->connection = $connection;
        $this->to = $to;
        $this->type = $type;

        $this->properties['timestamp'] = time();
    }

    /**
     * Creates a Publisher instance to handle specific actions.
     */
    public function handler(string $name): Publisher
    {
        $this->addHeader('handler', $name);

        $message = $this->getMessage();

        return new Publisher($message, $this->connection, $this->type, $this->to, $this->routingKey);
    }

    /**
     * Get the AMQPMessage instance
     */
    private function getMessage(): AMQPMessage
    {
        $body = $this->getBodyAsJson();
        $properties = $this->toProperties();

        return new AMQPMessage($body, $properties);
    }

    /**
     * Set headers for the message
     */
    public function setHeaders(array $headers): self
    {
        $this->properties['application_headers'] = $headers;

        return $this;
    }

    /**
     * Add key value pair to existing headers
     */
    public function addHeader($key, $value): self
    {
        $this->properties['application_headers'][$key] = $value;

        return $this;
    }

    /**
     * Set the message body
     *
     * Stores the body data and invalidates the cached AMQPMessage.
     */
    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the body as a JSON string
     */
    protected function getBodyAsJson(): string
    {
        return json_encode($this->body);
    }

    protected function toProperties(): array
    {
        $headers = $this->buildHeaders();
        $this->properties['application_headers'] = $headers;

        return $this->properties;
    }

    /**
     * Builds and returns message header
     */
    private function buildHeaders(): AMQPTable
    {
        return new AMQPTable($this->properties['application_headers']);
    }

    public function setRoutingKey(string $routingKey): self
    {
        $this->routingKey = $routingKey;

        return $this;
    }
}
