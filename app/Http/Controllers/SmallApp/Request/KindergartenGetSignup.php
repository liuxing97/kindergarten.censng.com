<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\BabySignup;
use App\KindergartenSemester;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenGetSignup extends Controller
{
    //
    function getData(Request $request){
        $phoneOrName = $request -> input('key');
        if(preg_match("/^\d*$/",$phoneOrName)){
            //驱动为电话号码
            $drive = 'phone';
        }else{
            $drive = 'name';
        }
        //得到当前学期id
        $kindergarten = $request -> session() -> get('kindergarten');
        $semesterObj = new KindergartenSemester();
        $semesterObj = $semesterObj -> where('kindergarten',$kindergarten)
            -> where('end',null)
            -> first();
        $semesterId = $semesterObj -> id;
        //宝宝表
        $talbeObj = new BabySignup();
        if($drive == 'phone'){
            $talbeObj = $talbeObj
                -> where('phone',$phoneOrName)
                -> where('semestarId',$semesterId)
                -> first();
        }else{
            $talbeObj = $talbeObj
                -> where('name',$phoneOrName)
                -> where('semestarId',$semesterId)
                -> first();
        }
        if($talbeObj){
            $dataArray = $talbeObj -> save();
            $data = [
                'msg' => 'get success',
                'data' => $dataArray,
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            $data = [
                'msg' => 'havnt data',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }

    function getAll(Request $request){
        //得到当前学期id
//        $kindergarten = $request -> session() -> get('kindergarten');
        $kindergarten = 80000001;
        $semesterObj = new KindergartenSemester();
        $semesterObj = $semesterObj -> where('kindergarten',$kindergarten)
            -> where('end',null)
            -> first();
//        dump($semesterObj);
        $semesterId = $semesterObj -> id;
        //宝宝表
        $talbeObj = new BabySignup();
        $talbeObj = $talbeObj -> where('semestarId', $semesterId) -> simplePaginate(10);
//        dump($talbeObj);
//        dump($talbeObj -> perPage());
        $items = $talbeObj -> items();
        if($items == null){
            $data = [
                'msg' => 'havnt data',
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
//            dump($items);
            $tableArray = $talbeObj -> toArray();
            dump($tableArray);
            $data = [
                'msg' => 'get data success',
                'data' => $tableArray,
                'time' => date('Y-m-d H:i:s')
            ];
        }
//        //将得到的数据
//        dump($talbeObj);
        return $data;
    }
}




