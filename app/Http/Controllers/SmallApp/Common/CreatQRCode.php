<?php

namespace App\Http\Controllers\SmallApp\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreatQRCode extends Controller
{
    function newLeaderQRCode(){
        //得到appid
        $appid = $request -> session() -> get('appid');
        //得到access_token
        $ACCESS_TOKEN = new AccessToken();
        $ACCESS_TOKEN = $ACCESS_TOKEN -> get($request);
//        return $ACCESS_TOKEN;
        //请求二维码二进制流
        $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=".$ACCESS_TOKEN;
//        dump($url);
        //调用环境配置
        $post_data='{"path":"/pages/index/index","width":"234"}';
//        $data = json_encode($data);
//        dump($data);
//        $query = http_build_query($data);
//        $query = json_encode($data);
        $aContext = array(
            'http' => array(
                'method' => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $post_data,
                'dataType'=> 'json'
            )
        );
        $cxContext  = stream_context_create($aContext);
//        dump($_SERVER["SCRIPT_FILENAME"]);
//        dump($cxContext);

        //调用微信公众平台接口
        $result = file_get_contents($url,false,$cxContext);

        //生成图片
        $imgDir = './../public/QRCode/newLeader/';
        $filename=md5($appid).".jpg";///要生成的图片名字 md5(appid).jpg
//        dump(file_exists($imgDir));
        $xmlstr = $result;
        if(empty($xmlstr)) {
            $xmlstr = file_get_contents('php://input');
        }

        $jpg = $xmlstr;//得到post过来的二进制原始数据
        if(empty($jpg))
        {
            echo 'nostream';
            exit();
        }

        $file = fopen("".$imgDir.$filename,"w");//打开文件准备写入
        fwrite($file,$jpg);//写入
        fclose($file);//关闭

        $filePath = './'.$imgDir.$filename;

        //图片是否存在
        if(!file_exists($filePath))
        {
            return false;
        }
        return $filePath;
    }
}
