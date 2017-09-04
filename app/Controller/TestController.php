<?php

namespace App\Controller;
use Framework\Controller;


class TestController extends Controller {
    public function test($request) {
        $rst = (new Test)->query('select * from users limit 0, 30');
        return $rst;
    }
}
