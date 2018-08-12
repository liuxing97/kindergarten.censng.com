<?php

namespace App\Http\Controllers\SmallApp\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeacherCreateHomework extends Controller
{
    function saveHomework (Request $request){
        //得到要请求的驱动类型
        $drive = $request -> input("drive");
        $userObj = $request -> session() -> get('userObj');
        //得到所属幼儿园
        $kindergarten = $request -> session() -> get('kindergarten');
        $classId = $userObj -> classid;


        //根据驱动类型请求驱动
        if($drive == 'words'){
            $res = $this -> saveWords();
        }else if($drive == 'photo'){
            $res = $this -> savePhoto();
        }

    }
    /*
     * 保存文字作业布置
     */
    function saveWords() {

    }
    /**
     * 保存照片作业布置
     */
    function savePhoto(){
        if ($_FILES["homeworkPhoto"]["error"] > 0)
        {
            echo "Error: " . $_FILES["homeworkPhoto"]["error"] . "<br />";
        }
        else
        {
            echo "Upload: " . $_FILES["homeworkPhoto"]["name"] . "<br />";
            echo "Type: " . $_FILES["homeworkPhoto"]["type"] . "<br />";
            echo "Size: " . ($_FILES["homeworkPhoto"]["size"] / 1024) . " Kb<br />";
            echo "Stored in: " . $_FILES["homeworkPhoto"]["tmp_name"];
        }
    }
}
