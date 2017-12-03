<?php
require_once 'PDODQLTest.php';
use Framework\DB\Drivers\Pgsql;

class PgsqlDQLTest extends PDODQLTest
{
    public static function setUpBeforeClass()
    {
        // 新建 pdo 对象, 用于测试被测驱动
        $dsn = 'pgsql:dbname=homestead;host=localhost;port=5432';
        self::$pdo = new PDO($dsn, 'homestead', 'secret');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        // 被测对象
        self::$db = new Pgsql('localhost', '5432', 'homestead', 'secret', 'homestead', 'utf8');
    }

}
