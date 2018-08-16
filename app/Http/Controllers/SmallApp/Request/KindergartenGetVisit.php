<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\NodiscountData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenGetVisit extends Controller
{
    /**
     * 得到访问数据
     */
    public function getData(Request $request){
        $kindergarten = $request -> session() -> get('kindergarten');
        $tableobj = new NodiscountData();
        $tableobj = $tableobj
            -> where('kindergarten',$kindergarten)
            -> where('isZero',null)
            -> first();
        if($tableobj){
            $tableDataArray = $tableobj -> toArray();
            $data = [
                'msg' => 'has data',
                'data' => $tableDataArray,
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            $data = [
                'msg' => 'get data fail',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }
}
