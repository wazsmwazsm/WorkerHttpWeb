<?php
namespace Framework;
use Framework\Config;
use Framework\Http\Response;
/**
 * Error.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Error {

    /**
     * response html.
     *
     * @var String
     */
    private static $_html_blade = '<html><head><title>{{title}}</title><style>'.
        'body{width:35em;margin:0 auto;font-family:Tahoma,Verdana,Arial,sans-serif}</style>'.
        '</head><body><center><h1>{{httpcode}}</h1><h3>{{exception}}</h3></center></body></html>';

    /**
     * print error.
     *
     * @param  \Exception $e
     * @return void
     */
    public static function printError(\Exception $e) {
        echo $e->getMessage()."\n";
    }

    /**
     * return error html.
     *
     * @param  \Exception $e
     * @param  int $code
     * @return String
     */
    public static function errorHtml(\Exception $e, $code) {

        $pattern = [
            '/\{\{title\}\}/',
            '/\{\{httpcode\}\}/',
            '/\{\{exception\}\}/',
        ];

        $title = $httpcode = 'HTTP '.$code.' '.Response::getHttpStatus($code);
        $exception = Config::get('app.debug') ? $e->getMessage() : 'something error...';
        $replacement = [$title, $httpcode, $exception];

        return preg_replace($pattern, $replacement, self::$_html_blade);
    }
}
