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
            switch ($e->getCode()) {
                case 404:
                    $header = 'HTTP/1.1 404 Not Found';
                    break;

                default:
                    $header = 'HTTP/1.1 500 Internal Server Error';
                    Error::printError($e); // if Server error, echo to stdout
                    break;
            }

            Response::header($header);
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
