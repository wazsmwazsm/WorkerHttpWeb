<?php
namespace Framework\Http;
use Workerman\Protocols\Http;
use Workerman\Protocols\HttpCache;
use Framework\Config;

/**
 * HTTP response.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
Class Response {

    /**
     * create http response.
     *
     * @param  int  $code
     * @param  string  $info
     * @return string
     */
    public static function abort($code, $info = NULL) {
        // if param $info not set or not debug
        if(($msg = $info) === NULL || ! Config::get('app.debug')) {
            $msg = array_key_exists($code, HttpCache::$codes) ? HttpCache::$codes[$code] : "there's something wrong";
        }
        // set http response header
        Http::header("HTTP/1.1 ".$code." ".$msg);
        $html = '<html><head><title>'.$code.' '.$msg.'</title></head><body><center><h3>'.$msg.'</h3></center></body></html>';
        return $html;
    }

}
