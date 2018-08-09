<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\Http\Controllers\SmallApp\Common\AccessToken;
use App\Http\Controllers\SmallApp\Common\CreatQRCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateNewLeader extends Controller
{
    //创建园长账户-得到二维码
    function getQRCode(Request $request)
    {
        $appid = $request -> session() -> get('appid');
        //判断是否已存在当前小程序用来打开创建园长账户的二维码
        $filePath = "./../public/QRCode/newLeader/".md5($appid).".jpg";
        //预先得到src地址
        $src = "https://".$_SERVER['SERVER_NAME']."/qrQRCode/newLeader/".md5($appid).".jpg";
        if(file_exists($filePath)){
            //返回图片地址
            $data = [
                'msg' => 'return QRCode success',
                'src' => $src,
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            //调用创建二维码
            $creatQRCodeObj = new CreatQRCode();
            $ret = $creatQRCodeObj -> newLeaderQRCode($request);
            if($ret){
                //返回图片地址
                $data = [
                    'msg' => 'return QRCode success',
                    'src' => $src,
                    'time' => date('Y-m-d H:i:s')
                ];
            }else{
                //创建二维码失败
                $data = [
                    'msg' => 'return QRCode fail',
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }
        return $data;
    }
}
