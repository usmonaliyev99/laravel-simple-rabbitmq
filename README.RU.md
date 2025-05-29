<div align="center">
  <h1>laravel-simple-rabbitmq</h1>
</div>

> _Documentation generated with the help of ChatGPT_

Пакет для упрощенного использования RabbitMQ с поддержкой нескольких подключений, легкой публикацией сообщений и режимом
потребителя.

### Основные функции

- **Несколько подключений**: Легкое управление несколькими подключениями RabbitMQ в одном приложении.

- **Поддержка Exchange**: Вы можете отправлять сообщения в exchange.

- **Публикация сообщений**: Легко публикуйте сообщения в очереди и exchange с помощью удобного, цепочечного синтаксиса.

- **Режим потребителя**: Позволяет потребителям получать и обрабатывать сообщения из очередей в режиме реального
  времени.

- **Управление очередями и exchange в конфигурационном файле**: Вы можете зарегистрировать очереди и exchange в
  `config/simple-mq.php` и
  легко определить их с помощью команды `amqp:define-queues`.

<div align="center">
  <h2>Установка</h2>
</div>

Вы можете установить пакет через Composer:

```bash
composer require usmonaliyev/laravel-simple-rabbitmq
```

На следующем этапе необходимо опубликовать конфигурационные файлы и файлы регистрации действий:

```bash
php artisan vendor:publish --provider="Usmonaliyev\SimpleRabbit\SimpleRabbitMQServiceProvider"
```

В результате выполнения этой команды у вас появится файл конфигурации `config/simple-mq.php` и файл регистрации
`routes/amqp-handlers.php`.

Файл конфигурации `config/simple-mq.php` содержит подключения к RabbitMQ с учетными данными, очередями, стандартным
подключением и
стандартной очередью.

Следующий этап — настройка файла `.env`.

```.dotenv
SIMPLE_MQ_CONNECTION=
SIMPLE_MQ_QUEUE=

SIMPLE_MQ_HOST=
SIMPLE_MQ_PORT=
SIMPLE_MQ_USERNAME=
SIMPLE_MQ_PASSWORD=
```

<div align="center">
  <h2>Использование</h2>
</div>

Пакет может публиковать и потреблять сообщения.

### Публикация

Вы можете опубликовать сообщение с использованием стандартного подключения и очереди:

```php
<?php

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Действия..
        
        SimpleMQ::queue('foo-queue')
          ->setBody(['name' => 'Первый Foo'])
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

Также функция `exchange` публикует сообщение в exchange RabbitMq:

```php
<?php

namespace App\Https\Controllers;

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Действия..
        
        SimpleMQ::exchange('foo-exchange')
          ->setBody(['name' => 'Первый Foo'])
          ->setRoutingKey('foo.bar')
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

Если у вас несколько подключений к RabbitMq, вы можете опубликовать сообщение с использованием метода `connection`.

```php
<?php

namespace App\Https\Controllers;

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Действия..
        
        SimpleMQ::connection('foo-connection')
          ->queue('foo-queue')
          ->setBody(['name' => 'Первый Foo'])
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

### Потребление

Создайте папку `app/AMQP/Handlers` и создайте свои классы обработчиков.

Например:

```php
<?php

namespace App\AMQP\Handlers;

use Usmonaliyev\SimpleRabbit\MQ\Message;

class FooHandler
{
    public function handle(Message $message)
    {
        // Выполните что-то...
        
        $message->ack();
        
        return ['ok' => true];
    }
}
```

Не забудьте подтвердить сообщение в конце процесса, иначе потребитель не примет следующее сообщение.

Затем зарегистрируйте свой обработчик в файле `routes/amqp-handlers.php`.

```php
<?php

use \App\AMQP\Handlers\FooHandler;
use \Usmonaliyev\SimpleRabbit\Facades\ActionMQ;

ActionMQ::register('create-foo', [FooHandler::class, 'handle']);
```

Для потребления сообщений используйте:

```shell
php artisan amqp:consume connection? queue?
```

Команда требует два аргумента: `connection` и `queue`.Если вы их не укажете, команда использует стандартное подключение
и очередь.

<div align="center">
  <h2>Контракты</h2>
  <div>
    <a href="https://t.me/+P7PlyAdDQAJjM2Fi" target="_blank">
      У нас есть группа в Telegram, вы можете присоединиться к нам.
    </a>
  </div>
  <br/>
  <img width="30%" src="https://github.com/usmonaliyev99/usmonaliyev99/blob/main/assets/have-you-joined-us.gif?raw=true">
</div>

<div align="center">
  <h2>Планы</h2>
</div>

- [ ] Настройка exchange в `config/simple-mq.php`
- [ ] Написание файлов `README.UZ.md` и `README.RU.md`.
- [ ] Настройка тестирования.

<div align="center">
  <h2>Тестирование</h2>
</div>

```bash
composer test
```

<div align="center">
  <h2>Лицензия</h2>
</div>

Лицензия [MIT](LICENSE.md).
