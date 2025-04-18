<div align="center">
  <h1>laravel-simple-rabbitmq</h1>
</div>

<div align="center">
  <img src="https://img.shields.io/packagist/dependency-v/usmonaliyev/laravel-simple-rabbitmq/php">
  <img src="https://img.shields.io/packagist/dt/usmonaliyev/laravel-simple-rabbitmq.svg">
  <img src="https://img.shields.io/packagist/v/usmonaliyev/laravel-simple-rabbitmq.svg">
  <img src="https://img.shields.io/packagist/l/usmonaliyev/laravel-simple-rabbitmq">
</div>

The package for simplified RabbitMQ usage, supporting multiple connections, easy publishing, and consumer mode.

## Documentation

- [Русский (RU)](README.RU.md)
- [O'zbekcha (UZ)](README.UZ.md)

### Key Features

- **Multiple Connections**: Effortlessly manage multiple RabbitMQ connections within the same application.

- **Exchange supporting**: You can push messages to exchanges

- **Message Publishing**: Easily publish messages to queues and exchange with a fluent, chainable syntax.

- **Consumer Mode**: Enable consumers to receive and process messages from queues in real time.

- **Manage queues and exchanges in config file**: You can register queues and exchanges in `config/simple-mq.php` and
  define them in easy way which is `amqp:define-queues` command.

<div align="center">
  <h2>Installation</h2>
</div>

You can install the package via composer:

```bash
composer require usmonaliyev/laravel-simple-rabbitmq
```

Next step you must publish config and action register files:

```bash
php artisan vendor:publish --provider="Usmonaliyev\SimpleRabbit\SimpleRabbitMQServiceProvider"
```

As a result of this command, you will have a configuration file `config/simple-mq.php` and a registry file
`routes/amqp-handlers.php`.

The `config/simple-mq.php` config file contains RabbitMQ connections with credentials, queues, default connection and
default queue.

The next stage is configure `.env` file.

```.dotenv
SIMPLE_MQ_CONNECTION=
SIMPLE_MQ_QUEUE=

SIMPLE_MQ_HOST=
SIMPLE_MQ_PORT=
SIMPLE_MQ_USERNAME=
SIMPLE_MQ_PASSWORD=
```

<div align="center">
  <h2>Usage</h2>
</div>

The package can publish and consume messages

### Publishing

You can publish a message with default connection and default queue:

```php
<?php

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Something..
        
        SimpleMQ::queue('foo-queue')
          ->setBody(['name' => 'First Foo'])
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

Also, `exchange` function publish message to RabbitMq exchange:

```php
<?php

namespace App\Https\Controllers;

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Something..
        
        SimpleMQ::exchange('foo-exchange')
          ->setBody(['name' => 'First Foo'])
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

If you have multiply connection to RabbitMq, you can publish a message with `connection` method.

```php
<?php

namespace App\Https\Controllers;

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Something..
        
        SimpleMQ::connection('foo-connection')
          ->queue('foo-queue')
          ->setBody(['name' => 'First Foo'])
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

### Consuming

Create `app/AMQP/Handlers` folder and create your handler classes.

For example:

```php
<?php

namespace App\AMQP\Handlers;

use Usmonaliyev\SimpleRabbit\MQ\Message;

class FooHandler
{
    public function handle(Message $message)
    {
        // do something...
        
        $message->ack();
        
        return ['ok' => true];
    }
}
```

Don't forget acknowledge message end of process, else consumer does not accept next message.

Then register your handler in `routes/amqp-handlers.php` file.

```php
<?php

use \App\AMQP\Handlers\FooHandler;
use \Usmonaliyev\SimpleRabbit\Facades\ActionMQ;

ActionMQ::register('create-foo', [FooHandler::class, 'handle']);
```

To consume messages use:

```shell
php artisan amqp:consume connection? queue?
```

The command requires two arguments which are `connection` and `queue`.\
If you don't give them, command uses default connection and queue.

<div align="center">
  <h2>Contracts</h2>
  <div>
    <a href="https://t.me/+P7PlyAdDQAJjM2Fi" target="_blank">
      We have a telegram group, you can join us.
    </a>
  </div>
  <br/>
  <img width="30%" src="https://github.com/usmonaliyev99/usmonaliyev99/blob/main/assets/have-you-joined-us.gif?raw=true">
</div>

<div align="center">
  <h2>Plans</h2>
</div>

- [ ] Exchange configuration in `config/simple-mq.php`
- [ ] Setup testing.

<div align="center">
  <h2>Testing</h2>
</div>

```bash
composer test
```

<div align="center">
  <h2>License</h2>
</div>

The [MIT](LICENSE.md) License.
