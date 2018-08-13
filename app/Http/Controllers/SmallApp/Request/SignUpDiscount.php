<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\KindergartenDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignUpDiscount extends Controller
{
    /**
     * 是否存在优惠券
     */
    public function isHas(Request $request){
        $discountObj = new KindergartenDiscount();
        $kindergarten = $request -> session() -> get('kindergarten');
        $wechat = $request -> session() -> get('openid');
        $discountObj = $discountObj
            -> where('wechat',$wechat)
            -> where('kindergarten', $kindergarten)
            -> where('type', 'signup')
            -> where('isUse','false')
            -> first();
        if($discountObj){
            $discountArray = $discountObj -> toArray();
            $data = [
                'msg' => 'has discount',
                'data' => $discountArray,
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            $data = [
                'msg' => 'no has',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }
}
