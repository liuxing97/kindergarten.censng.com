<?php
/**
 * Created by PhpStorm.
 * User: liuxing
 * Date: 2018-08-07
 * Time: 17:23
 */

namespace App\Http\Controllers\SmallApp\Request;


use App\Kindergarten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class OnLogin
{
    public function login(Request $request)
    {
        $appCode = Input::get('code');
        $appKindergarten = Input::get('kindergarten');
        dump($appKindergarten);
        //获取相对应的幼儿园配置
        $kindergartenObj = new Kindergarten();
        $kindergartenObj = $kindergartenObj -> where('kindergarten',$appKindergarten) -> first();
        if($kindergartenObj){
            $kindergartenItem = $kindergartenObj -> toArray();
            $appId = $kindergartenItem['appid'];
            $appSecret = $kindergartenItem['appsecret'];
            //放入session中
            $request -> session() -> push('appid', $appId);
            $request -> session() -> push('appsecret', $appSecret);
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
            $result = json_decode($result);
            $userOpenid = $result['openid'];
            dump($userOpenid);
            echo "123";
            $session_key = $result['session_key'];
            dump($session_key);
        }else{
            echo "幼儿园未查询到";
        }
    }
}