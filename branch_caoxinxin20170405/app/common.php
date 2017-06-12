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
use app\common\model\Structure as StructureModel;
use app\common\model\StructureEmployer;
use app\crm\model\CustomerTrace;
use app\common\model\ImportFile as FileModel;

// 应用公共文件


/**
 * 验证用户名格式
 * @param $tel
 * @return int
 * created by messhair
 */
function check_tel ($tel) {
    return preg_match('/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}/',$tel);
}

/**
 * 验证填写支付宝账号格式
 * @param $alipay
 * @return bool
 * created by messhair
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
 * 权限验证
 * @param $rule 权限名称 string
 * @param $uid 用户id
 * @return bool
 * created by messhair
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
 * created by messhair
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
 * created by messhair
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
 * created by messhair
 */
function get_corpid ($tel = null) {
    $userinfo = session('userinfo');
    if (!empty($userinfo['corp_id'])) {
        return $userinfo['corp_id'];
    }
    if (!is_null($tel)) {
        $corp_id = UserCorporation::getUserCorp($tel);
        //session('userinfo',['corp_id'=>$corp_id]);
        return $corp_id;
    }
    return false;
}

function getStructureIds($user_id = null){
    $userinfo = session('userinfo');
    if (!empty($userinfo['structure_ids'])) {
        return $userinfo['structure_ids'];
    }
    if (!is_null($user_id)) {
        $structureEmployer = new StructureEmployer();
        $struct_ids = $structureEmployer->getStructIdsByEmployer($user_id);
        //session('userinfo',['corp_id'=>$corp_id]);
        return $struct_ids;
    }
    return false;
}

function getCommStatusArr($comm_status){
    $comm_status_arr = [];
    switch ($comm_status){
        case 1:
            $comm_status_arr=[
                "tend_to"=>1,
                "phone_correct"=>1,
                "profile_correct"=>1,
                "call_through"=>1,
                "is_wait"=>1,
            ];
            break;
        case 2:
            break;
    }
    return $comm_status_arr;
}

/**
 * 通过手机号获取用户id
 * @param $tel
 * @param string $corp_id
 * @return int
 * created by messhair
 */
function get_userid_from_tel ($tel,$corp_id='') {
    if (empty($corp_id)) {
        $corp_id = get_corpid($tel);
    }
    $employM = new Employer($corp_id);
    $users = $employM->getEmployerByTel($tel);
    return $users['id'];
}
/**
 * 处理app端传来图像文件
 * @param $data
 * @return mixed|string
 * created by messhaira
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
 * created by messhair
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
 * 修改客户信息
 * @param $userid 操作人id
 * @param $customerid 客户id
 * @param $remark 备注
 * @param null $saleid 销售机会id
 * @return array
 * created by messhair
 */
function write_customer_log ($userid,$customerid,$remark,$saleid=null) {
    if (is_array($customerid)) {
        $data = [];
        foreach ($customerid as $k=>$v) {
            $data[] .= [
                'customer_id' =>$v,
                'operator_id' => $userid,
                'create_time'  => time(),
                'sale_id' => $saleid,
                'remark' => $remark
            ];
        }
        $cus_traceM = new CustomerTrace();
        $res =$cus_traceM->addMultipleCustomerMessage($data);
    } else {
        $data = [
            'customer_id' =>$customerid,
            'operator_id' => $userid,
            'create_time'  => time(),
            'sale_id' => $saleid,
            'remark' => $remark
        ];
        $cus_traceM = new CustomerTrace();
        $res = $cus_traceM->addSingleCustomerMessage($data);
    }
    if ($res > 0) {
        return [
            'status'=>true,
            'message'=>'记录客户信息成功'
        ];
    } else {
        return [
            'status'=>false,
            'message'=>'记录客户信息失败'
        ];
    }
}

