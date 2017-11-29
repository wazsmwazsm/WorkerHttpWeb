<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Framework\DB\Drivers\Mysql;

class MysqlTest extends TestCase
{
    use TestCaseTrait;

    protected static $db;

    protected static $pdo;

    public function getConnection()
    {
        // pdo 对象，用于测试被测对象和构建测试基境
        if (self::$pdo == null) {
            $dsn = 'mysql:dbname=homestead;host=localhost;port=3360';
            self::$pdo = new PDO($dsn, 'homestead', 'secret', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        }
        // 待测的 mysql 对象
        if (self::$db == null) {
            self::$db = new Mysql('localhost', '3306', 'homestead', 'secret', 'homestead', 'utf8');
        }

        return $this->createDefaultDBConnection(self::$pdo, ':memory:');
    }

    public function getDataSet()
    {
        return $this->createXMLDataSet(dirname(__FILE__).'/test.xml');
    }

    public function testSetGetTable()
    {
        $table = 'user';
        $this->assertEquals($table, self::$db->table($table)->getTable());
    }

    public function testResult()
    {
        // get
        $table = 'user';
        $pre = self::$pdo->prepare('select * from '.$table);
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals($expect, self::$db->table($table)->get());

        // row
        $table = 'user';
        $pre = self::$pdo->prepare('select * from '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($expect, self::$db->table($table)->row());
    }


    public function testAggregate()
    {
        // list
        $table = 'user';
        $field = 'username';
        $pre = self::$pdo->prepare('select '.$field.' from '.$table);
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_COLUMN, 0);

        $this->assertEquals($expect, self::$db->table($table)->list($field));

        // count
        $pre = self::$pdo->prepare('select COUNT('.$field.') as count_num from '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC)['count_num'];

        $this->assertEquals($expect, self::$db->table($table)->count($field));


    }
}
