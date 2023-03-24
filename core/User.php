<?php

namespace core;

class User {
    public $id;
    public $socket;

    public function __construct($id, $socketObj) {
        $this->id = $id;
        $this->socket = $socketObj;
    }
}
