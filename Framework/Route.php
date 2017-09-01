<?php
namespace Framework;
use Framework\Http\Requests;

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
    private static $_map_tree = [];

    /**
     * call method (http methods).
     *
     * @param  string  $method
     * @param  mixed  $params
     * @return void
     */
    public static function __callstatic($method, $params) {
        if(count($params) !== 2) {
            // not catch, trigger Fatal error
            throw new \LogicException("method $method accept 2 params!");
        }
        // create map tree, exp: $_map_tree['/a/b']['get'] = 'controller@method'
        self::$_map_tree[self::_uriParse($params[0])][strtoupper($method)] = $params[1];
    }

    /**
     * Parse uri.
     *
     * @param  string  $uri
     * @return void
     */
    private static function _uriParse($uri) {
        // make uri as /a/b/c mode
        $uri = ($uri == '/') ? $uri : '/'.rtrim($uri, '/');
        $uri = preg_replace('/\/+/', '/', $uri);

        return $uri;
    }

    /**
     * dispatch route.
     *
     * @return mixed
     */
    public static function dispatch(Requests $request) {
        // get request param
        $uri = self::_uriParse(parse_url(($request->server->REQUEST_URI))['path']);
        $method = $request->server->REQUEST_METHOD;
        // 查找路由规则是否存在
        if( ! isset(self::$_map_tree[$uri][$method])) {
            throw new \LogicException("route rule uri: $uri <==> method : $method is not set!");
        }
        // get callback info
        $callback = self::$_map_tree[$uri][$method];

        // is class
        if(is_string($callback)) {
            // syntax check
            if( ! preg_match('/^[a-zA-Z_\\]\w+@[a-zA-Z_]\w+/', $callback)) {
                throw new \LogicException("Please use ' controller@method ' define callback");
            }
            // get controller method info
            $controller = explode('@', $callback);
            list($class, $method) = [$controller[0], $controller[1]];
            // class methods exist ?
            if( ! class_exists($class) || ! method_exists($class, $method)) {
                throw new \BadMethodCallException("Class@method: $callback is not found!");
            }
            // call method
            return call_user_func([(new $class), $method], $request);
        }
        // is callback
        if(is_callable($callback)) {
            // call function
            return call_user_func($callback, $request);
        }
    }
}
