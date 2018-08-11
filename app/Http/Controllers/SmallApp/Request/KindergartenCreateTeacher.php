<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\Http\Controllers\SmallApp\Common\CreatQRCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenCreateTeacher extends Controller
{
    /**
     * 得到新建老师QRCode
     */
    function getQRCode(Request $request){
        $appid = $request -> session() -> get('appid');
        //获取老师的班级权限
        $classId = $request -> input('classId');
        //判断是否已存在当前小程序用来打开创建园长账户的二维码
        $filePath = "./../public/QRCode/newLeader/".md5($appid.$classId).".jpg";
//        dump($filePath);
        //预先得到src地址
        $src = "https://".$_SERVER['SERVER_NAME']."/QRCode/newLeader/".md5($appid.$classId).".jpg";
//        dump($src);
        if(file_exists($filePath)){
//            dump('has');
            //返回图片地址
            $data = [
                'msg' => 'return QRCode success',
                'src' => $src,
                'time' => date('Y-m-d H:i:s')
            ];
        }else {
            //调用创建二维码
            $creatQRCodeObj = new CreatQRCode();
            $ret = $creatQRCodeObj -> newTeacherQRCode($request);
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
