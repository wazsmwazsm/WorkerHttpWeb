<?php

namespace App\Controller;
use Framework\Controller;
use Framework\DB\DB;


class TestController extends Controller {
    public function test($request) {
        // $rst = DB::$connection['con1']->query('select * from ad_promote_info limit 0, 30');

        $rst = DB::$connection['con1']->table('users')
             ->where('id', '<', 10)
             ->where('id', '>', 2)
             ->get();
        return $rst;
    }
}
