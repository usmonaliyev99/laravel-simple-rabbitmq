<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnectionFactory;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Usmonaliyev\SimpleRabbit\MQ\Connection;
use Usmonaliyev\SimpleRabbit\MQ\ConnectionManager;
use Usmonaliyev\SimpleRabbit\MQ\MessageBuilder;
use Usmonaliyev\SimpleRabbit\SimpleMQ;
use function PHPUnit\Framework\assertInstanceOf;


// This is a helper function to create a fresh test environment for each test
function setupTest(array $configs = []) {
    // Reset Mockery
    Mockery::close();
    $defaultConfigs = require('config/simple-mq.php');
    $configs = array_merge($defaultConfigs, $configs);

    Config::shouldReceive('get')
        ->andReturnUsing(function ($key) use ($configs) {
            return data_get($configs, substr($key, 10));
        })
        ->byDefault();

    App::shouldReceive('make')->with(ConnectionManager::class)->andReturnUsing(fn() => new ConnectionManager());

    $test = new stdClass();
    $test->channel = Mockery::mock(AMQPChannel::class);
    $test->channel->shouldReceive('basic_publish')->withAnyArgs()->andReturn();

    $test->connection = Mockery::mock(AMQPStreamConnection::class);
    $test->connection->shouldReceive('channel')->andReturn($test->channel);
    $test->connection->shouldReceive('close')->andReturn();

    $test->connectionFactory = Mockery::mock('alias:'. AMQPConnectionFactory::class)
        ->shouldReceive('create')
        ->andReturn($test->connection);

    $test->simpleMq = new SimpleMQ();
    return $test;
}

beforeEach(function () {
    // This is intentionally left empty as we'll use the setupTest function in each test
});

afterEach(function () {
    Mockery::close();
});

test('connection method with name parameter returns the specified connection', function () {
    $test = setupTest();
    /** @var Connection $connection */
    $connection = $test->simpleMq->connection('default');
    assertInstanceOf(Connection::class, $connection);;
});

test('can send message to queue', function () {
    $test = setupTest();
    /** @var MessageBuilder $queue */
    $queue = $test->simpleMq->queue('default');
    $queue->setBody(['name' => 'First Foo'])
        ->handler('create-foo')
        ->publish();

    $test->channel->shouldHaveReceived('basic_publish')->withArgs([new \Mockery\Matcher\Any(), '', 'default'])->once();
});

test('can send message to exchange', function () {
    $test = setupTest();
    /** @var MessageBuilder $exchange */
    $exchange = $test->simpleMq->exchange('default');
    $exchange->setBody(['name' => 'First Foo'])
        ->setRoutingKey('foo.create')
        ->handler('create-foo')
        ->publish();

    $test->channel->shouldHaveReceived('basic_publish')->withArgs([new \Mockery\Matcher\Any(), 'default', 'foo.create'])->once();
});