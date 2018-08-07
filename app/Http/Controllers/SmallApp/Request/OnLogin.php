<?php
/**
 * Created by PhpStorm.
 * User: liuxing
 * Date: 2018-08-07
 * Time: 17:23
 */

namespace App\Http\Controllers\SmallApp\Request;


use App\Baby;
use App\Kindergarten;
use App\SmallappAdmin;
use App\SmallappControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class OnLogin
{
    public function login(Request $request)
    {
        $appCode = Input::get('code');
        $appKindergarten = Input::get('kindergarten');
        //kindergarten信息放入session中
        $request->session()->put('kindergarten',$appKindergarten);
//        dump($appKindergarten);
        //获取相对应的幼儿园配置
        $kindergartenObj = new Kindergarten();
        $kindergartenObj = $kindergartenObj -> where('kindergarten',$appKindergarten) -> first();
        if($kindergartenObj){
            $kindergartenItem = $kindergartenObj -> toArray();
            $appId = $kindergartenItem['appid'];
            $appSecret = $kindergartenItem['appsecret'];
            //放入session中
            $request -> session() -> put('appid', $appId);
            $request -> session() -> put('appsecret', $appSecret);
            //使用微信接口-登录凭证校验接口
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code';
            $data = [
                'appid'=> $appId,
                'secret' => $appSecret,
                'js_code' => $appCode,
                'grant_type' => 'authorization_code'
            ];
            $postdata = http_build_query(
                $data
            );
            $opts = array('http' =>
                array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );
            $context = stream_context_create($opts);
            $result = file_get_contents($url, false, $context);
            //解析获取的openid与session_key
            $result = json_decode($result);
//            dump($result);
            $userOpenid = $result ->openid;
            $request -> session() -> put('openid',$userOpenid);
//            dump($userOpenid);
//            echo "123";
            $session_key = $result ->session_key;
            $request -> session() -> put('session_key',$session_key);
            $data = $request->session()->all();
//            dump($data);
            return [
                'msg' => '用户已完成临时登录',
                'session' => $request -> session() -> get('_token'),
                'time' => date('Y-m-d H:i:s')
            ];
//            dump($session_key);
        }else{
            return [
                'msg' => '幼儿园未查询到，所以无法进行临时登录',
                'session' => $request -> session() -> get('_token'),
                'time' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * 已登录的情况下，才会调用本方法，返回json 用户信息对象及用户类型，
     */
    public function getIdentity(Request $request)
    {
        //得到微信用户openid及从哪个幼儿园小程序进来
        $data = $request->session()->all();
        dump($data);
        $wechat = $request -> session()-> get('openid');
        $appKindergarten = $request -> session() -> get('kindergarten');
        //查询控制表中是否存在记录
        $ctrlObj = new SmallappControl();
        $ctrlObj = $ctrlObj -> where('wechat',$wechat) -> where('kindergarten',$appKindergarten) -> first();
        dump($wechat);
        dump($appKindergarten);
        dump($ctrlObj);
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