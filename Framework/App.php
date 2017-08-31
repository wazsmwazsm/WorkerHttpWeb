<?php
namespace Framework;
use Workerman\Connection\TcpConnection;
use Framework\Http\Requests;
use Framework\Http\Response;
use Framework\Route;
use Framework\Error;

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
     */
    public static function run(TcpConnection $con, $data) {
      try {
          // dispatch route, return Response data
          $response = Response::bulid(Route::dispatch(new Requests($data)));

          $con->send($response);

      } catch (\LogicException $e) {
          Error::printError($e);
          Response::header("HTTP/1.1 404 Not Found");
          $con->close(Error::errorHtml($e));

      } catch (\BadMethodCallException $e) {
          Error::printError($e);
          Response::header("HTTP/1.1 500 Internal Server Error");
          $con->close(Error::errorHtml($e));

      } catch (\InvalidArgumentException $e) {
          Error::printError($e);
          Response::header("HTTP/1.1 500 Internal Server Error");
          $con->close(Error::errorHtml($e));
      }

    }
}
