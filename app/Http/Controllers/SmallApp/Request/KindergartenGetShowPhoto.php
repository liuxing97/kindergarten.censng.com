<?php

namespace App\Http\Controllers\SmallApp\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KindergartenGetShowPhoto extends Controller
{


    function getPhotos(Request $request){
        $kindergarten = $request -> session() -> get('kindergarten');
        $huanjingFolderPath = "./showImages/huanjing/";
        $ketangFolderPath = "./showImages/ketang/";
        $huodongFolderPath = "./showImages/huodong/";

        $huanjingPhotos = [];
        $ketangPhotos = [];
        $huodongPhotos = [];

        for ($t=1; $t<10; $t++){
            $huanjingPath = $huanjingFolderPath.$kindergarten.'-'.$t.".jpg";
            $huanjingSrc = "https://kindergarten.censng.com/showImages/huanjing/".$kindergarten.'-'.$t.".jpg";
            if(file_exists($huanjingPath)){
                array_push($huanjingPhotos,$huanjingSrc);
            }
            $ketangPath = $ketangFolderPath.$kindergarten.'-'.$t.".jpg";
            $ketangSrc = "https://kindergarten.censng.com/showImages/ketang/".$kindergarten.'-'.$t.".jpg";
            if(file_exists($ketangPath)){
                array_push($ketangPhotos,$ketangSrc);
            }
            $huodongPath = $huodongFolderPath.$kindergarten.'-'.$t.".jpg";
            $huodongSrc = "https://kindergarten.censng.com/showImages/huodong/".$kindergarten.'-'.$t.".jpg";
            if(file_exists($huodongPath)){
                array_push($huodongPhotos,$huodongSrc);
            }
        }

        $data = [
            'msg' => 'get success',
            'huanjing' => $huanjingPhotos,
            'ketang' => $ketangPhotos,
            'huodong' => $huodongPhotos,
            'time' => date('Y-m-d H:i:s')
        ];
        return $data;
    }


}
