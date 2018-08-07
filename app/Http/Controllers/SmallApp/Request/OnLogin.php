<?php
/**
 * Created by PhpStorm.
 * User: liuxing
 * Date: 2018-08-07
 * Time: 17:23
 */

namespace App\Http\Controllers\SmallApp\Request;


use App\Kindergarten;
use Illuminate\Support\Facades\Input;

class OnLogin
{
    public function login()
    {
        $appCode = Input::get('code');
        $appKindergarten = Input::get('kindergarten');
        dump($appKindergarten);
        //获取相对应的幼儿园配置
        $kindergartenObj = new Kindergarten();
        $kindergartenObj = $kindergartenObj -> where('kindergarten',$appKindergarten) -> first();
        if($kindergartenObj){
            $kindergartenItem = $kindergartenObj -> toArray();
            dump($kindergartenItem);
        }else{
            echo "幼儿园未查询到";
        }
    }
}