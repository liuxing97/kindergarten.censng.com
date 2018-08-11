<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\SmallappClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenCreateClass extends Controller
{
    /**
     * 创建班级
     */
    public function createClass(Request $request) {
        $kindergarten = $request -> session() -> get('kindergarten');
        $classType = $request -> input('classType');
        $className = $request -> input('className');
        //得到要操作的表对象
        $tableObj = new SmallappClass();
        $tableObj -> kindergarten = $kindergarten;
        $tableObj -> classType = $classType;
        $tableObj -> className = $className;
        $res = $tableObj -> save();
        if($res){
            $data = [
                'msg' => 'create class success',
                'time' => date("Y-m-d H:i:s")
            ];
        }else{
            $data = [
                'msg' => 'create class fail',
                'time' => date("Y-m-d H:i:s")
            ];
        }
        return $data;
    }
}
