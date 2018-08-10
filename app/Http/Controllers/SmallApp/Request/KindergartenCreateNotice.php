<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\SmallappNotice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rules\In;

class KindergartenCreateNotice extends Controller
{
    //
    function newNotice(Request $request){
        $kindergarten = $request -> session() -> get('kindergarten');
        //因为这里应该是本人操作，所以直接从session中读openid
        $publisher = $request -> session() -> get('openid');
        $noticeType = Input::get('noticeType');
        $noticeTitle = Input::get('noticeTitle');
        $content = Input::get('content');
        //$href幼儿园无操作权限，仅可通过特殊通知传值
        $reader = Input::get('reader');
        //得到要操作的模型对象
        $modelObj = new SmallappNotice();
        $modelObj -> kindergarten = $kindergarten;
        $modelObj -> publisher = $publisher;
        $modelObj -> noticeType = $noticeType;
        $modelObj -> noticeTitle = $noticeTitle;
        $modelObj -> content = $content;
        $modelObj -> reader = $reader;
        $res = $modelObj -> save();
        if($res){
            $data = [
                'msg' => 'create notice success',
                'time' => date("Y-m-d H:i:s")
            ];
        }else{
            $data = [
                'msg' => 'create notice fail',
                'time' => date("Y-m-d H:i:s")
            ];
        }
        return $data;
    }
}
