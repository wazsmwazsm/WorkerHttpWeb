<?php

namespace App\Controller;
use Framework\Http\Controller;
use Framework\Http\Requests;
use Framework\DB\DB;
use App\Models\Test;
use Framework\DB\Redis;


class TestController extends Controller
{
    public function test(Test $model, Requests $request)
    {
        $rst = $model->get();

        return $rst;
    }
}
