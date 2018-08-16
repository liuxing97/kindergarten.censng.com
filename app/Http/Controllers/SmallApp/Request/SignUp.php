<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\Baby;
use App\BabyDiscount;
use App\BabyMore;
use App\BabySignup;
use App\KindergartenDiscount;
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
            'parentName','parentPhone','relationship');
        //获取信息-报名所需要的信息
        $signUpParams = $request -> only('babyName','babyClassType','payType','classTypeCost','discountId');
        //初始值
        $isHasBabyDef = false;
        $isHasSignupDef = false;

        //创建宝宝用户
        $babyObj = new Baby();
        $signupObj = new BabySignup();
        $semestarObj = new KindergartenSemester();
        //得到已开始，没有结束的学期
        $semestarObj = $semestarObj -> where('kindergarten',$kindergarten) -> where('end',NULL) -> first();
        //如果本学期已开始
        if($semestarObj){
//            dump($semestarObj);
            //本学期id是
            $semestarId = $semestarObj -> id;
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
            $isHasSignupObj = $signupObj -> where('wechat',$wechat) -> where('semestarId',$semestarId) -> get();
            //如果已存在报名记录
            if($isHasSignupObj){
                //判断要建立宝宝的名字，在报名表中已存在
                foreach($isHasSignupObj as $signupObj){
                    $name = $signupObj -> name;
                    if($signUpParams['babyName'] == $name){
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
                    $createSignupRes = $this -> createSignupData($wechat,$creatArchRes,$kindergarten,$signUpParams,$semestarId);
                    if($createSignupRes){
                        //调用创建订单，如果有使用优惠券则进行报销
                        Db::commit();
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
        $baseTableObj -> phone = $parameter['parentPhone'];
        $baseTableObj -> sex = $parameter['babySex'];
        $baseTableObj -> birthday = $parameter['birthday'];
        $baseTableObj -> parents = $parameter['parentName'];
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
    protected function createSignupData($wechat,$babyId,$kindergarten,$parameter,$semestarId){
        //报名信息
        $signupObj = new BabySignup();
        $signupObj -> name = $parameter['babyName'];
        $signupObj -> wechat = $wechat;
        $signupObj -> babyId = $babyId;
        $signupObj -> kindergarten = $kindergarten;
        $signupObj -> classType = $parameter['babyClassType'];
        $signupObj -> cost = $parameter['classTypeCost'];
        $signupObj -> semestarId = $semestarId;
        if($parameter['discountId'] != 'null' && $parameter['discountId'] != null){
//            dump($parameter);
//            dump($parameter['discountId']);
            //判断优惠券是否有效
            $discountObj = new BabyDiscount();
            $discountObj = $discountObj -> where('id',$parameter['discountId']) -> first();
            $discountObj -> isUse = 'true';
            $discountObj -> isInvalid = 'true';
            $res = $discountObj -> save();
            if($res){
                $signupObj -> discountId = $parameter['discountId'];
            }else{
                return false;
            }
        }
        $signupObj -> paytype = $parameter['payType'];
        $res = $signupObj -> save();
        if($res){
            //如果是在线报名,返回订单编号
            if($parameter['payType'] == 'wechat'){
                //订单号为：md5(wechat+time)
                $orderNum = 1;
            }else{
//                echo "123";
                return true;
            }
        }else{
            return false;
        }
    }

}
