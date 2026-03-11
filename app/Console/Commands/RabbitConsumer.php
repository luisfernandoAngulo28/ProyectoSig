<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\RabbitMQ;

class RabbitConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:consume';
    protected $description = 'Consume messages from RabbitMQ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $consumer = new RabbitMQ();
        $consumer->consumerMessage();
    }
}
