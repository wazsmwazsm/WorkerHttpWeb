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
        $this->assertEquals('user', self::$db->table('user')->getTable());
    }

    public function testGet()
    {
        $expect = self::$pdo->query('SELECT * FROM user ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testRow()
    {
        $expect = self::$pdo->query('SELECT * FROM user ')
                ->fetch(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->row();

        $this->assertEquals($expect, $testResult);
    }

    public function testList()
    {
        $expect = self::$pdo->query('SELECT username FROM user ')
                ->fetchAll(PDO::FETCH_COLUMN, 0);
        $testResult = self::$db->table('user')->list('username');

        $this->assertEquals($expect, $testResult);
    }

    public function testCount()
    {
        $expect = self::$pdo->query('SELECT COUNT(*) AS count_num FROM user ')
                ->fetch(PDO::FETCH_ASSOC)['count_num'];
        $testResult = self::$db->table('user')->count('*');

        $this->assertEquals($expect, $testResult);
    }

    public function testSum()
    {
        $expect = self::$pdo->query('SELECT SUM(id) AS sum_num FROM user ')
                ->fetch(PDO::FETCH_ASSOC)['sum_num'];
        $testResult = self::$db->table('user')->sum('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testMax()
    {
        $expect = self::$pdo->query('SELECT MAX(id) AS max_num FROM user ')
                ->fetch(PDO::FETCH_ASSOC)['max_num'];
        $testResult = self::$db->table('user')->max('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testMin()
    {
        $expect = self::$pdo->query('SELECT MIN(id) AS min_num FROM user ')
                ->fetch(PDO::FETCH_ASSOC)['min_num'];
        $testResult = self::$db->table('user')->min('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testAvg()
    {
        $expect = self::$pdo->query('SELECT AVG(id) AS avg_num FROM user ')
                ->fetch(PDO::FETCH_ASSOC)['avg_num'];
        $testResult = self::$db->table('user')->avg('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testSelect()
    {
        // field
        $expect = self::$pdo->query('SELECT username, email FROM user ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->select('username', 'email')->get();

        $this->assertEquals($expect, $testResult);
        // *
        $expect = self::$pdo->query('SELECT * FROM user ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->select('*')->get();

        $this->assertEquals($expect, $testResult);

    }

    public function testWhere()
    {
        // where
        $expect = self::$pdo->query('SELECT * FROM user WHERE g_id = 3')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('g_id', 3)->get();

        $this->assertEquals($expect, $testResult);

        // where = param
        $expect = self::$pdo->query('SELECT * FROM user WHERE id = 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where > param
        $expect = self::$pdo->query('SELECT * FROM user WHERE id > 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '>', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where < param
        $expect = self::$pdo->query('SELECT * FROM user WHERE id < 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '<', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where <= param
        $expect = self::$pdo->query('SELECT * FROM user WHERE id <= 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '<=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where >= param
        $expect = self::$pdo->query('SELECT * FROM user WHERE id >= 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '>=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where !=
        $expect = self::$pdo->query('SELECT * FROM user WHERE id != 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '!=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where <>
        $expect = self::$pdo->query('SELECT * FROM user WHERE id <> 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '<>', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where array param
        $expect = self::$pdo->query('SELECT * FROM user WHERE sort_num = 20 AND activated = 0')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where(['sort_num' => 20, 'activated' => 0])->get();

        $this->assertEquals($expect, $testResult);

        // and where 
        $expect = self::$pdo->query('SELECT * FROM user WHERE (sort_num = 20 AND activated = 0 AND id = 24)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where(['sort_num' => 20, 'activated' => 0])
            ->where('id', 24)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where
        $expect = self::$pdo->query('SELECT * FROM user WHERE id < 20 OR id >= 100')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', '<', 20)
            ->orWhere('id', '>=', 100)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where array param
        $expect = self::$pdo->query('SELECT * FROM user WHERE id >= 100 OR (sort_num = 50 AND activated = 1)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', '>=', 100)
            ->orWhere(['sort_num' => 50, 'activated' => 1])
            ->get();

        $this->assertEquals($expect, $testResult);

    }

    public function testWhereIn()
    {
        // where in
        $expect = self::$pdo->query('SELECT * FROM user WHERE id IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);

        // where not in
        $expect = self::$pdo->query('SELECT * FROM user WHERE id NOT IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNotIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where in
        $expect = self::$pdo->query('SELECT * FROM user WHERE id = 3 OR id IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->Where('id', '=', 3)
            ->orWhereIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);
        // or where not in
        $expect = self::$pdo->query('SELECT * FROM user WHERE id != 3 OR id NOT IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->Where('id', '!=', 3)
            ->orWhereNotIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testWhereBetween()
    {
        // where between
        $expect = self::$pdo->query('SELECT * FROM user WHERE id BETWEEN 20 and 30')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereBetween('id', 20, 30)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where between
        $expect = self::$pdo->query('SELECT * FROM user WHERE id = 1 OR id BETWEEN 20 and 30')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', 1)
            ->orWhereBetween('id', 20, 30)
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testWhereNull()
    {
        // where null
        $expect = self::$pdo->query('SELECT * FROM user WHERE username IS NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);

        // where not null
        $expect = self::$pdo->query('SELECT * FROM user WHERE username IS NOT NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNotNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where NULL
        $expect = self::$pdo->query('SELECT * FROM user WHERE id = 5 OR username IS NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', 5)
            ->orWhereNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where not NULL
        $expect = self::$pdo->query('SELECT * FROM user WHERE id = 5 OR username IS NOT NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', 5)
            ->orWhereNotNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testWhereBrackets()
    {
        // Where Brackets
        $expect = self::$pdo->query('SELECT * FROM user WHERE (id < 50 OR username IS NOT NULL) AND sort_num = 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereBrackets(function($query) {
                $query->where('id', '<', 50)
                      ->orWhereNotNull('username');
            })
            ->where('sort_num', 20)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or Where Brackets
        $expect = self::$pdo->query('SELECT * FROM user WHERE sort_num = 20 OR (id < 10 AND id > 5)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('sort_num', 20)
            ->orWhereBrackets(function($query) {
                $query->where('id', '<', 10)
                      ->where('id', '>', 5);
            })
            ->get();

        $this->assertEquals($expect, $testResult);
    }




}
