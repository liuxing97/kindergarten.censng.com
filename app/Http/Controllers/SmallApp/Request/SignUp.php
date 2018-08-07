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
        //获取信息
        $archiveParams = $request -> only('wechat','kindergarten','name','nickname','sex','birthday','allergy','hobby',
            'class','parents','phone','relationship');
        $signUpParams = $request ->only('wechat','kindergarten','payType','cost','discount');
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
