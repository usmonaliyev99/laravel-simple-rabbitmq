<div align="center">
  <h1>laravel-simple-rabbitmq</h1>
</div>

> _Documentation generated with the help of ChatGPT_

RabbitMQ dan foydalanishni soddalashtiruvchi paket, ko‘p connectionlarni qo‘llab-quvvatlash, oson xabar yuborish va
consumer rejimiga ega.

### Asosiy xususiyatlar

- **Ko'p ulanishlar**: Bitta dastur ichida bir nechta RabbitMQ connectionlarni oson boshqarish imkoniyati.

- **Exchange qo‘llab-quvvatlashi**: Xabarlarni exchange ga yuborishingiz mumkin.

- **Xabar yuborish**: queue va exchange ga xabarlarni zanjirli sintaksis yordamida oson yuborish.

- **Consumer mode**: Consumer navbatlardan real vaqtda xabarlarni olish va qayta ishlash imkoniyatini beradi.

- **Queue va exchangelarni konfiguratsiya faylida boshqarish**: `config/simple-mq.php` faylida queue va exchange ni
  ro‘yxatdan o‘tkazib, `amqp:define-queues` buyrug‘i yordamida oson e'lon qilish.

<div align="center">
  <h2>O'rnatish</h2>
</div>

Paketni Composer orqali o'rnatishingiz mumkin:

```bash
composer require usmonaliyev/laravel-simple-rabbitmq
```

Keyingi qadamda konfiguratsiya va kerakli fayllarini nashr qilish kerak:

```bash
php artisan vendor:publish --provider="Usmonaliyev\SimpleRabbit\SimpleRabbitMQServiceProvider"
```

Ushbu buyruq natijasida sizda `config/simple-mq.php` konfiguratsiya fayli va `routes/amqp-handlers.php` registratsiya
fayli paydo bo'ladi.

`config/simple-mq.php` konfiguratsiya faylida RabbitMQ ulanishlari, ularnish login va parollari, queue, default
connection va default queue belgilash mumkin.

Keyingi bosqich `.env` faylini sozlash.

```.dotenv
SIMPLE_MQ_CONNECTION=
SIMPLE_MQ_QUEUE=

SIMPLE_MQ_HOST=
SIMPLE_MQ_PORT=
SIMPLE_MQ_USERNAME=
SIMPLE_MQ_PASSWORD=
```

<div align="center">
  <h2>Foydalanish</h2>
</div>

Paket xabarlarni yuborish va consume qilish imkoniyatiga ega.

### Xabar yuborish

Default connection va queue dan foydalanib xabar yuborishingiz uchun:

```php
<?php

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Biror narsa..
        
        SimpleMQ::queue('foo-queue')
          ->setBody(['name' => 'Birinchi Foo'])
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

Bundan tashqari, `exchange` funksiyasi xabarni RabbitMq exchange ga yuboradi:

```php
<?php

namespace App\Https\Controllers;

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Biror narsa..
        
        SimpleMQ::exchange('foo-exchange')
          ->setBody(['name' => 'Birinchi Foo'])
          ->setRoutingKey('foo.bar')
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

Agar sizda RabbitMq bilan ko‘p ulanish mavjud bo‘lsa, `connection` funksiyasidan foydalanishingiz mumkin.

```php
<?php

namespace App\Https\Controllers;

use Illuminate\Http\Request;
use Usmonaliyev\SimpleRabbit\Facades\SimpleMQ;

class FooController
{
    public function createFoo(Request $request)
    {
        // Biror narsa..
        
        SimpleMQ::connection('foo-connection')
          ->queue('foo-queue')
          ->setBody(['name' => 'Birinchi Foo'])
          ->handler('create-foo')
          ->publish();
          
        return response()->json(['message' => 'OK']);
    }
}
```

### Consuming

`app/AMQP/Handlers` papkasini yarating va o‘z handler classingizni yarating.

Masalan:

```php
<?php

namespace App\AMQP\Handlers;

use Usmonaliyev\SimpleRabbit\MQ\Message;

class FooHandler
{
    public function handle(Message $message)
    {
        // Biror narsa...
        
        $message->ack();
        
        return ['ok' => true];
    }
}
```

Jarayon oxirida xabarni tasdiqlashni unutmang (Acknowledge => $message->ack()), aks holda consumer keyingi xabarni qabul
qilmaydi.

Keyin o'z handleringizni `routes/amqp-handlers.php` fayliga ro'yxatdan o'tkazing.

```php
<?php

use \App\AMQP\Handlers\FooHandler;
use \Usmonaliyev\SimpleRabbit\Facades\ActionMQ;

ActionMQ::register('create-foo', [FooHandler::class, 'handle']);
```

Xabarlarni consume qilish uchun foydalaning:

```shell
php artisan amqp:consume connection? queue?
```

Bu buyruq ikkita argumentni talab qiladi: `connection` va `queue`.
Agar siz ularni bermasangiz, buyruq default connection va queueni ishlatadi.

<div align="center">
  <h2>Shartnomalar</h2>
  <div>
    <a href="https://t.me/+P7PlyAdDQAJjM2Fi" target="_blank">
      Bizda Telegram guruhi bor, siz qo‘shilishingiz mumkin.
    </a>
  </div>
  <br/>
  <img width="30%" src="https://github.com/usmonaliyev99/usmonaliyev99/blob/main/assets/have-you-joined-us.gif?raw=true">
</div>

<div align="center">
  <h2>Rejalar</h2>
</div>

- [ ] `config/simple-mq.php` faylida exchange sozlash
- [ ] `README.UZ.md` va `README.RU.md` fayllarini yozish.
- [ ] Testlarni sozlash.

<div align="center">
  <h2>Test qilish</h2>
</div>

```bash
composer test
```

<div align="center">
  <h2>Litsenziya</h2>
</div>

[MIT](LICENSE.md) Litsenziya.
