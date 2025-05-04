<?php

namespace Usmonaliyev\SimpleRabbit\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Usmonaliyev\SimpleRabbit\ActionMQ;
use Usmonaliyev\SimpleRabbit\Facades\ConsumeMQ;

class ConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amqp:consume {queue?} {connection?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume a queue';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $queue = $this->argument('queue');
        $connection = $this->argument('connection');

        $action = App::make(ActionMQ::class);
        $action->load();

        ConsumeMQ::connection($connection)
            ->queue($queue)
            ->listen($action->consume(...));
    }
}
