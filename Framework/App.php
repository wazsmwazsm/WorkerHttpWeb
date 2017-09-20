<?php
namespace Framework;
use Workerman\Connection\TcpConnection;
use Framework\Http\Requests;
use Framework\Http\Response;
use Framework\Http\Route;
use Framework\Error;
use Framework\DB\DB;
/**
 * App.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class App
{
    /**
     * run http app.
     *
     * @param  Workerman\Connection\TcpConnection $con
     * @param  mixed  $data
     * @return void
     * @throws \LogicException
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public static function run(TcpConnection $con, $data)
    {
        try {
            // dispatch route, return Response data
            $response = Response::bulid(Route::dispatch(new Requests($data)));
            $con->send($response);

        } catch (\Exception $e) {
            // create http response header
            $eCode = $e->getCode() == 0 ? 500 : $e->getCode();
            $header = 'HTTP/1.1 '.$eCode.' '.Response::getHttpStatus($eCode);
            Response::header($header);

            // show error, if 404 only return http response 
            if($eCode == 500) Error::printError($e);
            $con->send(Error::errorHtml($e, $header));
        }

    }

    /**
     * init db connections.
     *
     * @return void
     */
    public static function dbInit()
    {
        // init database
        DB::init();
    }

}
