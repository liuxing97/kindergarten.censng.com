<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\ControlAuthorityApply;
use App\Http\Controllers\SmallApp\Common\CreatQRCode;
use App\SmallappAdmin;
use App\SmallappClass;
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

    public function getWaitingList(Request $request){
        $appid = $request -> session() -> get('appid');
        //查询
        $applyListObj = new ControlAuthorityApply();
        $applyListObj = $applyListObj -> where('kindergarten',$appid) -> where('action',NULL) -> where('applytype', 'teacher') ->get();
        if($applyListObj){
            $applyListObj = $applyListObj -> toArray();
            $time = 0;
            foreach ($applyListObj as $applyItem){
                //查询班级编号，得到班级名称，并追加到数组中
                $classObj = new SmallappClass();
                $classObj = $classObj -> find($applyItem['parameter']);
                $applyItem['className'] = $classObj -> className;
                $applyListObj[$time] = $applyItem;
                $time++;
            }
        }
        return $applyListObj;
    }
    public function handle(Request $request){//得到要处理的数据序号
        $dataId = $request -> get('dataId');
        //得到要处理的数据动作
        $dataAction = $request -> get('dataAction');
        if($dataAction != 'true' and $dataAction != 'false'){
//            dump($dataAction);
//            dump(Input::all());
            return 'handle error';
        }
        //获取数据
        $dataObj = new ControlAuthorityApply();
//        $dataObj = $dataObj -> find($dataId);
        $dataObj = $dataObj -> where('id',$dataId) -> where('action',NULL) -> where('applytype','teacher') -> first();
        $dataObj -> action = $dataAction;
        $res = $dataObj -> save();
        $wechat = $dataObj -> wechat;
        if($res){
            //如果是要通过
            if($dataAction == 'true'){
                //创建账户
                $res = $this -> creatUser($request,$wechat,$dataObj->parameter);
                if($res){
                    $data = [
                        'msg' => 'action: '.$dataAction.' ,success',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }else{
                    $data = [
                        'msg' => 'action: '.$dataAction.' ,success,but create user fail',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }
            }else{
                $data = [
                    'msg' => 'action: '.$dataAction.' ,success',
                    'time' => date('Y-m-d H:i:s')
                ];
            }

        }else{
            $data = [
                'msg' => 'action: '.$dataAction.' ,fail',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }

    //处理小程序待审核列表中的数据-创建新的园长用户到数据库
    public function creatUser(Request $request,$wechat,$classid){
        $tableObj = new SmallappAdmin();
        $kindergarten = $request -> session() -> get('kindergarten');
        $type = 'teacher';
        $tableObj -> kindergarten = $kindergarten;
        $tableObj -> wechat = $wechat;
        $tableObj -> type = $type;
        $tableObj -> classid = $classid;
        $res = $tableObj -> save();
        if($res){
            return true;
        }else{
            return false;
        }
    }

}
