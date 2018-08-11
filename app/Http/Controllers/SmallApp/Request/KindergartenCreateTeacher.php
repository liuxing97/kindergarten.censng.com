<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\ControlAuthorityApply;
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
        $filePath = "./../public/QRCode/newTeacher/".md5($appid.$classId).".jpg";
//        dump($filePath);
        //预先得到src地址
        $src = "https://".$_SERVER['SERVER_NAME']."/QRCode/newTeacher/".md5($appid.$classId).".jpg";
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

    public function bindWechatApply(Request $request){
        //        dump($request ->session() ->all());
        $appid = $request -> session() -> get('appid');
        $wechat = $request -> session() -> get('openid');
        $classId = $request -> input('classId');
        //判断是否已经存在过未处理的申请，或已成功的申请。
        $isApplyObj = new ControlAuthorityApply();
        $isApplyObj = $isApplyObj -> where('wechat',$wechat) -> where('action',NULL) -> orWhere('action','true') -> first();
        if($isApplyObj){
            $data = [
                'msg' => 'has apply',
                'time' => date('Y-m-d H:i:s'),
            ];
            return $data;
        }
        $applytype = 'teacher';
        $tableObj = new ControlAuthorityApply();
        $tableObj -> kindergarten = $appid;
        $tableObj -> applytype = $applytype;
        $tableObj -> wechat = $wechat;
        $tableObj -> parameter = $classId;
        $ret = $tableObj -> save();
        if($ret){
            $data = [
                'msg' => 'apply success',
                'time' => date('Y-m-d H:i:s'),
            ];
        }else{
            $data = [
                'msg' => 'apply fail',
                'time' => date('Y-m-d H:i:s'),
            ];
        }
        return $data;

    }
    public function getWaitingList(){}
    public function handle(){}
}
