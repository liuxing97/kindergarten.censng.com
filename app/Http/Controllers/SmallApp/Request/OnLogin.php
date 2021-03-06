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
    function __construct(Request $request)
    {

    }

    public function login(Request $request)
    {
//        $request -> session() -> setId($_COOKIE['laravel_session']);
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
//            dump($appId);
//            dump($appSecret);
//            exit;
            $userOpenid = $result ->openid;
            $request -> session() -> put('openid',$userOpenid);
//            dump($userOpenid);
//            echo "123";
            $session_key = $result ->session_key;
            $request -> session() -> put('session_key',$session_key);
            $data = $request->session()->all();
//            dump($data);
            return [
                'msg' => 'temp login success',
                'session' => $request -> session() -> get('_token'),
                'time' => date('Y-m-d H:i:s')
            ];
//            dump($session_key);
        }else{
            return [
                'msg' => 'temp login success fail',
                'session' => $request -> session() -> get('_token'),
                'time' => date('Y-m-d H:i:s')
            ];
        }
    }

}