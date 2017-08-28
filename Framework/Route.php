<?php
namespace Framework;

class Route {

    public static $map_tree = [];

    public static function __callstatic($method, $params) {
        $uri = $params[0];
        $callback = $params[1];
        self::$map_tree[$uri][strtoupper($method)] = $callback;
    }

    public static function dispatch() {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $callback = isset(self::$map_tree[$uri][$method]) ?
                    self::$map_tree[$uri][$method] : null;

        if( ! $callback) {
            return 'null';
        }

        if(is_string($callback) && class_exists($callback)) {
            return 'is_class';
        }

        call_user_func($callback);

    }
}