/**
 * 生成随机红包
 * @param $total 总金额 单位元，3.33
 * @param $num  个数
 * @param $redtype 红包类型 1运气红包  2普通红包
 * @param float $min 最小红包金额
 * @return array
 * created by messhair
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
 * created by messhair
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
 * created by messhair
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
 * created by messhair
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
 * 依次列出部门下所有部门id
 * @param $arr 存放所有部门信息
 * @param $id 初始部门id
 * @param array $new_arr
 * @return array
 * created by messhair
 */
function deep_get_ids ($arr,$id,$new_arr=[]) {
    foreach ($arr as $key => $val) {
        if ($val['struct_pid'] ==$id) {
            $new_arr[] .= $val['id'];
            unset($arr[$key]);
            $new_arr = deep_get_ids($arr,$val['id'],$new_arr);
        } else {
            continue;
        }
    }
    return $new_arr;
}
/**
 * curl并发测试
 * @param $urls
 * @param $delay
 * @return array
 * created by messhair
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

/**
 * 友好的输出值的代码
 * @param $val mixed 要输出代码的值名称
 * @param $valName string 名称标记
 * @param $exit boolean 是否退出程序
 * @param $hr boolean 是否显示分割线
 * @return string 返回字符串内容或直接退出
 * created by blu10ph
 * */
function var_exp($val,$valName='',$exit=false,$hr=true){
    $str = '';
    if($hr){
        $str .= "\r\n<hr/>\r\n";
    }
    if(!empty($valName)){
        $str .= $valName.':';
    }
    $str .= var_export($val, true);
    if($exit == 'return'){
        return $str;
    }else{
        echo $str;
        if($exit!=false){
            exit;
        }
    }
}

/**
 * 创建多级目录
 * @param $dir string 要创建的路径,可多级
 * @return boolean
 * created by blu10ph
 */
function mkdirs($dir) {
    if(!is_dir($dir)) {
        if (!mkdirs(dirname($dir))){
            return false;
        }
        if(!mkdir($dir,0777)){
            return false;
        }
    }
    return true;
}

/**
 * 读取上传的Excel文件的表头
 * @param $attach_id integer 要文件id
 * @param $column_num int 列最大数量
 * @return array 内容数组
 * created by blu10ph
 */
function getHeadFormExcel($attach_id, $column_num=0) {
    $attach_id = intval ( $attach_id );
    $Line = array(
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
    );
    $column_num = intval ( $column_num );
    $res = array (
        'status' => 0,
        'data' => ''
    );
    if($column_num>count($Line)){
        $res ['data'] = '列数超出处理范围！';
        return $res;
    }
    if (empty ( $attach_id ) || ! is_numeric ( $attach_id )) {
        $res ['data'] = '上传文件ID无效！';
        return $res;
    }
    $fileModel = new FileModel(get_corpid());
    $file = $fileModel->get(1,0,['id'=>$attach_id]);
    //var_exp($file,'$file');
    if(!$file){
        $res ['data'] = '读取文件记录失败！';
        return $res;
    }
    $filename = $file ['savepath'] . DS . $file ['savename'];
    //var_exp($filename,'$filename');
    if (! file_exists ( $filename )) {
        $res ['data'] = '上传的文件读取失败';
        return $res;
    }
    $extend = $file['ext'];
    if (! ($extend == 'xls' || $extend == 'xlsx' || $extend == 'csv')) {
        $res ['data'] = '文件格式不对，请上传xls,xlsx格式的文件';
        return $res;
    }

    vendor ( 'PHPExcel' );
    vendor ( 'PHPExcel.PHPExcel_IOFactory' );
    vendor ( 'PHPExcel.Reader.Excel5' );

    switch (strtolower ( $extend )) {
        case 'csv' :
            $format = 'CSV';
            $objReader = \PHPExcel_IOFactory::createReader ( $format )->setDelimiter ( ',' )->setInputEncoding ( 'GBK' )->setEnclosure ( '"' )->setLineEnding ( "\r\n" )->setSheetIndex ( 0 );
            break;
        case 'xls' :
            $format = 'Excel5';
            $objReader = \PHPExcel_IOFactory::createReader ( $format );
            break;
        default :
            $format = 'Excel2007';
            $objReader = \PHPExcel_IOFactory::createReader ( $format );
    }

    $objPHPExcel = $objReader->load ( $filename );
    $objPHPExcel->setActiveSheetIndex ( 0 );
    $sheet = $objPHPExcel->getSheet ( 0 );
    $allColumn = $sheet->getHighestColumn (); // 取得总列数
    $column_num = $column_num?:PHPExcel_Cell::columnIndexFromString($allColumn);
    $column_num = ($column_num>count($Line))?count($Line):$column_num;
    $j = 1;
    $result = array ();
    for ( $k=0;$k<$column_num;$k++ ) {
        $result [$k] = trim ( ( string ) $objPHPExcel->getActiveSheet ()->getCell ( $Line[$k] . $j )->getValue () );
    }
    $res ['status'] = 1;
    $res ['data'] = $result;
    return $res;
}

