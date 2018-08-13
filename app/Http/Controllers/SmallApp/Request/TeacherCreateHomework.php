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
//        dump($userObj);


        //根据驱动类型请求驱动
        if($drive == 'words'){
            $res = $this -> saveWords();
        }else if($drive == 'photo'){
            $res = $this -> savePhoto($classId);
        }
        return $res;
    }
    /*
     * 保存文字作业布置
     */
    function saveWords() {

    }
    /**
     * 保存照片作业布置
     */
    function savePhoto($classId){
//        echo $classId;
        //图片保存文件夹路径/public/homework/classid/
        $folderPath = "./homework/".$classId."/";
        if (!file_exists($folderPath)){
            mkdir ($folderPath,0777,true);
        } else {
        }
        //图片文件夹下名称
        $fileName = date('Y-m-d').".jpg";
        //图片保存完整路径/public/homework/classid/Y-m-d.jpg
        $filePath = $folderPath.$fileName;
//        echo $filePath; size为1000kb
        if ((($_FILES["homeworkPhoto"]["type"] == "image/gif")
                || ($_FILES["homeworkPhoto"]["type"] == "image/jpeg")
                || ($_FILES["homeworkPhoto"]["type"] == "image/pjpeg"))
            && ($_FILES["homeworkPhoto"]["size"] < 1000000))
        {
            if ($_FILES["homeworkPhoto"]["error"] > 0)
            {
                $data = [
                    'msg' => "Return Code: " . $_FILES["homeworkPhoto"]["error"],
                    'time' => date('Y-m-d H:i:s')
                ];
            }
            else
            {
                if (file_exists($filePath))
                {
                    $ret = unlink($filePath);
                    if($ret){
                        move_uploaded_file($_FILES["homeworkPhoto"]["tmp_name"],
                            $filePath);
                        $data = [
                            'msg' => 'rewrite homework success',
                            'time' => date('Y-m-d H:i:s')
                        ];
                    }else{
                        $data = [
                            'msg' => 'rewrite homework fail',
                            'time' => date('Y-m-d H:i:s')
                        ];
                    }

                }
                else
                {
                    move_uploaded_file($_FILES["homeworkPhoto"]["tmp_name"],
                        $filePath);
                    $data = [
                        'msg' => 'upload homework success',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }
        else
        {
            $data =[
                'msg' => 'type or error has fail',
                'time' => date('Y-m-d H:i:s'),
            ];
        }
        return $data;
    }
}
