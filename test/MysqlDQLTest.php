<?php
require_once 'PDODQLTest.php';
use Framework\DB\Drivers\Mysql;

class MysqlDQLTest extends PDODQLTest
{
    public static function setUpBeforeClass()
    {
        // 新建 pdo 对象, 用于测试被测驱动
        $dsn = 'mysql:dbname=test;host=localhost;port=3306';
        $options = [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => FALSE,
            PDO::ATTR_EMULATE_PREPARES => FALSE,
        ];
        self::$pdo = new PDO($dsn, 'homestead', 'secret', $options);
        self::$pdo->prepare('set names utf8')->execute();
        // 被测对象
        self::$db = new Mysql('localhost', '3306', 'homestead', 'secret', 'test', 'utf8');
    }

}
