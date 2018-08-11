<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\SmallappClassType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenCreateClassType extends Controller
{
    //创建新类型
    public function newType(Request $request){
        //得到幼儿园id
        $kindergarten = $request -> session() -> get('kindergarten');
        $title = $request -> input('title');
        $cost = $request -> input('cost');
//        dump($title);
//        dump($cost);
        //得到要tableObj
        $tableObj = new SmallappClassType();
        $tableObj -> kindergarten = $kindergarten;
        $tableObj -> title = $title;
        $tableObj -> cost = $cost;
        $res = $tableObj -> save();
        if($res){
            $data = [
                'msg' => 'new classType success',
                'time' => date("Y-m-d H:i:s")
            ];
        }else{
            $data = [
                'msg' => 'new classType fail',
                'time' => date("Y-m-d H:i:s")
            ];
        }
        return $data;
    }
}
