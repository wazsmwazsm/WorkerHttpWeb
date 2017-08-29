<?php
namespace Framework\Http;

/**
 * HTTP requests.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
Class Requests {

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
     * session info.
     *
     * @var Array
     */
    public $session;
    /**
     * upload file info.
     *
     * @var Array
     */
    public $files;

    /**
     * get http request param.
     */
    public function __construct() {
        $this->requset = (object) $_REQUEST;
        $this->server  = (object) $_SERVER;
        $this->cookie  = (object) $_COOKIE;
        $this->session = (object) $_SESSION;
        $this->files   = (object) $_FILES;
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
