<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class PDODMLTest extends TestCase
{
    use TestCaseTrait;

    protected static $db;

    protected static $pdo;

    public function getConnection()
    {

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

}
