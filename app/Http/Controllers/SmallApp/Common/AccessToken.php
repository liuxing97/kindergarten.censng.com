<?php

namespace App\Http\Controllers\SmallApp\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccessToken
{
    //获取
    public function get(Request $request){

        //获取session中的数据
        $appid = $request -> session() -> get('appid');
        $secret = $request -> session() -> get('appsecret');
//        dump($appid);
        //接口地址
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
//        dump($url);
        //调用环境配置
        $aContext = array(
            'http' => array(
                'method' => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => '' )
        );
        $cxContext  = stream_context_create($aContext);
        //调用微信公众平台接口
        $ret = file_get_contents($url,false,$cxContext);
        $rets = json_decode($ret);
//        $rets -> access_token;
        return $rets -> access_token;
    }
}
