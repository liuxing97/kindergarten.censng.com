<?php
/**
 * Created by PhpStorm.
 * User: liuxing
 * Date: 2018-08-07
 * Time: 15:15
 */

namespace App\Http\Controllers\SmallApp\Request;


use Illuminate\Http\Request;

class GetUserInfo
{
    /**
     * 已登录的情况下，才会调用本方法，返回json 用户信息对象及用户类型，
     */
    public function getIdentity(Request $request)
    {
//        $request -> session() -> setId($_COOKIE['laravel_session']);
        //得到微信用户openid及从哪个幼儿园小程序进来
        $data = $request->session()->all();
//        dump($data);
        $wechat = $request -> session()-> get('openid');
        $appKindergarten = $request -> session() -> get('kindergarten');
        //查询控制表中是否存在记录
        $ctrlObj = new SmallappControl();
        $ctrlObj = $ctrlObj -> where('wechat',$wechat) -> where('kindergarten',$appKindergarten) -> first();
//        dump($wechat);
//        dump($appKindergarten);
//        dump($ctrlObj);
        if($ctrlObj){
            $ctrlData = $ctrlObj -> toArray();
            //缓存用户对象
            $request -> session() -> put('identity','control');
            $request -> session() -> put('userObj',$ctrlObj);
            //组织返回数据
            $retData = [
                'msg' => '用户存在',
                'type' => 'control',
                'userInfo' => $ctrlData,
                'time' => date('Y-m-d H:i:s')
            ];
            return $retData;
        }

        //查询管理表是否存在记录
        $adminObj = new SmallappAdmin();
        $adminObj = $adminObj -> where('wechat',$wechat) -> where('kindergarten',$appKindergarten) -> first();
        if($adminObj){
            $adminData = $adminObj -> toArray();
            //缓存用户对象
            $request -> session() -> put('identity',$adminData['type']);
            $request -> session() -> put('userObj',$adminObj);
            //组织返回数据
            $retData = [
                'msg' => '用户存在',
                'type' => $adminData['type'],
                'userInfo' => $adminData,
                'time' => date('Y-m-d H:i:s')
            ];
            return $retData;
        }


        //查询普通用户表是否存在记录
        $babyObj = new Baby();
        $babyObj = $babyObj -> where('wechat',$wechat) -> where('kindergarten',$appKindergarten) -> first();
        //如果存在
        if($babyObj){
            $babyData = $babyObj -> toArray();
            //缓存用户对象
            $request -> session() -> put('identity',$babyData['type']);
            $request -> session() -> put('userObj',$babyObj);
            //组织返回数据
            $retData = [
                'msg' => '用户存在',
                'type' => $babyData['type'],
                'userInfo' => $babyData,
                'time' => date('Y-m-d H:i:s')
            ];
            return $retData;
        }


        $retData = [
            'msg' => '用户不存在',
            'type' => 'tourist',
            'userInfo' => '',
            'time' => date('Y-m-d H:i:s')
        ];
        return $retData;
    }
}