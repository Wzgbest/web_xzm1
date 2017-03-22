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
use app\common\model\UserCorporation;
use app\common\model\Occupation;
use app\common\model\CorporationStructure;
use app\common\model\Umessage;
use app\common\model\Employer;

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
 * 验证填写支付宝账号格式
 * @param $alipay
 * @return bool
 */
function check_alipay_account ($alipay) {
    if (preg_match('/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}/',$alipay)) {
        return true;
    } elseif (preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i',$alipay)) {
        return true;
    }
    return false;
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
 * 发送邮件
 * @param $to_user 接收方
 * @param $title  主题
 * @param string $content 内容
 * @param array $attachment['path','name'] 附件['地址','附件名称']
 * @return bool
 * @throws Exception
 * @throws phpmailerException
 */
function send_mail ($to_user, $title, $content='',$attachment=array()) {
    $mail = new PHPMailer(true);
    $mail->IsSMTP();                  // send via SMTP
    $mail->Host = config('system_email.host');   // SMTP servers
    $mail->SMTPAuth = true;           // turn on SMTP authentication
    $mail->Username = config('system_email.user');     // SMTP username  注意：普通邮件认证不需要加 @域名
    $mail->Password = config('system_email.pass'); // SMTP password
    $mail->From = config('system_email.user');      // 发件人邮箱
    $mail->FromName =  config('system_email.from_name');  // 发件人称呼
//    $mail->SMTPSecure = 'ssl';
//    $mail->Port = 465;
    $mail->CharSet = "UTF-8";   // 这里指定字符集！
    $mail->AddAddress($to_user);  // 收件人邮箱和姓名
    //$mail->WordWrap = 50; // set word wrap 换行字数
    if (!empty($attachment)) {
        if (isset($attachment['name'])) {
            $mail->AddAttachment($attachment['path'], $attachment['name']);
        } else {
            $mail->AddAttachment($attachment['path']);
        }
    }
    $mail->IsHTML(true);  // send as HTML
    $mail->Subject = $title;
    $mail->Body = $content;
    $status = $mail->send();
    return $status;
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
    $data['MsgID']=true;//TODO 测试开启
    if ($data['MsgID']) {
        session('reset_code'.$tel,$code);
        return ['status'=>true];
    } else {
        $content = '手机号'.$tel.'发送信息失败，原因为：'.$data['Result'];
        send_mail('wangqiwen@winbywin.com', '通信项目短信问题', $content);
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
        session('corp_id'.$tel,$corp_id);
        return $corp_id;
    }
}

/**
 * 通过手机号获取用户id
 * @param $tel
 * @param string $corp_id
 * @return int
 */
function get_userid_from_tel ($tel,$corp_id='') {
    if (empty($corp_id)) {
        $corp_id = get_corpid($tel);
    }
    $employM = new Employer($corp_id);
    $users = $employM->getEmployer($tel);
    return $users['id'];
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
        $save_path = PUBLIC_PATH.$img_path;//物理路径
        if (!is_dir($save_path)) {
            mkdir($save_path,0755);
        }
        $img_path = $img_path.'/'.time().rand(10000,99999).'.tmp';//相对路径文件
        $save_path = PUBLIC_PATH.$img_path;//物理路径文件
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

/**
 * 记录操作
 * @param $userid 用户id非tel
 * @param $type　类型
 * @param $remark　标识
 * @param string $corp_id　公司代号
 * @return int|string
 */
function write_log ($userid,$type,$remark,$corp_id='') {
    if (empty($corp_id)) {
        $corp_id = get_corpid($userid);
    }
    $u = new Umessage($corp_id);
    $data = [
        'userid'=>$userid,
        'type' =>$type,
        'remark'=>$remark,
        'create_time'=>time()
    ];
    return $u->addUmessage($data);
}

//function rand_bonus($bonus_total=0, $bonus_count=3, $bonus_type=1){
//    $bonus_items  = array(); // 将要瓜分的结果
//    $bonus_balance = $bonus_total; // 每次分完之后的余额
//    $bonus_avg   = number_format($bonus_total/$bonus_count, 2); // 平均每个红包多少钱
//    $i       = 0;
//    while($i<$bonus_count){
//        if($i<$bonus_count-1){
//            $rand      = $bonus_type?(rand(1, $bonus_balance*100-1)/100):$bonus_avg; // 根据红包类型计算当前红包的金额
//            $bonus_items[] = $rand;
//            $bonus_balance -= $rand;
//        }else{
//            $bonus_items[] = $bonus_balance; // 最后一个红包直接承包最后所有的金额，保证发出的总金额正确
//        }
//        $i++;
//    }
//    return $bonus_items;
//}

/**
 * 生成随机红包
 * @param $total 总金额 单位元，3.33
 * @param $num  个数
 * @param float $min 最小红包金额
 * @return array
 */
function get_red_bonus ($total,$num,$min=0.01) {
    $arr= array();
    for ($i=1;$i<$num;$i++) {
        $safe_total=($total-($num-$i)*$min)/($num-$i);
        $safe_total = $safe_total<$min ?$min:$safe_total;
        $money=mt_rand($min*100,$safe_total*100)/100;
        $money=number_format($money, 2, '.', '');
        $total=$total-$money;
        $arr[].=$money;
    }
    $arr[].=number_format($total, 2, '.', '');
    shuffle($arr);
    return $arr;
}

