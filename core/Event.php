<?php
namespace core;

class Event {
    private static $events = [];

    public static function listen($name, $callback) {
        self::$events[$name][] = $callback;
    }

    public static function trigger($name, $payload = null) {
        foreach (self::$events[$name] as $event => $callback) {
            call_user_func($callback, $payload);
        }
    }
}