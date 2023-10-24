<?php

namespace App\Models;

use Exception;
use SplObjectStorage;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ChatServer implements MessageComponentInterface
{
    protected SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        $querystring = explode('?', $conn->httpRequest->getUri())[1];
        parse_str($querystring, $parameters);

        if (isset($parameters['sender_id'])) {
            $conn->sender_id = $parameters['sender_id'];
        }

        if (isset($parameters['receiver_id'])) {
            $conn->receiver_id = $parameters['receiver_id'];
        }

        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $messageData = json_decode($msg);
        if (empty($messageData->message)) return;

        $chatModel = new ChatsModel();
        $chatModel->fill([
            'sender_id' => $messageData->senderId,
            'receiver_id' => $messageData->receiverId,
            'message' => $messageData->message,
        ]);

        $chatModel->save();
        foreach ($this->clients as $client) {
            if ($from === $client || $client->receiver_id != $messageData->receiverId || $client->sender_id != $messageData->senderId) continue;
            $client->send(json_encode(['message' => $messageData->message, 'sender_id' => $client->sender_id]));
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
        echo "Connection $conn->resourceId has disconnected\n";
    }

    /**
     * @param ConnectionInterface $conn
     * @param Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, Exception $e): void
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
