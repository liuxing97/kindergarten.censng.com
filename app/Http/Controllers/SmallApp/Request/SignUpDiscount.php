<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\BabyDiscount;
use App\KindergartenDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignUpDiscount extends Controller
{
    /**
     * 是否存在优惠券
     */
    public function isHasDiscount(Request $request){
        $discountObj = new BabyDiscount();
        $kindergarten = $request -> session() -> get('kindergarten');
        $wechat = $request -> session() -> get('openid');
        $isUse = 'false';
        $isInvalid = 'false';
        //得到目前幼儿园正在举行的关于signup报名的活动
        $kindergartenDiscountObj = new KindergartenDiscount();
        $kindergartenDiscountObj = $kindergartenDiscountObj
            -> where('kindergarten',$kindergarten)
            -> where('purpose', 'signup')
            -> where('isInvalid',$isInvalid)
            -> first();
        if(!$kindergartenDiscountObj){
            //幼儿园没有举办活动
            $data = [
                'msg' => 'kindergarten havnt discount',
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            $kindergartenDiscountArray = $kindergartenDiscountObj -> toArray();
//            dump($kindergartenDiscountObj);
            $discountId = $kindergartenDiscountObj -> id;
            $babyDiscountObj = new BabyDiscount();
            $babyDiscountObj = $babyDiscountObj
                -> where('wechat',$wechat)
                -> where('kindergarten',$kindergarten)
                -> where('discountId',$discountId)
                -> where('isInvalid',$isInvalid)
                -> where('isUse',$isUse)
                -> first();
            if($babyDiscountObj){
//                dump($babyDiscountObj);
                $babyDiscountArray = $babyDiscountObj -> toArray();
//                dump($babyDiscountArray);
                //有适应的优惠券
                $data = [
                    'msg' => 'user has discount',
                    'data' => $babyDiscountArray,
                    'sourcedata' => $kindergartenDiscountArray,
                    'time' => date('Y-m-d H:i:s')
                ];
            }else{
                //没有适应的优惠券
                $data = [
                    'msg' => 'user havnt discount',
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }
        return $data;
    }
}
