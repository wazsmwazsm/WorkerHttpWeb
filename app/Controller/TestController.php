<?php

namespace App\Controller;
use Framework\Controller;
use Framework\DB\DB;


class TestController extends Controller {
    public function test($request) {
        // $rst = DB::$connection['con1']->query('select * from ad_promote_info limit 0, 30');

        $rst = DB::$connection['con2']->table('material_audio_new_info')
             ->select('group as gp', 'count(*) as gp_count')
             ->groupBy('group')
             ->having('count(*)','>', 13)
             ->get();
        return $rst;
    }
}
