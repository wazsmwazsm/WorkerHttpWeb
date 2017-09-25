<?php

namespace App\Controller;
use Framework\Http\Controller;
use Framework\Http\Requests;
use Framework\DB\DB;
use App\Models\Test;

class TestController extends Controller
{
    public function test(Test $model, Requests $request)
    {
        // $rst = DB::connection('con1')->query('select * from ad_promote_info limit 0, 30');


        // $rst = DB::connection('con2')->table('ad_promote_collect')
        //      ->leftJoin('ad_promote_oss', 'ad_promote_oss.adId', 'ad_promote_collect.adId')
        //      ->select('ad_promote_oss.adId')
        //      ->where('ad_promote_collect.date', 1500912000)
        //      ->get();
        // $rst = DB::connection('con1')->table('ad_promote_info')
        //      ->where('id', '<', 10)
        //      ->get();
       $rst = $model
              ->where('id', '<', 10)
              ->get();
        // $rst = DB::connection('con2')->table('ad_promote_collect')
        //      ->where([
        //        'adId' => '001-001',
        //        'adStyle' => 'big_ad',
        //      ])
        //      ->get();
        // $rst = DB::connection('con2')->table('ad_promote_collect')
        //      ->where('id', '<', 10)
        //      ->orWhereBrackets(function($query) {
        //         $query->where('adId', '001-001')
        //               ->orWhere('adId', '001-003');
        //      })
        //      ->orderBy('id', 'DESC')
        //      ->get();


        // $rst = Test::where([
        //       'adId' => '001-001',
        //       'adStyle' => 'big_ad',
        //     ])
        //     ->get();
        // $rst = Test::where([
        //       'adId' => '001-001',
        //       'adStyle' => 'big_ad',
        //     ])->orderBy('id', 'DESC')
        //     ->get();

        // $rst = DB::connection('con2')->table('ad_promote_collect')
        //       ->list('id');
        // $rst = (string) $rst;

        // $rst = DB::connection('con2')->table('ad_promote_collect')
        //      ->whereNull('adStyle')
        //      ->whereNotNull('package_name')
        //      ->get();
        // $rst = DB::connection('con2')->table('ad_promote_collect')
        //      ->whereInSub('id', function($query) {
        //           $query->table('ad_promote_info')
        //                 ->select('id')->where('id', '<', '10');
        //      })
        //      ->orderBy('id', 'DESC')
        //      ->get();

        //  $rst = $model->whereInSub('id', function($query) {
        //            $query->table('ad_promote_info')
        //                  ->select('id')->where('id', '<', '100');
        //       })
        //       ->orderBy('id', 'DESC')
        //       ->paginate(10, $request->page);
        // $rst = DB::connection('con2')->select('id','adId','adTitle')->fromSub(function($query) {
        //   $query->table('ad_promote_info')->where('id', '<', '100');
        // })->where('id', '!=', 9)
        // ->orderBy('id', 'ASC')
        // ->paginate(10, $request->page);

        // $rst = DB::connection('con2')->table('ad_promote_collect')
        // ->withDebug()
        // ->insert([
        //   'package_name' => 'aa',
        //   'adId' => '007-008',
        //   'adStyle' => 'big_ad',
        //   'request' => 0,
        //   'impression' => 20,
        //   'click' => 10,
        //   'date' => time(),
        // ]);


        // $rst = DB::connection('con2')->table('ad_promote_collect')
        // ->where('package_name', 'aa')->withDebug()
        // ->update([
        //   'impression' => 20,
        //   'click' => 10,
        // ]);
        // $rst = DB::connection('con2')->table('ad_promote_collect')
        // ->where('package_name', 'aa')
        // ->delete();

        // $rst = (string) $rst;

        return $rst;
    }
}
