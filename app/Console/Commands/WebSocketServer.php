<?php

namespace App\Console\Commands;

use App\Models\ChatServer;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server as Reactor;
use React\EventLoop\Factory;

class WebSocketServer extends Command
{
    protected $signature = 'websocket:server';
    protected $description = 'Start the WebSocket server';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $loop = Factory::create();
        $chatServer = new ChatServer();
        $socket = new Reactor('0.0.0.0:8090', $loop);
        new IoServer(
            new HttpServer(new WsServer($chatServer)),
            $socket
        );
        $loop->run();
    }
}
