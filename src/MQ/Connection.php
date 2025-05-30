<?php

namespace Usmonaliyev\SimpleRabbit\MQ;

use Exception;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPConnectionFactory;
use Usmonaliyev\SimpleRabbit\Exceptions\ConnectionNotFoundException;

class Connection
{
    /**
     * Connection name which is in ~/config/simple-mq.php
     */
    private $name;

    private AbstractConnection $connection;

    /**
     * Initializes the RabbitMQ connection with the provided configuration.
     *
     * @throws Exception If required configuration is missing.
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        $config = Config::get("simple-mq.connections.$name");

        if (! isset($config)) {
            throw new ConnectionNotFoundException($name);
        }

        $connectionConfig = new AMQPConnectionConfig();
        $connectionConfig->setIoType($config['io_type']);
        $connectionConfig->setHost($config['host']);
        $connectionConfig->setPort($config['port']);
        $connectionConfig->setUser($config['username']);
        $connectionConfig->setPassword($config['password']);
        $connectionConfig->setVhost($config['vhost']);
        $connectionConfig->setInsist($config['insist']);
        $connectionConfig->setLoginMethod($config['login_method']);
        $connectionConfig->setLoginResponse($config['login_response'] ?? '');
        $connectionConfig->setLocale($config['locale']);
        $connectionConfig->setConnectionTimeout($config['connection_timeout']);
        $connectionConfig->setReadTimeout($config['read_write_timeout']);
        $connectionConfig->setStreamContext($config['context']);
        $connectionConfig->setKeepalive($config['keepalive']);
        $connectionConfig->setHeartbeat($config['heartbeat']);
        $connectionConfig->setChannelRPCTimeout($config['channel_rpc_timeout']);
        $connectionConfig->setIsSecure($config['is_secure'] ?? false);
        $connectionConfig->setIsLazy($config['is_lazy'] ?? false);
        $connectionConfig->setSslCaCert($config['ssl_ca_cert']);
        $connectionConfig->setSslCaPath($config['ssl_ca_path']);
        $connectionConfig->setSslCert($config['ssl_cert']);
        $connectionConfig->setSslKey($config['ssl_key']);
        $connectionConfig->setSslVerify($config['ssl_verify']);
        $connectionConfig->setSslVerifyName($config['ssl_verify_name']);
        $connectionConfig->setSslPassPhrase($config['ssl_pass_phrase']);
        $connectionConfig->setSslCiphers($config['ssl_ciphers']);
        $connectionConfig->setSslSecurityLevel($config['ssl_security_level']);
        $connectionConfig->setSslCryptoMethod($config['ssl_crypto_method']);

        $this->connection = AMQPConnectionFactory::create($connectionConfig);
    }

    /**
     * Get a publisher for the specified queue.
     */
    public function queue(?string $queueName = null): MessageBuilder
    {
        $queueName = $queueName ?? Config::get('simple-mq.queue');

        return new MessageBuilder($this->name, $queueName, 'QUEUE');
    }

    /**
     * Get a publisher for the specified exchange.
     */
    public function exchange($exchangeName): MessageBuilder
    {
        return new MessageBuilder($this->name, $exchangeName, 'EXCHANGE');
    }

    /**
     * Returns the active AbstractConnection connection.
     *
     * @throws Exception
     */
    public function getAMQPConnection(): AbstractConnection
    {
        return $this->connection;
    }

    /**
     * Retrieves an AMQP channel for the current connection.
     * If no channel exists, a new channel is created and returned.
     * Channels are used to interact with RabbitMQ, allowing you to
     * publish messages, consume messages, and perform other operations.
     *
     * @return AMQPChannel The channel associated with the current connection.
     *
     * @throws Exception If the channel cannot be created or the connection is invalid.
     */
    public function getChannel(): AMQPChannel
    {
        return $this->connection->channel();
    }

    /**
     * Definition section manager queues and exchange
     */
    public function getDefinition(): Definition
    {
        return new Definition($this->connection);
    }

    /**
     * Close channel and connection before destruction
     *
     * @throws Exception
     */
    public function __destruct()
    {
        $this->connection->close();
    }
}
