<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\BabyDiscount;
use App\Http\Controllers\SmallApp\Common\CreatQRCode;
use App\Kindergarten;
use App\KindergartenDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NodiscountData;

class DiscountsSignup extends Controller
{
    /**
     * 处理发放优惠券逻辑
     * 如果转发人和阅读人不同，判断是否已发放，如果未发放，则发放优惠券
     */
    function handleGrant(Request $request){
        //得到转发人id
        $source = $request -> input('source');
        //自己进来的，没有通过转发二维码进入
        if($source == 'self'){
            $source = $request -> session() -> get('openid');
        }
        //得到活动id
        $discountId = $request -> input('discountId');
        //得到阅读人id
        $wechat = $request -> session() -> get('openid');
        //得到所属幼儿园
        $kindergarten = $request -> session() -> get('kindergarten');




        //判断是否存在活动，没有存在活动的时候，fromTime的值可能为no time，自己进来的，也可能有时间，是失效的活动页面进入，no discount已经说明了无效，仅在最后，如果不是同一人，记录UV
        if($discountId == 'no discount'){
            //使用noDiscount驱动写UV，并给source和wechat
            $ret = $this -> UVDrive($request,'noDiscount',$source,$wechat,$kindergarten);
            if(!$ret){
                //设置返回值
                $data = [
                    'msg' => 'write uv error',
                    'time' => date('Y-m-d H:i:s')
                ];
            }else{
                //设置返回值
                $data = [
                    'msg' => 'no discount',
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }else{
            //得到所来的验证码时间参数
            $fromTime = $request -> input('fromTime');
            //判断时间是否<存在的活动创建时间
            if($fromTime == 'guyicuowu'){
                //这是过期的优惠活动
            }else{
                //使用discount驱动写UV，并给source和wechat,discountId
                $ret = $this -> UVDrive($request,'discount',$source,$wechat,$kindergarten,$discountId);
                if(!$ret){
                    //设置返回值
                    $data = [
                        'msg' => 'write uv error',
                        'time' => date('Y-m-d H:i:s')
                    ];
                }else{
                    //判断是否相同
                    if($source == $wechat){
                        //判断是否已发放
                        $tableObj = new BabyDiscount();
                        $tableObjRet = $tableObj -> where('wechat',$source) -> where('kindergarten',$kindergarten) -> where('discountId',$discountId) -> first();
                        //如果已发放
                        if($tableObjRet) {
                            $data = [
                                'msg' => 'your discount has grant',
                                'time' => date('Y-m-d H:i:s')
                            ];
                        }else {
                            $data = [
                                'msg' => 'waiting your friend',
                                'time' => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                    else{
                        //判断是否已发放
                        $tableObj = new BabyDiscount();
                        $tableObjRet = $tableObj -> where('wechat',$source) -> where('kindergarten',$kindergarten) -> where('discountId',$discountId) -> first();
                        //如果已发放
                        if($tableObjRet){
                            $data = [
                                'msg' => 'hasGrant',
                                'time' => date('Y-m-d H:i:s')
                            ];
                        }else{
                            //发放
                            $tableObj -> kindergarten = $kindergarten;
                            $tableObj -> wechat = $source;
                            $tableObj -> discountId = $discountId;
                            $ret = $tableObj -> save();
                            if($ret){
                                $data = [
                                    'msg' => 'grant success',
                                    'time' => date('Y-m-d H:i:s')
                                ];
                            }else{
                                $data = [
                                    'msg' => 'grant fail',
                                    'time' => date('Y-m-d H:i:s')
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 小程序判断是否幼儿园有活动
     */
    function isHasDiscount(Request $request){
        $kindergarten = $request -> session() -> get('kindergarten');
        //查询是否有正在进行中的活动
        $disCountObj = new KindergartenDiscount();
        $disCountObj = $disCountObj ->where('kindergarten',$kindergarten)
            -> where('purpose','signup')
            -> where('isInvalid', 'false') -> first();

        //如果没活动，返回 no discount
        if($disCountObj){
            $disCountArray = $disCountObj -> toArray();
            $data = [
                'msg' => 'has discount',
                'data' => $disCountArray,
                'time' => date('Y-m-d H:i:s')
            ];
        }else{
            $data = [
                'msg' => 'no discount',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }


    /**
     * 获取优惠券图片
     * $resultPath: public/discounts/result/幼儿园/md5(微信id+优惠券id).jpg
     *
     * @param Request $request
     * @return array
     */
    function getShowPhoto(Request $request){
        //得到幼儿园编号
        $kindergarten = $request -> session() -> get('kindergarten');
        //得到微信id
        $wechat = $request -> session() -> get('openid');
        //得到优惠券id
        $discountId = $request -> input('discountId');
        //如果不存在优惠活动
        if($discountId == 'no discount'){
            //使用默认宣传背景
            $discountPara = 'defBgImg';
            //优惠券id为参数识别有效期的改为，不改，没有优惠活动，直接用wechat作为标识，无优惠活动，即无目的性，仅园方登记某段时间内的转发次数
            //最终路径地址
            $resultPath = "/discounts/result/".$kindergarten."/".md5($wechat).'.jpg';
            $src = "https://".$_SERVER['SERVER_NAME']."/discounts/result/".$kindergarten."/".md5($wechat).'.jpg';
            $creatDrive = 'no discount';
        }else{
            //获取优惠活动详情
            $discountObj = new KindergartenDiscount();
//        dump($discountId);
            $discountObj = $discountObj -> find($discountId);
//        dump($discountObj);
            $discountPara = $discountObj -> parameter;
//        dump($discountPara);
//        exit();
            //最终路径地址
            $resultPath = "/discounts/result/".$kindergarten."/".md5($wechat.$discountId).'.jpg';
            $src = "https://".$_SERVER['SERVER_NAME']."/discounts/result/".$kindergarten."/".md5($wechat.$discountId).'.jpg';
            $creatDrive = 'has discount';
        }
        if($discountPara == 'defBgImg'){
            //得到使用的哪种宣传背景
            $drive = 'def';
        }
        else{
            $drive = 'a';
        }

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
//            dump("not has");
            //调用创建二维码
            $creatQRCodeObj = new CreatQRCode();
            $ret = $creatQRCodeObj -> newDiscountQRCode($request,$creatDrive);
            if($ret){
                //合成图片
                $res = $this->composeImg($request,$drive,$creatDrive);
                if($res){
                    //返回图片地址
                    $data = [
                        'msg' => 'return QRCode success',
                        'src' => $src,
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
    function composeImg(Request $request,$type = 'def',$creatDrive,$newW = 465,$newX=308,$newY=285){
        //得到幼儿园编号
        $kindergarten = $request -> session() -> get('kindergarten');
        //得到微信id
        $wechat = $request -> session() -> get('openid');
        if($creatDrive == 'no discount'){

        }else{
            //得到优惠券id
            $discountId = $request -> input('discountId');
        }
        //根据驱动选择背景图片地址
        if($type == 'def') {
            $bgPath = './discounts/source/bg1.jpg';
        }else{
            $bgPath = '';
        }

        if($creatDrive == 'no discount'){
            $qrPath = './discounts/QRCode/'.$kindergarten.'/'.md5($wechat).'.jpg';
        }else{
            $qrPath = './discounts/QRCode/'.$kindergarten.'/'.md5($wechat.$discountId).'.jpg';
        }
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
        if($creatDrive == 'no discount'){
            $filename=md5($wechat).".jpg";///要生成的图片名字 md5(appid).jpg
        }else{

            $filename=md5($wechat.$discountId).".jpg";///要生成的图片名字 md5(appid).jpg
        }

        imagejpeg($dst,$imgDir.$filename);
//        dump(file_exists($imgDir));
//        $xmlstr = $dst;
//        if(empty($xmlstr)) {
//            $xmlstr = file_get_contents('php://input');
//        }
//
//        $jpg = $xmlstr;//得到post过来的二进制原始数据
//        if(empty($jpg))
//        {
//            echo 'nostream';
//            exit();
//        }
//
//        $file = fopen("".$imgDir.$filename,"w");//打开文件准备写入
//        fwrite($file,$jpg);//写入
//        fclose($file);//关闭

        $filePath = './'.$imgDir.$filename;

        //图片是否存在
        if(!file_exists($filePath))
        {
            return false;
        }
        return $filePath;
    }

    /**
     * UV驱动
     * 使用discount驱动写UV，并给source和wechat,discountId
     * noDiscount驱动写UV，并给source和wechat
     */
    function UVDrive(Request $request,$drive,$source,$wechat,$kindergarten,$discountId = null){
        //如果是自己访问，不进行记录
        if($source == $wechat){
            return true;
        }else{
            //判断驱动
            if($drive == 'noDiscount'){
               // 写入最近的无活动转发登记表中
                $tableObj = new NodiscountData();
                $tableObj = $tableObj -> where('kindergarten', $kindergarten) -> where('isZero',null) -> first();
                $uv = $tableObj -> uv;
                $tableObj -> uv = ++$uv;
                $ret = $tableObj -> save();
                $pvType = 'tempPv';
            }else{
                //写入活动补充表中
                $tableObj = new KindergartenDiscount();
                $tableObj = $tableObj -> where('id', $discountId) -> first();
//                dump($tableObj);
                $uv = $tableObj -> uv;
                $tableObj -> uv = ++$uv;
                $ret = $tableObj -> save();
                $pvType = 'discount';
            }
            //记录以后PV应该存到那个数据库的哪条数据中
            $request -> session() -> put('pvType',$pvType);
            $request -> session() -> put('pvId',$tableObj ->id);
            if($ret){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * 处理PV
     * writePv
     */
    function writePv(Request $request){
        $pvType = $request -> session() -> get('pvType');
//        dump($pvType);
        $pvId = $request -> session() -> get('pvId');
        if($pvType){
            if($pvType == 'discount'){
                $tableObj = new KindergartenDiscount();
                $tableObj = $tableObj -> where('id',$pvId)-> first();
            }else{
                $tableObj = new NodiscountData();
                $tableObj = $tableObj -> where('id',$pvId) -> first();
            }
            $pv = $tableObj -> pv;
            $tableObj -> pv = ++$pv;
            $ret = $tableObj -> save();
            if($ret){
                $data = [
                    'msg' => 'pv success',
                    'time' => date('Y-m-d H:i:s')
                ];
            }else{
                $data = [
                    'msg' => 'pv fail',
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }else{
            $data = [
                'msg' => 'dont need write pv',
                'time' => date('Y-m-d H:i:s')
            ];
        }
        return $data;
    }
}
