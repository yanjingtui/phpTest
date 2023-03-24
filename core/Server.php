<?php
namespace core;

use core\Container;
use core\User;
use core\Event;

include("core/Event.php");
include("core/User.php");
include("core/Container.php");

class Server
{
    private $serverSocket;
    private $container;
    private $events;
    private $readSocks;

    public function __construct()
    {
        $this->serverSocket = stream_socket_server("tcp://0.0.0.0:4444", $errno, $errorMessage);

        if ($this->serverSocket === false) {
            die("Could not bind to socket: $errorMessage");
        }

        $this->container = new Container();
        $this->events = new Event();

        $this->events::listen('send', function($payload){
            $this->broadcastMessage($payload['user'], $payload['data']);
        });

        echo "Server started \n";
    }

    public function run()
    {
        while (true) {
            $this->readSocks = $this->container->getSockets();
            $this->readSocks[] = $this->serverSocket;

            if (!stream_select($this->readSocks, $write, $except, 300000)) {
                die('something went wrong while selecting');
            }

            if (in_array($this->serverSocket, $this->readSocks)) {
                $new_client = stream_socket_accept($this->serverSocket); 

                if ($new_client) {
                    echo 'Connection accepted from ' . stream_socket_get_name($new_client, true) . "\n";

                    $this->handleClient($new_client);

                    echo "Now there are total " . count($this->container->getUsers()) . " clients\n";
                }
                unset($this->readSocks[array_search($this->serverSocket, $this->readSocks)]);
            }

            foreach ($this->readSocks as $socks) {
                $data = fread($socks, 128);
                $user = $this->container->getUserBySocket($socks);
                echo "User " . $user->id . " have send a message: " . $data . "\n";
                $payload = array('user'=>$user,'data'=>$data);
                $this->events::trigger('send', $payload);
            }
        }
    }

    private function handleClient($clientSocket)
    {
        $user = new User(rand(1, 1000), $clientSocket);
        $this->container->addUser($user);
        fwrite($clientSocket, "Welcome User:". $user->id . "\n");
    }

    private function broadcastMessage($sender, $message)
    {
        $users = $this->container->getUsers();
        foreach ($users as $user) {
            if ($user->id != $sender->id) {
                fwrite($user->socket, $message);
            }
        }
    }

}
