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
use app\common\model\CorporationStructure;
use app\common\model\Umessage;
use app\common\model\Employer;

// 应用公共文件


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
    $corp_id = session('corp_id');
    $corp_id ='sdzhongxun';//TODO,测试开启
    $auth = new \myvendor\Auth($corp_id);
    if (!$auth->check($rule,$uid)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 发送邮件
 * @param $sender 发件人邮箱
 * @param $pass 发件人邮箱密码
 * @param $to_user 接收人邮箱
 * @param $title  主题
 * @param $sender_nick 发件人昵称
 * @param string $content 内容
 * @param array $attachment['path','name'] 附件['地址','附件名称']
 * @return bool
 * @throws Exception
 * @throws phpmailerException
 */
function send_mail ($sender,$pass,$to_user, $title, $sender_nick='',$content='',$attachment=array()) {
    $mail = new PHPMailer(true);
    $mail->IsSMTP();                  // send via SMTP
    $mail->Host = get_mail_smtp($sender);   // SMTP servers
    $mail->SMTPAuth = true;           // turn on SMTP authentication
    $mail->Username = $sender;     // SMTP username  注意：普通邮件认证不需要加 @域名
    $mail->Password = $pass; // SMTP password
    $mail->From = $sender;      // 发件人邮箱
    $mail->FromName =  $sender_nick;  // 发件人称呼
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
        send_mail(config('system_email.user'),config('system_email.pass'),'wangqiwen@winbywin.com', '通信项目短信问题',config('system_email.from_name'), $content);
        return ['status'=>false,'message'=>$content];
    }
}

/**
 * 获取公司id代号
 * @param $tel
 * @return bool|mixed|string
 */
function get_corpid () {
    $userinfo = session('userinfo');
    if (empty($userinfo)) {
        return false;
    }
    if (!empty($userinfo['corp_id'])) {
        return $userinfo['corp_id'];
    } else {
        return false;
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
        if ($img_ext == '') {
          $res['message'] = '未能识别上传图像格式，联系管理员';
        } else {
            $img_path = substr($img_path,0,-3).$img_ext;
            $new_save_path = substr($save_path,0,-3).$img_ext;
            rename($save_path,$new_save_path);
            $res = ['imgurl' => $img_path,'message' =>'SUCCESS','status'=>true];
        }
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


/**
 * 生成随机红包
 * @param $total 总金额 单位元，3.33
 * @param $num  个数
 * @param $redtype 红包类型 1运气红包  2普通红包
 * @param float $min 最小红包金额
 * @return array
 */
function get_red_bonus ($total,$num,$redtype,$min=0.01) {
    $arr= array();
    if ($redtype == 2) {
        $each_money = $total/$num;
        for($i =0;$i<$num;$i++){
            $arr[$i] = number_format($each_money,2,'.','');
        }
    } elseif ($redtype ==1) {
        for ($i=1;$i<$num;$i++) {
            $safe_total=($total-($num-$i)*$min)/($num-$i);
            $safe_total = $safe_total<$min ?$min:$safe_total;
            $money=mt_rand($min*100,$safe_total*100)/100;
            $money=number_format($money, 2, '.', '');
            $total=$total-$money;
            $arr[].=$money;
        }
        $arr[].=number_format($total, 2, '.', '');
    }
    shuffle($arr);
    return $arr;
}

/**
 * 获取邮箱smtp服务器地址
 * @param $email 邮件地址
 * @param $email_arr 邮箱smtp数组
 * @return bool
 */
function get_mail_smtp ($email,$email_arr=null) {
    if (false ===filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    if (empty($email_arr)) {
        $email_arr = cache('email_smtp');
        if (empty($email_arr)) {
            $email_arr=Db::name('email_smtp')->select();
            cache('email_smtp',$email_arr);
        }
    }
    $host = explode('@',$email)[1];
    if(!empty($host)){
        $result=[];
        exec('nslookup -type=MX '.escapeshellcmd($host), $result);
        $mx = $result[4];
        foreach ($email_arr as $key => $val ) {
            if (strpos($mx,$val['email_preg'])!==false) {
                $smtp = $val['smtp_server'];
                break;
            }
        }
        return isset($smtp)?$smtp:false;
    }
    return false;
}

/**
 * 对邮箱密码进行加密
 * @param $input 原密码
 * @return string
 */
function encrypt_email_pass ($input) {
    $key = md5_file('/project/online_update.tar');//TODO 修改为实际
    $input = str_replace("\n", "", $input);
    $input = str_replace("\t", "", $input);
    $input = str_replace("\r", "", $input);
    $key = substr(md5($key), 0, 24);
    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $encrypted_data = mcrypt_generic($td, $input);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return trim(chop(base64_encode($encrypted_data)));
}

/**
 * 对邮箱密码进行解密
 * @param $input 密文
 * @return string
 */
function decrypt_email_pass ($input) {
    $key = md5_file('/project/online_update.tar');//TODO 修改为实际
    $input = str_replace("\n", "", $input);
    $input = str_replace("\t", "", $input);
    $input = str_replace("\r", "", $input);
    $input = trim(chop(base64_decode($input)));
    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
    $key = substr(md5($key), 0, 24);
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $decrypted_data = mdecrypt_generic($td, $input);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return trim(chop($decrypted_data));
}
/**
 * curl并发测试
 * @param $urls
 * @param $delay
 * @return array
 */
function rolling_curl($urls, $delay) {
    $queue = curl_multi_init();
    $map = array();
    foreach ($urls as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_multi_add_handle($queue, $ch);
        $map[(string) $ch] = $url;
    }

    $responses = array();
    do {
        while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;
        if ($code != CURLM_OK) { break; }
        while ($done = curl_multi_info_read($queue)) {
            /**
             * $done
             * array (size=3)
                    'msg' => int 1
                    'result' => int 0
                    'handle' => resource(47, curl)
             */
            $info = curl_getinfo($done['handle']);
            $error = curl_error($done['handle']);
//            dump(curl_multi_getcontent($done['handle']), $delay);exit;
            $results[] = curl_multi_getcontent($done['handle']);
            $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results');
            curl_multi_remove_handle($queue, $done['handle']);
            curl_close($done['handle']);
        }
        if ($active > 0) {
            curl_multi_select($queue, 0.5);
        }
    } while ($active);
    curl_multi_close($queue);
    return $responses;
}