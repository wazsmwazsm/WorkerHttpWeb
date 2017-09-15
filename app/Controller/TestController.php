<?php

namespace App\Controller;
use Framework\Controller;
use Framework\DB\DB;


class TestController extends Controller {
    public function test($request) {
        // $rst = DB::$connection['con1']->query('select * from ad_promote_info limit 0, 30');


        // $rst = DB::$connection['con2']->table('ad_promote_collect')
        //      ->leftJoin('ad_promote_oss', 'ad_promote_oss.adId', 'ad_promote_collect.adId')
        //      ->select('ad_promote_oss.adId')
        //      ->where('ad_promote_collect.date', 1500912000)
        //      ->get();
        // $rst = DB::$connection['con1']->table('ad_promote_info')
        //      ->where('id', '<', 10)
        //      ->orWhereIn('id', [62,22,1,3])
        //      ->get();

        $rst = DB::$connection['con2']->table('ad_promote_collect')
             ->where('id', '<', 10)
             ->orBrackets(function($query) {
                $query->where('adId', '001-001')
                      ->orWhere('adId', '001-003');
             })
             ->orderBy('id', 'DESC')
             ->get();

        // $rst = (string) $rst;
        return $rst;
    }
}
