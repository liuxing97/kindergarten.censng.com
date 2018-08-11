<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\SmallappClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonGetClass extends Controller
{
    public function get(Request $request){
        $kindergarten = $request -> session() -> get('kindergarten');
        // 得到要操作的模型
        $modelObj = new SmallappClass();
        $modelObj = $modelObj -> where('kindergarten',$kindergarten) -> get();
        $modelArray = $modelObj -> toArray();
        return $modelArray;
    }
}
