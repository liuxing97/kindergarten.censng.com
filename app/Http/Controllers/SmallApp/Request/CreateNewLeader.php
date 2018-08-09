<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\ControlAuthorityApply;
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
//        dump($filePath);
        //预先得到src地址
        $src = "https://".$_SERVER['SERVER_NAME']."/QRCode/newLeader/".md5($appid).".jpg";
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


    public function bindWechatApply(Request $request){
//        dump($request ->session() ->all());
        $appid = $request -> session() -> get('appid');
        $wechat = $request -> session() -> get('openid');
        //判断是否已经存在过未处理的申请。
        $isApplyObj = new ControlAuthorityApply();
        $isApplyObj = $isApplyObj -> where('wechat',$wechat) -> where('action',NULL) -> first();
        if($isApplyObj){
            $data = [
                'msg' => 'has apply',
                'time' => date('Y-m-d H:i:s'),
            ];
            return $data;
        }
        $applytype = 'leader';
        $tableObj = new ControlAuthorityApply();
        $tableObj -> kindergarten = $appid;
        $tableObj -> applytype = $applytype;
        $tableObj -> wechat = $wechat;
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

    //得到所有本小程序待审核列表-园长申请权限列表
    public function getWaitingList(Request $request){
        $appid = $request -> session() -> get('appid');
        //查询
        $applyListObj = new ControlAuthorityApply();
        $applyListObj = $applyListObj -> where('kindergarten',$appid) ->get();
        if($applyListObj){
            $applyListObj = $applyListObj -> toArray();
        }
        return $applyListObj;
    }

    //处理小程序待审核列表中的数据
    public function handle(Request $request){
        //得到要处理的数据序号
        $dataId = $request -> get('dataId');
        //得到要处理的数据动作
        $dataAction = $request -> get('dataAction');
        if($dataAction != 'true' and $dataAction != 'false'){
            return 'handle error';
        }
        //获取数据
        $dataObj = new ControlAuthorityApply();
        $dataObj = $dataObj -> find($dataId);
        $dataObj -> action = $dataAction;
        $res = $dataObj -> save();
        if($res){
            $data = [
                'msg' => 'action: '.$dataAction.' ,success',
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            $data = [
                'msg' => 'action: '.$dataAction.' ,fail',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }
}