/**
 * 读取上传的Excel文件
 * @param $attach_id integer 要文件id
 * @param $column array 列名
 * @param $dateColumn array 日期列
 * @return array 内容数组
 * created by blu10ph
 */
function importFormExcel($attach_id, $column, $dateColumn = array()) {
    $attach_id = intval ( $attach_id );
    $res = array (
        'status' => 0,
        'data' => ''
    );
    if (empty ( $attach_id ) || ! is_numeric ( $attach_id )) {
        $res ['data'] = '上传文件ID无效！';
        return $res;
    }
    $fileModel = new FileModel(get_corpid());
    $file = $fileModel->get(1,0,['id'=>$attach_id]);
    //var_exp($file,'$file');
    if(!$file){
        $res ['data'] = '读取文件记录失败！';
        return $res;
    }
    $filename = $file ['savepath'] . DS . $file ['savename'];
    //var_exp($filename,'$filename');
    if (! file_exists ( $filename )) {
        $res ['data'] = '上传的文件读取失败';
        return $res;
    }
    $extend = $file['ext'];
    if (! ($extend == 'xls' || $extend == 'xlsx' || $extend == 'csv')) {
        $res ['data'] = '文件格式不对，请上传xls,xlsx格式的文件';
        return $res;
    }

    vendor ( 'PHPExcel' );
    vendor ( 'PHPExcel.PHPExcel_IOFactory' );
    vendor ( 'PHPExcel.Reader.Excel5' );

    switch (strtolower ( $extend )) {
        case 'csv' :
            $format = 'CSV';
            $objReader = \PHPExcel_IOFactory::createReader ( $format )->setDelimiter ( ',' )->setInputEncoding ( 'GBK' )->setEnclosure ( '"' )->setLineEnding ( "\r\n" )->setSheetIndex ( 0 );
            break;
        case 'xls' :
            $format = 'Excel5';
            $objReader = \PHPExcel_IOFactory::createReader ( $format );
            break;
        default :
            $format = 'Excel2007';
            $objReader = \PHPExcel_IOFactory::createReader ( $format );
    }

    $objPHPExcel = $objReader->load ( $filename );
    $objPHPExcel->setActiveSheetIndex ( 0 );
    $sheet = $objPHPExcel->getSheet ( 0 );
    $highestRow = $sheet->getHighestRow (); // 取得总行数
    for($j = 2; $j <= $highestRow; $j ++) {
        $addData = array ();
        foreach ( $column as $k => $v ) {
            if ($dateColumn) {
                foreach ( $dateColumn as $d ) {
                    if ($k == $d) {
                        $addData [$v] = gmdate ( "Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP ( $objPHPExcel->getActiveSheet ()->getCell ( "$k$j" )->getValue () ) );
                    } else {
                        $addData [$v] = trim ( ( string ) $objPHPExcel->getActiveSheet ()->getCell ( $k . $j )->getValue () );
                    }
                }
            } else {
                $addData [$v] = trim ( ( string ) $objPHPExcel->getActiveSheet ()->getCell ( $k . $j )->getValue () );
            }
        }

        $isempty = true;
        foreach ( $column as $v ) {
            $isempty && $isempty = empty ( $addData [$v] );
        }

        if (! $isempty)
            $result [$j] = $addData;
    }
    $res ['status'] = 1;
    $res ['data'] = $result;
    return $res;
}

/**
 * 输出Excel文件
 * @param $data array 内容数组
 * @param $filename string 文件名
 * @param $sheet boolean 是否是多个sheet
 * @return null 会直接exit(),不会返回
 * created by blu10ph
 */
function outExcel($data, $filename = '', $sheet = false) {
    saveExcelToPath($data, $sheet, $filename);
    unset ( $sheet );
    unset ( $dataArr );
    exit;
}

/**
 * 输出Excel文件
 * @param $data array 内容数组
 * @param $sheet boolean 是否是多个sheet
 * @return string|boolean 成功返回文件路径字符串,失败返回false
 * created by blu10ph
 */
function saveExcel($data, $sheet = false) {
    $path = dirname($_SERVER['SCRIPT_FILENAME']) . DS . 'download' . DS . date('Ymd');
    $mkdir_flg = true;
    if(!is_dir($path) && function_exists('mkdirs')){
        $mkdir_flg = mkdirs($path);
    }
    if(!$mkdir_flg){
        return false;
    }
    $savename = md5(microtime(true)).'.xlsx';
    $relative_path = dirname($_SERVER['SCRIPT_NAME']).'/download/' . date('Ymd') . '/' . $savename;
    saveExcelToPath($data, $sheet,$savename,$path);
    unset ( $sheet );
    unset ( $dataArr );
    return $relative_path;
}


/**
 * 输出Excel文件
 * @param $data array 内容数组
 * @param $sheet boolean 是否是多个sheet
 * @param $filename string 文件名
 * @param $path string 文件路径,null时直接输出
 * created by blu10ph
 */
function saveExcelToPath($data, $sheet = false,$filename=null,$path=null) {
    $filename = empty ( $filename ) ? date ( 'YmdHis' ) : $filename ;
    vendor ( 'PHPExcel' );
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    if(!$path){
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename.'.xls');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
    }
    if(!$path){
        $path = 'php://output';
    }else{
        $path .= DS . $filename;
    }
    $Line = array(
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
    );
    if(!$sheet){
        foreach ( $data as $k=>$v ) {
            $u=$k+1;
            $s = count($v);
            for($i=0;$i<$s;$i++){
                $n = $Line[$i].$u;
                $va = array_values($v);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($n,$va[$i]);
            }
        }

        /*// Miscellaneous glyphs, UTF-8
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A4', 'Miscellaneous glyphs')
                    ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
        */
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
    }else {
        $f=0;
        foreach ( $data as $t=>$u )
        {
            foreach ( $u as $k=>$v )
            {
                $u=$k+1;
                $s = count($v);
                for($i=0;$i<$s;$i++){
                    $n = $Line[$i].$u;
                    $va = array_values($v);
                    $objPHPExcel->setActiveSheetIndex($f)
                        ->setCellValue($n,$va[$i]);
                    if($data[$t][$k][1]!=$data[$t][$k-1][1]&&$k!=0){
                        $objPHPExcel->getActiveSheet()->getStyle($n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                        $objPHPExcel->getActiveSheet()->getStyle($n)->getFill()->getStartColor()->setARGB('FFFF00');
                    }
                }
            }


            /*// Miscellaneous glyphs, UTF-8
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A4', 'Miscellaneous glyphs')
                        ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
            */
            // Rename worksheet
            $objPHPExcel->createSheet();$objPHPExcel->getSheet($f)->setTitle($t);
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex($f);
            $f++;
        }
        $f=0;
    }
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($path);
}