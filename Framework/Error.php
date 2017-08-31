<?php
namespace Framework;
use Framework\Config;

/**
 * Error.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Error {

    /**
     * print error.
     *
     * @param  \Exception $e
     * @return void
     */
    public static function printError(\Exception $e) {
        echo $e->getMessage();
    }

    /**
     * return error html.
     *
     * @param  \Exception $e
     * @return String
     */
    public static function errorHtml(\Exception $e) {
        return Config::get('app.debug') ?
               '<html><head><title>'.$e->getMessage().'</title></head><body><center><h3>'.$e->getMessage().'</h3></center></body></html>' :
               '<html><head><title>Error</title></head><body><center><h3>something error...</h3></center></body></html>';
    }
}
