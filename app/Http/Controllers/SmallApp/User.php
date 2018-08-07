<?php

namespace App\Http\Controllers\SmallApp;

use App\Baby;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class User extends Controller
{
    //是否已登记用户
    public function isRegister($wechat){
        //查询用户是否在宝宝用户表内babys表
        $babyObj = new Baby();
        $babyObj = $babyObj -> where('wechat',$wechat) -> first();
        //如果已经登记,返回登记的数据模型
        if($babyObj){
            return $babyObj;
        }else{
            return false;
        }
    }
}
