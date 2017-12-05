<?php
require_once 'PDODQLTest.php';
use Framework\DB\Drivers\Pgsql;

class PgsqlDQLTest extends PDODQLTest
{
    public static function setUpBeforeClass()
    {
        // 新建 pdo 对象, 用于测试被测驱动
        $dsn = 'pgsql:dbname=test;host=localhost;port=5432';
        $options = [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];
        self::$pdo = new PDO($dsn, 'homestead', 'secret', $options);
        self::$pdo->prepare("set names 'utf8'")->execute();
        // 被测对象
        $config = [
          'host'     => 'localhost',
          'port'     => '5432',
          'user'     => 'homestead',
          'password' => 'secret',
          'dbname'   => 'test',
          'charset'  => 'utf8',
          'schema'   => 'public',
        ];
        self::$db = new Pgsql($config);
    }

}
