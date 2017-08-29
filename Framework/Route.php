<?php
namespace Framework;
use Workerman\Protocols\Http;
use Framework\Http\Requests;
use Framework\Http\Response;

/**
 * HTTP router.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Route {
    /**
     * The route map.
     *
     * @var Array
     */
    public static $map_tree = [];

    /**
     * call method (http methods).
     *
     * @param  string  $method
     * @param  mixed  $params
     * @return void
     */
    public static function __callstatic($method, $params) {
        $uri = $params[0];
        $callback = $params[1];
        self::$map_tree[$uri][strtoupper($method)] = $callback;
    }

    /**
     * dispatch route.
     *
     * @return String
     */
    public static function dispatch() {
        $request = new Requests();
        // get request param
        $uri = parse_url(($request->server->REQUEST_URI))['path'];
        $method = $request->server->REQUEST_METHOD;
        $callback = isset(self::$map_tree[$uri][$method]) ?
                    self::$map_tree[$uri][$method] : null;
        // is class
        if(is_string($callback)) {
            $controller = explode('@', $callback);
            list($class, $method) = [$controller[0], $controller[1]];
            // class methods exist ?
            if(class_exists($class) && method_exists($class, $method)) {
                return call_user_func([(new $class), $method], $request);
            } else {
                return Response::abort(500, "Route error: ".$callback.' is not found!');
            }
        }
        // is callback
        if(is_callable($callback)) {
            return call_user_func($callback, $request);
        }
        // call error callback
        return Response::abort(404, "Route not found!");
    }
}
