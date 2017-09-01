<?php
namespace Framework\Http;

/**
 * HTTP requests.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
Class Requests {
    /**
     * get param.
     *
     * @var Array
     */
    public $get;
    /**
     * post param.
     *
     * @var Array
     */
    public $post;
    /**
     * request param.
     *
     * @var Array
     */
    public $requset;
    /**
     * server info.
     *
     * @var Array
     */
    public $server;
    /**
     * cokkie info.
     *
     * @var Array
     */
    public $cookie;
    /**
     * upload file info.
     *
     * @var Array
     */
    public $files;

    /**
     * get http request param.
     *
     * @param Array $request
     */
    public function __construct(Array $request) {
        $this->get     = (object) $request['get'];
        $this->post    = (object) $request['post'];
        $this->requset = (object) array_merge($request['get'], $request['post']);
        $this->server  = (object) $request['server'];
        $this->cookie  = (object) $request['cookie'];
        $this->files   = (object) $request['files'];
    }

    /**
     * Get an input element from the request.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key) {
        if (array_key_exists($key, $this->requset)) {
            return $this->_requset[$key];
        }
        return NULL;
    }
}
