<?php
use PHPUnit\Framework\TestCase;

class PDODQLTest extends TestCase
{
    protected static $db;

    protected static $pdo;

    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {
        self::$pdo = NULL;
        self::$db  = NULL;
    }

    public function testSetGetTable()
    {
        $table = 'user';

        $this->assertEquals($table, self::$db->table($table)->getTable());
    }

    public function testGet()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT * FROM '.$table);
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals($expect, self::$db->table($table)->get());
    }

    public function testRow()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT * FROM '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($expect, self::$db->table($table)->row());
    }

    public function testList()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT username FROM '.$table);
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_COLUMN, 0);

        $this->assertEquals($expect, self::$db->table($table)->list('username'));
    }

    public function testCount()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT COUNT(*) AS count_num FROM '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC)['count_num'];

        $this->assertEquals($expect, self::$db->table($table)->count('*'));
    }

    public function testSum()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT SUM(id) AS sum_num FROM '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC)['sum_num'];

        $this->assertEquals($expect, self::$db->table($table)->sum('id'));
    }

    public function testMax()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT MAX(id) AS max_num FROM '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC)['max_num'];

        $this->assertEquals($expect, self::$db->table($table)->max('id'));
    }

    public function testMin()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT MIN(id) AS min_num FROM '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC)['min_num'];

        $this->assertEquals($expect, self::$db->table($table)->min('id'));
    }

    public function testAvg()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT AVG(id) AS avg_num FROM '.$table);
        $pre->execute();
        $expect = $pre->fetch(PDO::FETCH_ASSOC)['avg_num'];

        $this->assertEquals($expect, self::$db->table($table)->avg('id'));
    }

    public function testSelect()
    {
        $table = 'user';

        $pre = self::$pdo->prepare('SELECT username, email FROM '.$table);
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals($expect, self::$db->table($table)->select('username', 'email')->get());

    }

    public function testWhere()
    {
        $table = 'user';

        // where
        $pre = self::$pdo->prepare('SELECT * FROM '.$table.' WHERE g_id = 3');
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table($table)->where('g_id', '3')->get();

        $this->assertEquals($expect, $testResult);

        // where 3 param
        $pre = self::$pdo->prepare('SELECT * FROM '.$table.' WHERE id <= 20');
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table($table)->where('id', '<=', '20')->get();

        $this->assertEquals($expect, $testResult);


        // where and where array param
        $pre = self::$pdo->prepare('SELECT * FROM '.$table.' WHERE sort_num = 20 AND activated = 0');
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table($table)->where(['sort_num' => '20', 'activated' => '0'])->get();

        $this->assertEquals($expect, $testResult);

        // or where
        $pre = self::$pdo->prepare('SELECT * FROM '.$table.' WHERE id < 20 or id >= 100');
        $pre->execute();
        $expect = $pre->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table($table)
            ->where('id', '<', 20)
            ->orWhere('id', '>=', 100)
            ->get();

        $this->assertEquals($expect, $testResult);

    }

}
