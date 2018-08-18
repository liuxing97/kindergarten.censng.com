<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\BabySignup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenConfirmSignup extends Controller
{
    //
    function confirm(Request $request){
        $confirmId = $request -> input('signupId');
        $tableObj = new BabySignup();
        $tableObj = $tableObj -> where('id',$confirmId) -> first();
        if($tableObj){
            if($tableObj -> payed == 'true'){
                $data = [
                    'msg' => 'signuped',
                    'time' => time()
                ];
            }else if($tableObj -> payed == 'false'){
                $data = [
                    'msg' => 'signuped false',
                    'time' => time()
                ];
            }else{
                $tableObj -> payed = 'true';
                $tableObj -> paytime = date('Y-m-d H:i:s');
                $res = $tableObj -> save();
                if($res){
                    $data = [
                        'msg' => 'confirm success',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }else{
                    $data = [
                        'msg' => 'confirm error',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }else{
            $data = [
                'msg' => 'sign up not find',
                'time' => time()
            ];
        }
        return $data;
    }
}