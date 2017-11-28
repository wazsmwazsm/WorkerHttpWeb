<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Framework\DB\Drivers\Mysql;

class DatabaseTest extends TestCase
{
    use TestCaseTrait;

    protected static $db;

    public static function setUpBeforeClass()
    {
        self::$db = new Mysql('localhost', '3306', 'homestead', 'secret', 'homestead', 'utf8');
    }

    public static function tearDownAfterClass()
    {
        self::$db = null;
    }

    public function getConnection()
    {
        $dsn = 'mysql:dbname=homestead;host=localhost;port=3360';
        $pdo = new PDO($dsn, 'homestead', 'secret');
        return $this->createDefaultDBConnection($pdo, ':memory:');
    }



    public function getDataSet()
    {
        return $this->createXMLDataSet('myXmlFixture.xml');
    }

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testPDO()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('guestbook'));
    }
    public function testA()
    {
        var_dump(self::$db);
    }

}
