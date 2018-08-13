<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\Http\Controllers\SmallApp\Common\CreatQRCode;
use App\KindergartenDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountsSignup extends Controller
{
    /**
     * 获取优惠券图片
     * $resultPath: public/discounts/result/幼儿园/md5(微信id+优惠券id).jpg
     *
     * @param Request $request
     * @return array
     */
    function getShowPhoto(Request $request){
        $appid = $request -> session() -> get('appid');
        //得到幼儿园编号
        $kindergarten = $request -> session() -> get('kindergarten');
        //得到微信id
        $wechat = $request -> session() -> get('openid');
        //得到优惠券id
        $discountId = $request -> input('discountId');
        //获取优惠活动详情
        $discountObj = new KindergartenDiscount();
        $discountObj = $discountObj -> find($discountId);
        $discountPara = $discountObj -> paramater;
        if($discountPara == 'defBgImg'){
            //得到使用的哪种宣传背景
            $drive = 'def';
        }
        else{
            $drive = 'a';
        }
        //最终路径地址
        $resultPath = "/discounts/result/".$kindergarten."/".md5($wechat.$discountId).'.jpg';
        $src = "https://".$_SERVER['SERVER_NAME']."/discounts/result/".$kindergarten."/".md5($wechat.$discountId).'.jpg';
        //判断是否已存在用户的优惠图片
        if(file_exists($resultPath)){
//            dump('has');
            //返回图片地址
            $data = [
                'msg' => 'return QRCode success',
                'src' => $src,
                'time' => date('Y-m-d H:i:s')
            ];
        }else {
            //调用创建二维码
            $creatQRCodeObj = new CreatQRCode();
            $ret = $creatQRCodeObj -> newDiscountQRCode($request);
            if($ret){
                //合成图片
                $res = $this->composeImg($drive);
                if($res){
                    //返回图片地址
                    $data = [
                        'msg' => 'return QRCode success',
                        'src' => $res,
                        'time' => date('Y-m-d H:i:s')
                    ];
                }else{
                    //图片合成失败
                    $data = [
                        'msg' => 'image synthesis failure',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }

            }else{
                //创建二维码失败
                $data = [
                    'msg' => 'return QRCode fail',
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }
        return $data;
    }

    /**
     * 合成图片，成功后返回路径，失败返回false
     * @param string $type
     * @param int $newW
     * @param int $newX
     * @param int $newY
     * @param Request $request
     */
    function composeImg($type = 'def',$newW = 465,$newX=308,$newY=285,Request $request){
        //得到幼儿园编号
        $kindergarten = $request -> session() -> get('kindergarten');
        //得到微信id
        $wechat = $request -> session() -> get('openid');
        //得到优惠券id
        $discountId = $request -> input('discountId');
        //根据驱动选择背景图片地址
        if($type == 'def') {
            $bgPath = './discounts/source/bg1.jpg';
        }else{
            $bgPath = '';
        }
        $qrPath = './discounts/QRCode/'.$kindergarten.'/'.md5($wechat.$discountId).'.jpg';
        $dst_path = $bgPath;
        $src_path = $qrPath;

        //创建图片的实例
        $dst = imagecreatefromstring(file_get_contents($dst_path));
        $src = imagecreatefromstring(file_get_contents($src_path));

        //获取水印图片的宽高
        list($src_w, $src_h) = getimagesize($src_path);
        //获取底片宽高
        list($dst_w,$dst_h) = getimagesize($dst_path);
        //将水印加入
        if($type == 'def'){
            imagecopyresized($dst, $src, $newX, $newY, 0, 0, $newW, $newW, $src_w,$src_h);
        }else{
            imagecopyresized($dst, $src, $newX, $newY, 0, 0, $newW, $newW, $src_w,$src_h);
        }
        //生成图片
        $imgDir = "./discounts/result/".$kindergarten.'/';
        //文件命名规则：md5(appid+classId)
        $filename=md5($wechat.$discountId).".jpg";///要生成的图片名字 md5(appid).jpg
//        dump(file_exists($imgDir));
        $xmlstr = $dst;
        if(empty($xmlstr)) {
            $xmlstr = file_get_contents('php://input');
        }

        $jpg = $xmlstr;//得到post过来的二进制原始数据
        if(empty($jpg))
        {
            echo 'nostream';
            exit();
        }

        $file = fopen("".$imgDir.$filename,"w");//打开文件准备写入
        fwrite($file,$jpg);//写入
        fclose($file);//关闭

        $filePath = './'.$imgDir.$filename;

        //图片是否存在
        if(!file_exists($filePath))
        {
            return false;
        }
        return $filePath;
    }
}
