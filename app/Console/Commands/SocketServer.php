<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use React\Socket\ConnectionInterface;

class SocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:socket-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $socket = new \React\Socket\SocketServer('127.0.0.1:9080');
        $socket->on('connection', function (ConnectionInterface $connection) {
            $connection->write("Hello " . $connection->getRemoteAddress() . "!\n");
            $connection->write("Welcome to this amazing server!\n");
            $connection->write("Here's a tip: don't say anything.\n");

            $connection->on('data', function ($data) use ($connection) {
                var_dump($data);
                $connection->close();
            });
        });
    }
}
