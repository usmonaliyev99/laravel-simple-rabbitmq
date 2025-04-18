<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue and connection
    |--------------------------------------------------------------------------
    |
    | This option controls the default queue connection that gets used while
    | using this messaging system. You can set this to any of the defined
    | connections below.
    |
    */
    'queue' => env('SIMPLE_MQ_QUEUE', 'simple'),

    'connection' => env('SIMPLE_MQ_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the message broker connections for your
    | application. These connections can be reused across multiple queues.
    |
    */
    'connections' => [

        'default' => [
            'host' => env('SIMPLE_MQ_HOST', 'localhost'),
            'port' => env('SIMPLE_MQ_PORT', 5672),
            'username' => env('SIMPLE_MQ_USERNAME', 'guest'),
            'password' => env('SIMPLE_MQ_PASSWORD', 'guest'),
            'vhost' => env('SIMPLE_MQ_VHOST', '/'),
            'insist' => false,
            'login_method' => 'AMQPLAIN',
            'login_response' => null,
            'locale' => 'en_US',
            'connection_timeout' => 30,
            'read_write_timeout' => 30,
            'context' => null,
            'keepalive' => false,
            'heartbeat' => false,
            'channel_rpc_timeout' => 0,
            'ssl_protocol' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queues
    |--------------------------------------------------------------------------
    |
    | Each queue can be linked to a specific connection and configured with
    | additional queue-specific settings, such as queue name or routing keys.
    |
    */
    'queues' => [

        'simple' => [
            // Specifies the name of the connection to be used for this queue.
            'connection' => 'default',

            /**
             * Be carefully these arguments because you can define queue one time with these arguments.
             * After queue is created, you don't change these arguments
             *
             * If you must change arguments, delete queue and define them one more time.
             */
            'arguments' => [
                /**
                 * Sets the time-to-live for messages in this queue to 60000 milliseconds (60 seconds).
                 *
                 * 1 min = 60000
                 * 1 hour = 3600000
                 * 1 day = 3600000 * 24
                 */
                'x-message-ttl' => 3600000,
                // Limits the maximum number of messages in the queue to 1000.
                'x-max-length' => 1000,
                // Specifies the dead-letter exchange to which messages will be sent if they expire.
                'x-dead-letter-exchange' => null,
                // Routing key for messages that are routed to the dead-letter exchange.
                'x-dead-letter-routing-key' => null,
            ],
        ],
    ],

];
