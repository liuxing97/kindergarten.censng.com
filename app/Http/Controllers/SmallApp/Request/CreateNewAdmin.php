<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\SmallappControl;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateNewAdmin extends Controller
{
    //
    function create(Request $request){
        $openid = $request -> session() -> get('openid');
        $kindergarten = $request -> session() -> get('kindergarten');
        $tableObj = new SmallappControl();
        $tableObj -> kindergarten = $kindergarten;
        $tableObj -> wechat = $openid;
        $ret = $tableObj -> save();
        if($ret){
            $data = [
                'msg' =>'create success',
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            $data = [
                'msg' => 'create fail',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }
}
