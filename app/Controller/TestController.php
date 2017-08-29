<?php

namespace App\Controller;

class TestController {
    public function test($request) {
        return json_encode($request->requset);
    }
}
