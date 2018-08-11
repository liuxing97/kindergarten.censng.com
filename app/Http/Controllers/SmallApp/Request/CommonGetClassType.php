<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\SmallappClassType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonGetClassType extends Controller
{
    /**
     * 获取班级类型
     */
    public function get(Request $request) {
        //得到幼儿园id
        $kindergarten = $request -> session() -> get('kindergarten');
        //获取要操作的模型
        $tableObj = new SmallappClassType();
        $tableObj -> where('kindergarten',$kindergarten) -> get();
        dump($tableObj);
    }
}
