<?php

namespace Usmonaliyev\SimpleRabbit;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use Usmonaliyev\SimpleRabbit\MQ\ConnectionManager;

class ConsumeMQ
{
    /**
     * Connection name for RabbitMQ connection
     */
    private ?string $connection = null;

    /**
     * Target queue for listening
     */
    private ?string $queue = null;

    private AbstractConnection $amqpConnection;

    private AMQPChannel $channel;

    public function connection(?string $name = null): self
    {
        $this->connection = $name;

        return $this;
    }

    public function queue(?string $name = null): self
    {
        $this->queue = $name;

        return $this;
    }

    /**
     * Start listening RabbitMq server
     *
     * @throws Exception
     */
    public function listen(callable $callback): void
    {
        $this->buildConnection();
        $this->setupQos();

        $queue = $this->getQueue();

        $this->channel->basic_consume($queue, '', false, false, false, false, $callback);

        echo "Consumer is listening to \"$queue\" queue...".PHP_EOL;

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->amqpConnection->close();
    }

    /**
     * Setup QOS for this channel to read message one by one
     */
    protected function setupQos($qos = [null, 1, null]): void
    {
        $this->channel->basic_qos(...$qos);
    }

    /**
     * Building AMQPStreamConnection to rabbit mq server
     *
     * @throws Exception
     */
    protected function buildConnection(): void
    {
        $manager = App::make(ConnectionManager::class);

        $this->amqpConnection = $manager
            ->connection($this->getConnectionName())
            ->getAMQPConnection();

        $this->channel = $this->amqpConnection->channel();
    }

    /**
     * Getting queue name if it is null, default queue name
     */
    public function getQueue(): string
    {
        return $this->queue ??= Config::get('simple-mq.queue');
    }

    /**
     * Getting connection name if it is null, default connection name
     */
    public function getConnectionName(): string
    {
        return $this->connection ??= Config::get('simple-mq.connection');
    }
}
