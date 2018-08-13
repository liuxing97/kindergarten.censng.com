<?php

namespace App\Http\Controllers\SmallApp\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreatQRCode extends Controller
{

    function newLeaderQRCode(Request $request){
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
        $post_data='{"path":"/controls/newLeaderUser/newLeaderUser","width":"800"}';
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

    function newTeacherQRCode(Request $request){
        //得到appid
        $appid = $request -> session() -> get('appid');
        //得到班级id
        $classId = $request -> input('classId');
        //得到access_token
        $ACCESS_TOKEN = new AccessToken();
        $ACCESS_TOKEN = $ACCESS_TOKEN -> get($request);
//        return $ACCESS_TOKEN;
        //请求二维码二进制流
        $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=".$ACCESS_TOKEN;
//        dump($url);
        //调用环境配置
        $path = "/leader/teacher/scaned?classId=".$classId;
        $post_data='{"path":"'.$path.'","width":"800"}';
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
        $imgDir = './../public/QRCode/newTeacher/';
        //文件命名规则：md5(appid+classId)
        $filename=md5($appid.$classId).".jpg";///要生成的图片名字 md5(appid).jpg
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

    function newDiscountQRCode(Request $request) {
        //得到微信标识
        $wechat = $request -> session() -> get('openid');
        //得到优惠券id
        $discountId = $request -> input('discountId');
        //得到是幼儿园编号
        $kindergarten = $request -> session() -> get('kindergarten');
        //得到access_token
        $ACCESS_TOKEN = new AccessToken();
        $ACCESS_TOKEN = $ACCESS_TOKEN -> get($request);
//        return $ACCESS_TOKEN;
        //请求二维码二进制流
        $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=".$ACCESS_TOKEN;
//        dump($url);
        //调用环境配置 wechat用来验证是否已转发，只要有别人点开，优惠券即生效
        $path = "discount/signupDiscount/signupDiscount?wechat=".$wechat;
        $post_data='{"path":"'.$path.'","width":"800"}';
        $aContext = array(
            'http' => array(
                'method' => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $post_data,
                'dataType'=> 'json'
            )
        );
        $cxContext  = stream_context_create($aContext);

        //调用微信公众平台接口
        $result = file_get_contents($url,false,$cxContext);
        //生成图片-图片地址
        $imgDir = "./discounts/QRCode/".$kindergarten.'/';
        $resultDir = "./discounts/result/".$kindergarten.'/';
        if(!file_exists($imgDir)){
//            echo "文件创建成果";
            mkdir ($imgDir,0777,true);
            mkdir ($resultDir,0777,true);
        }
        else{
//            echo "123";
        }
//        exit;
        //文件命名规则：md5(appid+classId)
        $filename=md5($wechat.$discountId).".jpg";///要生成的图片名字 md5(appid).jpg
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
