<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\Baby;
use App\BabyMore;
use App\BabySignup;
use App\KindergartenSemester;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SignUp extends Controller
{
    /**
     * 创建新的宝宝档案
     * @param Request $request
     */
    public function createSignup(Request $request)
    {
        //开启事务
        DB::beginTransaction();
        //获取微信号
        $wechat = $request -> session() -> get('openid');
        //获取幼儿园标识
        $kindergarten = $request -> session() -> get('kindergarten');
        //获取信息-创建档案所需要的信息
        $archiveParams = $request -> only('babyName','babyNickName','babySex','birthday','allergy','hobby',
            'parents','phone','relationship');
        //获取信息-报名所需要的信息
        $signUpParams = $request -> only('babyClassType','payType','classTypeCost','discountId');
        //初始值
        $isHasBabyDef = false;
        $isHasSignupDef = false;

        //创建宝宝用户
        $babyObj = new Baby();
        $signupObj = new BabySignup();
        $semesterObj = new KindergartenSemester();
        //得到已开始，没有结束的学期
        $semesterObj = $semesterObj -> where('kindergarten',$kindergarten) -> where('end',NULL) -> get();
        //如果本学期已开始
        if($semesterObj){
            //本学期id是
            $semesterId = $semesterObj -> id;
            //判断是否已经创建过用户信息
            $isHasObj = $babyObj -> where('wechat',$wechat) -> get();
            //如果宝宝资料已创建过
            if($isHasObj){
                //判断要建立宝宝的名字，在宝宝表中已存在
                foreach ($isHasObj as $babyObj){
                    $name = $babyObj -> name;
                    if($archiveParams['babyName'] == $name){
                        //已存在宝宝记录
                        $isHasBabyDef = true;
                    }
                }
            }
            //判断是否已报名过
            $isHasSignupObj = $signupObj -> where('wechat',$wechat) -> where('semestarId',$semesterId) -> get();
            //如果已存在报名记录
            if($isHasSignupObj){
                //判断要建立宝宝的名字，在报名表中已存在
                foreach($isHasSignupObj as $signupObj){
                    $name = $signupObj -> name;
                    if($signUpParams['name'] == $name){
                        //已存在报名记录
                        $isHasSignupDef = true;
                    }
                }
            }

            //如果从来没有记录过信息
            if(!$isHasBabyDef && !$isHasSignupDef){
                //没用户 没报名----调用新档案 -调用新报名
                $creatArchRes = $this -> createBaby($wechat,$kindergarten,$archiveParams);
                if($creatArchRes){
                    $createSignupRes = $this -> createSignupData($wechat,$creatArchRes,$kindergarten,$signUpParams);
                    if($createSignupRes){
                        $data = [
                            'msg' => 'signup success',
                            'data' => $createSignupRes,
                            'time' => date('Y-m-d H:i:s')
                        ];
                    }else{
                        $data = [
                            'msg' => 'create signup fail',
                            'time' => date('Y-m-d H:i:s')
                        ];
                    }
                }else{
                    $data = [
                        'msg' => 'create archive fail',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }
            }else if($isHasBabyDef && !$isHasSignupDef){
                //有用户 没报名----用户是老用户，今年报名没有报--暂时不作处理
                $data = [
                    'msg' => 'fail',
                    'time' => date('Y-m-d H:i:s')
                ];
            }else if($isHasBabyDef && $isHasSignupDef){
                //有用户 有报名----已报名过--返回信息
                $data = [
                    'msg' => 'is signuped',
                    'time' => date('Y-m-d H:i:s')
                ];
            }else if(!$isHasBabyDef && $isHasSignupDef){
                //没用户 有报名----异常数据--暂时不作处理
                $data = [
                    'msg' => 'error',
                    'error' => 'havnt baby but has signup',
                    'time' => date('Y-m-d H:i:s')
                ];
            }else {
                $data = [
                    'msg' => 'unknown error',
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }else{
            //学校还没有进行学期开始，所以暂时还不可以报名
            $data = [
                'msg' => 'not open school',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }

    /**
     * 创建账户信息，从报名信息中提取账户信息
     * @param $wechat
     * @param $kindergarten
     * @param $parameter
     * @return bool
     */
    private function createBaby($wechat,$kindergarten,$parameter) {
        $baseTableObj = new Baby();
        $baseTableObj -> wechat = $wechat;
        $baseTableObj -> kindergarten = $kindergarten;
        $baseTableObj -> name = $parameter['babyName'];
        $baseTableObj -> nickname = $parameter['babyNickName'];
        $baseTableObj -> sex = $parameter['babySex'];
        $baseTableObj -> birthday = $parameter['birthday'];
        $baseTableObj -> partnets = $parameter['parents'];
        $baseTableObj -> relationship = $parameter['relationship'];
        $baseRes = $baseTableObj -> save();
        if($baseRes){
            //如果成功写入，继续写附加信息
            $moreTableObj = new BabyMore();
            $moreTableObj -> wechat = $wechat;
            $moreTableObj -> babyId = $baseTableObj -> id;
            $moreTableObj -> allergy = $parameter['allergy'];
            $moreTableObj -> hobby = $parameter['hobby'];
            $moreRes = $moreTableObj -> save();
            if($moreRes){
                return $baseTableObj -> id;
            }else{
                return false;
            }
        }else{
            //写入失败
            return false;
        }

    }

    /**
     * 创建报名信息
     */
    protected function createSignupData($wechat,$babyId,$kindergarten,$parameter){
        //报名信息
        $signupObj = new BabySignup();
        $signupObj -> wechat = $wechat;
        $signupObj -> babyId = $babyId;
        $signupObj -> kindergarten = $kindergarten;
        $signupObj -> classType = $parameter['babyClassType'];
        $signupObj -> cost = $parameter['classTypeCost'];
        if($parameter['discountId'] != 'null'){
            //暂时先不判断优惠券是否有效，默认为有效，直接写入
            $signupObj -> discountId = $parameter['discountId'];
        }
        $signupObj -> paytype = $parameter['payType'];
        $res = $signupObj -> save();
        if($res){
            //如果是在线报名,返回订单编号
            if($parameter['payType'] == 'wechat'){
                //订单号为：md5(wechat+time)
                $orderNum = 1;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    /**
     * 生成订单
     */
    function createOrder ($wechat){

    }
}
