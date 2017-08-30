<?php
namespace Framework;
use Workerman\Connection\TcpConnection;
use Framework\Http\Requests;
use Framework\Http\Response;
use Framework\Route;

/**
 * App.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class App {
    /**
     * run http app.
     *
     * @param  Workerman\Connection\TcpConnection  $con
     * @param  mixed  $data
     * @return void
     */
    public static function run(TcpConnection $con, $data) {

        $rst = Route::dispatch(new Requests($data));
        $con->send($rst);
    }
}
