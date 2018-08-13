<?php

namespace App\Http\Controllers\SmallApp\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Baby\Data;
use App\Http\Controllers\SmallApp\Response\SignUpResponse;
use Illuminate\Support\Facades\DB;

class SignUp extends Controller
{
    /**
     * 创建新的宝宝档案
     * @param Request $request
     * @return bool
     */
    public function newArchive(Request $request)
    {
        //开启事务
        DB::beginTransaction();
        //获取微信号
        $wechat = $request -> session() -> get('openid');
        //获取幼儿园标识
        $kindergarten = $request -> session() -> get('kindergarten');
        //获取信息-创建档案所需要的信息
        $archiveParams = $request -> only('babyName','babyNickName','babySex','birthday','allergy','hobby',
            'class','parents','phone','relationship');
        //获取信息-报名所需要的信息
        $signUpParams = $request -> only('babyClassType','payType','classTypeCost','discount');
        //创建档案
        $babyDataObj = new Data();
        $isNewArchiveTrue = $babyDataObj -> newArchive($archiveParams);
        //创建报名信息
        $babySignUpObj = new \App\Http\Controllers\Baby\SignUp();
        $isNewSignUpTrue = $babySignUpObj -> newSignUp($signUpParams);
        //判断是否更新成功并调用回复
        $responseObj = new SignUpResponse();
        if($isNewArchiveTrue && $isNewSignUpTrue){
            DB::commit();
            return $responseObj -> signUpSeccess();
        } else {
            DB::rollback();
            return $responseObj -> signUpfail();
        }
    }
}
