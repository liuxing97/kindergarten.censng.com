<?php

namespace App\Http\Controllers\SmallApp\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenCreateClassType extends Controller
{
    //创建新类型
    public function newType(Request $request){
        //得到幼儿园id
        $kindergarten = $request -> session() -> get('kindergarten');
        $title = $request -> input('title');
        $cost = $request -> input('cost');
        dump($title);
        dump($cost);
    }
}
