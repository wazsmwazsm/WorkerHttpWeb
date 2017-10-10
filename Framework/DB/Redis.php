<?php

namespace Framework\DB;
use Framework\Config;
use Framework\Error;
use Predis\Client;
/**
 * Redis.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Redis
{
    /**
     * clients.
     *
     * @var array
     */
    private static $_clients;

    /**
     * init redis clients.
     *
     * @return void
     */
    public static function init()
    {
        // get redis config
        $rd_confs = Config::get('database.redis');
        // create redis init params
        $cluster = $rd_confs['cluster'];
        $options = (array) $rd_confs['options'];
        $servers = $rd_confs['rd_con'];
        // get clients
        self::$_clients = $cluster ? self::createAggregateClient($servers, $options) :
                                     self::createSingleClients($servers, $options);
        // check redis connect
        foreach (self::$_clients as $con_name => $client) {
            try {
                $client->ping();
            } catch (\Exception $e) {
                $msg = "Redis connect fail, check your redis config for connection '$con_name' . \n".$e->getMessage();
                Error::printError($msg);
            }
        }
    }

    /**
     * Create a new aggregate client supporting sharding.
     *
     * @param  array  $servers
     * @param  array  $options
     * @return array
     */
    public static function createAggregateClient(array $servers, array $options = [])
    {
        return ['default' => new Client(array_values($servers), $options)];
    }

    /**
     * Create an array of single connection clients.
     *
     * @param  array  $servers
     * @param  array  $options
     * @return array
     */
    public static function createSingleClients(array $servers, array $options = [])
    {
        $clients = [];

        foreach ($servers as $key => $server) {
            $clients[$key] = new Client($server, $options);
        }

        return $clients;
    }

    /**
     * call method (predis methods).
     *
     * @param  string  $method
     * @param  mixed  $params
     * @return void
     */
    public static function __callstatic($method, $params)
    {
        return call_user_func_array([self::$_clients['default'], $method], $params);
    }

}
