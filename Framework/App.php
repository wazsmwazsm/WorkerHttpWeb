<?php
namespace Framework;
use Workerman\Connection\TcpConnection;
use Framework\Http\Requests;
use Framework\Http\Response;
use Framework\Route;
use Framework\Error;
use Framework\DB\DB;
/**
 * App.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class App {
    /**
     * run http app.
     *
     * @param  Workerman\Connection\TcpConnection $con
     * @param  mixed  $data
     * @return void
     * @throws \LogicException
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public static function run(TcpConnection $con, $data) {
        try {
            // dispatch route, return Response data
            $response = Response::bulid(Route::dispatch(new Requests($data)));

            $con->send($response);

        } catch (\LogicException $e) {
            Error::printError($e);
            Response::header("HTTP/1.1 404 Not Found");
            $con->close(Error::errorHtml($e, 404));

        } catch (\BadMethodCallException $e) {
            Error::printError($e);
            Response::header("HTTP/1.1 500 Internal Server Error");
            $con->close(Error::errorHtml($e, 500));

        } catch (\InvalidArgumentException $e) {
            Error::printError($e);
            Response::header("HTTP/1.1 500 Internal Server Error");
            $con->close(Error::errorHtml($e, 500));
        }

    }

    /**
     * init db connections.
     *
     * @return void
     */
    public static function dbInit() {
        // init database
        DB::init();
    }

}
