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
        if(count($params) !== 2) {
            // not catch, trigger Fatal error
            throw new \LogicException("method $method accept 2 params!\n");
        }
        // create map tree, exp: $map_tree['/a/b']['get'] = 'controller@method'
        self::$map_tree[self::uriParse($params[0])][strtoupper($method)] = $params[1];
    }

    /**
     * Parse uri.
     *
     * @param  string  $uri
     * @return void
     */
    public static function uriParse($uri) {
        // make uri as /a/b/c mode
        $uri = $uri == '/' ? $uri : '/'.rtrim($uri, '/');
        $uri = preg_replace('/\/+/', '/', $uri);

        return $uri;
    }

    /**
     * dispatch route.
     *
     * @return String
     */
    public static function dispatch(Requests $request) {
        try {
            // get request param
            $uri = self::uriParse(parse_url(($request->server->REQUEST_URI))['path']);
            $method = $request->server->REQUEST_METHOD;
            // 查找路由规则是否存在
            if( ! isset(self::$map_tree[$uri][$method])) {
                throw new \LogicException("uri: $uri <==> method $method route rule is not set!\n");
            }
            // get callback info
            $callback = self::$map_tree[$uri][$method];

            // is class
            if(is_string($callback)) {
                // syntax check
                if( ! preg_match('/^[a-zA-Z_\\]\w+@[a-zA-Z_]\w+/', $callback)) {
                    throw new \LogicException("Please use ' controller@method ' define callback\n");
                }
                // get controller method info
                $controller = explode('@', $callback);
                list($class, $method) = [$controller[0], $controller[1]];
                // class methods exist ?
                if( ! class_exists($class) || ! method_exists($class, $method)) {
                    throw new \BadMethodCallException("Class@method: ".$callback." is not found!\n");
                }
                // call method
                return call_user_func([(new $class), $method], $request);
            }
            // is callback
            if(is_callable($callback)) {
                // call function
                return call_user_func($callback, $request);
            }

        } catch (\LogicException $e) {
            echo $e->getMessage();
            return Response::abort(404, "Route not found!");
        } catch (\BadMethodCallException $e) {
            echo $e->getMessage();
            return Response::abort(500, "Route error: ".$callback." is not found!");
        }
    }
}
