<?php
require_once 'PDODQLTest.php';
use Framework\DB\Drivers\Mysql;

class MysqlDQLTest extends PDODQLTest
{
    public static function setUpBeforeClass()
    {
        // 新建 pdo 对象, 用于测试被测驱动
        $dsn = 'mysql:dbname=homestead;host=localhost;port=3360';
        self::$pdo = new PDO($dsn, 'homestead', 'secret', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8']);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        // 被测对象
        self::$db = new Mysql('localhost', '3306', 'homestead', 'secret', 'homestead', 'utf8');
    }

}
