<?php

namespace App\Controller;
use Framework\Controller;
use Framework\DB;


class TestController extends Controller {
    public function test($request) {
        $rst = DB::$connection['con1']->query('select * from users limit 0, 30');
        return $rst;
    }
}
