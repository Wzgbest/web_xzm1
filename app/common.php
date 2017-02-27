<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
use app\huanxin\model\UserCorporation;
use app\huanxin\model\Occupation;
use app\huanxin\model\CorporationStructure;

// 应用公共文件

//Db::listen( function ($sql, $time, $explain) {
//    echo $sql.' [execute time '.$time.'s]';
////    dump($explain);
//});

/**
 * 验证用户名格式
 * @param $tel
 * @return int
 */
function check_tel ($tel) {
    return preg_match('/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}/',$tel);
}

/**
 * 整合职位id和职位名称，批量获取用户信息时使用
 * @param $friendsInfo 用户信息二维数组
 * @param $corp_id 公司代号
 * @return array
 */
function get_occupation_name ($friendsInfo,$corp_id) {
    $occuM = new Occupation($corp_id);
    $occus = $occuM->getAllOccupations();
    foreach ($occus as $key => $val) {
        foreach ($friendsInfo as $k => $v) {
            if ($v['occupation'] == $val['id']) {
                $friendsInfo[$k]['occupation'] =$val['occu_name'];
            }
        }
    }
    return $friendsInfo;
}

/**
 * 整合部门id合部门名称，批量获取用户信息时使用
 * @param $friendsInfo
 * @param $corp_id
 * @return mixed
 */
function get_struct_name ($friendsInfo,$corp_id) {
    $structM = new CorporationStructure($corp_id);
    $structs = $structM->getAllStructure();
    foreach ($structs as $key => $val) {
        foreach ($friendsInfo as $k => $v) {
            if ($v['structid'] == $val['id']) {
                $friendsInfo[$k]['structid'] =$val['struct_name'];
            }
        }
    }
    return $friendsInfo;
}

/**
 * 权限验证
 * @param $rule 权限名称 string
 * @param $uid 用户id
 * @return bool
 */
function check_auth ($rule,$uid) {
    $corp_id = session('corp_id');$corp_id ='sdzhongxun';//TODO,更改
    $auth = new \myvendor\Auth($corp_id);
    if (!$auth->check($rule,$uid)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 短信发送公用模块
 * @param $phone   手机号码
 * @param $type    发送类型：1，发送验证码
 * @param $content  若为验证码，则为发送验证码;若为创建站点/删除/到期等，则为站点名称；若为6，则自己填写。
 */
function sendSms($phone, $type, $content = null){
    $User = config('sms_workid');
    $Pass = config('sms_workpass');

    $url = "http://smshttp.k400.cc/SendSMS.aspx?User=" . $User . "&Pass=" . $Pass . "&Destinations="
        . $phone . "&Content=" . $smsContent;
    $data = file_get_contents($url);
    $data = json_decode($data,true);
    if($data['MsgID']){
        return true;
    }else{
        $content = '手机号' . $phone . '短信发送失败，原因为：' . $data['Result'];
        sendMail('wangqiwen@winbywin.com', '中迅建站系统短信问题', $content, '自己');
        return false;
    }
}

/**
 * 云径短信平台发送手机验证码
 * @param $tel
 * @param $code
 * @param $content
 * @return array
 */
function send_sms ($tel,$code,$content) {
    $user = config('sms_workid');
    $pass = config('sms_workpass');
    $url = "http://smshttp.k400.cc/SendSMS.aspx?User=" . $user . "&Pass=" . $pass . "&Destinations=". $tel . "&Content=" . $content;
    $data = file_get_contents($url);
    $data = json_decode($data,true);
    $data['MsgID']=true;//测试开启
    if ($data['MsgID']) {
        session('reset_code'.$tel,$code);
        return ['status'=>true];
    } else {
        $content = '手机号'.$tel.'发送信息失败，原因为：'.$data['Result'];
        return ['status'=>false,'message'=>$content];
    }
}

/**
 * 获取公司id
 * @param $tel
 * @return bool|mixed|string
 */
function get_corpid ($tel) {
    if (session('corp_id'.$tel)) {
        return session('corp_id'.$tel);
    }
    $corp_id = UserCorporation::getUserCorp($tel);
    if (empty($corp_id)) {
        return false;
    } else {
        return $corp_id;
    }
}

/**
 * 处理app端传来图像文件
 * @param $data
 * @return mixed|string
 */
function get_app_img ($data) {
    $img_path = config('upload_image.image_path');
    $data = base64_decode($data);
    $res['status'] = false;
    try{
        $img_path = $img_path.date('Y-m-d',time());//相对路径
        $save_path = config('base_path').$img_path;//物理路径
        if (!is_dir($save_path)) {
            mkdir($save_path,0755);
        }
        $img_path = $img_path.'/'.time().rand(10000,99999).'.tmp';//相对路径文件
        $save_path = config('base_path').$img_path;//物理路径文件
        file_put_contents($save_path,$data);
        $arr=getimagesize($save_path);
        $img_type = explode(',',config('upload_image.image_ext'));
        $img_ext = '';
        foreach ($img_type as $val) {
            if (false !== strpos($arr['mime'],$val)) {
                $img_ext = $val;
                break;
            }
        }
        $img_path = substr($img_path,0,-3).$img_ext;
        $new_save_path = substr($save_path,0,-3).$img_ext;
        rename($save_path,$new_save_path);
        $res = ['imgurl' => $img_path,'status'=>true];
    } catch(\Exception $e){
         $res['message'] = '存储头像失败，联系管理员';
    }
    return $res;
}