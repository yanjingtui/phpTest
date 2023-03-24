<?php
namespace core;

class Container {
    private $users = array();

    public function addUser($user) {
        $this->users[$user->id] = $user;
    }

    public function removeUser($user) {
        unset($this->users[$user->id]);
    }

    public function getUsers() {
        return $this->users;
    }

    public function getSockets() {
        $sockets = array();
        foreach ($this->users as $user) {
            if ($user->socket ) {
                $sockets[] = $user->socket;
            }
        }
        return $sockets;
    }

    public function getUserBySocket($socket) {
        foreach ($this->users as $user) {
            if ($user->socket == $socket) {
                return $user;
            }
        }
        return false;
    }
}


