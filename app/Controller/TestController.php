<?php

namespace App\Controller;
use Framework\Controller;

class TestController extends Controller {
    public function test($request) {
        return $request->requset;
    }
}
