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
use think\Request;
use app\common\model\UserCorporation;
use app\common\model\Umessage;
use app\common\model\Employee;
use app\common\model\StructureEmployee;
use app\crm\model\CustomerTrace;
use app\common\model\ImportFile as FileModel;
use app\common\model\Picture as PictureModel;

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
    $corp_id = get_corpid();
    $auth = new \myvendor\Auth($corp_id);
    if (!$auth->check($rule,$uid)) {
        return false;
    } else {
        return true;
    }
}

function set_cache_by_corp($corp_id, $name, $data, $config=null){
    cache("user_cache_".$corp_id."_".$name,$data,$config);
}

function get_cache_by_corp($corp_id, $name){
    $data = cache("user_cache_".$corp_id."_".$name);
    return $data;
}

function set_cache_by_tel($telephone, $name, $data, $config=null){
    cache("user_cache_".$telephone."_".$name,$data,$config);
}

function get_cache_by_tel($telephone, $name){
    $data = cache("user_cache_".$telephone."_".$name);
    return $data;
}

function set_telephone_token_list($telephone,$token_arr){
    cache("phone_token_".$telephone,$token_arr);
}

function set_telephone_to_token_list($telephone,$token){
    $token_arr = cache("phone_token_".$telephone);
    $token_arr[] = $token;
    cache("phone_token_".$telephone,$token_arr);
}

function get_telephone_token_list($telephone){
    $token_arr = cache("phone_token_".$telephone);
    return $token_arr;
}

function set_telephone_by_token($token,$telephone){
    cache("token_phone_".$token,$telephone);
}

function get_telephone_by_token($token){
    $telephone = cache("token_phone_".$token);
    return $telephone;
}

function del_telephone_by_token($token){
    cache("token_phone_".$token,null);
}

function get_token_by_cookie(){
    return cookie("xzmid");
}

function set_token_to_cookie($token){
    cookie("xzmid",$token);
}

function set_userinfo($corp_id,$telephone,$user_arr){
    $userinfo = [
        'corp_id'=>$corp_id,
        'telephone'=>$telephone,
        'userid'=>$user_arr['id'],
        'userpic'=>$user_arr['userpic'],
        'wiredphone'=>$user_arr['wired_phone'],
        'partphone'=>$user_arr['part_phone'],
        'truename'=>$user_arr['truename'],
        'nickname'=>$user_arr['nickname'],
        'role'=>$user_arr['role_name'],
        'userinfo'=>$user_arr,
    ];
    
    set_cache_by_tel($telephone,'userinfo',$userinfo);
    return $userinfo;
}

function get_userinfo($telephone=null){
    if(!$telephone){
        $telephone = input('userid','',"string");
        //var_exp($telephone,'$telephone0');
        if(empty($telephone)){
            $token = get_token_by_cookie();
            if(empty($token)){
                //var_exp(00,'00');
                return [];
            }
            //var_exp($token,'$token');
            $telephone = get_telephone_by_token($token);
        }
    }
    $userinfo = get_cache_by_tel($telephone,'userinfo');
    //var_exp($userinfo,'$userinfo');
    return $userinfo;
}

function check_telphone_and_password($telephone,$password){
    $result['status'] = false;
    if ($telephone == '' || $password == '') {
        $result['message'] = '缺少必填信息';
        $result['errnum'] = 1;
        return $result;
    }
    if (!check_tel($telephone)) {
        $result['message'] = '手机号码格式不正确';
        $result['errnum'] = 2;
        return $result;
    }
    $corp_id = UserCorporation::getUserCorp($telephone);
    if (empty($corp_id)) {
        $result['message'] = '用户不存在或用户未划分公司归属';
        $result['errnum'] = 3;
        return $result;
    }
    $employeeM = new Employee($corp_id);
    $user_arr = $employeeM->getEmployeeByTel($telephone);
    if (empty($user_arr)) {
        $result['message'] = '用户不存在或用户未划分公司归属';
        $result['errnum'] = 3;
        return $result;
    }
    if ($user_arr['password'] != md5($password)) {
        $result['message'] = '密码错误';
        $result['errnum'] = 4;
        return $result;
    }
    if (empty($user_arr['lastlogintime'])) {
        $result['message'] = '用户首次登陆，请修改密码';
        $result['errnum'] = 5;
        return $result;
    }
    $result["status"] = true;
    $result["corp_id"] = $corp_id;
    $result["user_info"] = $user_arr;
    return $result;
}

function check_telephone_and_token($telephone,$access_token){
    $info['status'] = false;
    $device_type_info = get_user_device($telephone,$access_token);
    //var_exp($device_type_info,'$device_type_info',1);
    if(empty($device_type_info)){
        $info['device_type'] = 0;
        $info['message'] = 'token不正确，请重新登陆';
        $info['errnum'] = 104;
        return $info;
    }
    $device_type = $device_type_info["device_type"];
    $info['device_type'] = $device_type;
    if (empty($telephone) || empty($access_token)) {
        $info['message'] = '用户id为空或token为空';
        $info['errnum'] = 101;
        return $info;
    }
    if (!check_tel($telephone)) {
        $info['message'] = '用户名格式不正确';
        $info['errnum'] = 102;
        return $info;
    }
    $corp_id = get_corpid($telephone);
    if ($corp_id == false) {
        $info['message'] = '用户不存在';
        $info['errnum'] = 103;
        return $info;
    }
    $employM = new Employee($corp_id);
    $userinfo = $employM->getEmployeeByTel($telephone);
    $field_name = 'other';
    switch ($device_type){//0:other,1:web,2:pc,3:ios:4:android
        case 1:
            $field_name = "web";
            break;
        case 2:
            $field_name = "pc";
            break;
        case 3:
        case 4:
            $field_name = "app";
            break;
    }
    $field_name .= "_token";
    if($field_name!="web_token"){
        if ($userinfo[$field_name] != $access_token) {
            del_user_device_token_cache($telephone,$access_token);
            $info['message'] = 'token不正确，请重新登陆';
            $info['errnum'] = 104;
            return $info;
        }
    }
    $info['message'] = 'SUCCESS';
    $info['status'] = true;
    $info['corp_id'] = $corp_id;
    $info['userinfo'] = $userinfo;
    set_userinfo($corp_id,$telephone,$userinfo);
    return $info;
}

function clear_token($telephone,$ues_token){
    $token_arr = get_telephone_token_list($telephone);
    set_telephone_token_list($telephone,$ues_token);
    if(is_array($token_arr)){
        foreach ($token_arr as $token){
            $device_type_info = get_user_device($telephone,$token);
            if($device_type_info["device_type"]==1){
                continue;
            }
            if(!in_array($token,$ues_token)){
                del_telephone_by_token($token);
                del_user_device_token_cache($telephone,$token);
            }
        }
    }
}

function login($corp_id,$uid,$telephone,$device_type,$ip){
    $result["status"] = false;
    //创建用户token，保存到cookie
    $employeeM = new Employee($corp_id);
    $save_res=$employeeM->createSystemToken($device_type,$telephone);
    if($save_res['res']>0){
        $result['access_token'] = $save_res['token'];
    }else{
        $result['message'] = '获取token信息失败，联系网站后台管理员';
        $result['errnum'] = 6;
        return $result;
    }
    set_token_to_cookie($save_res['token']);
    set_telephone_by_token($save_res['token'],$telephone);
    set_user_device($telephone,$save_res['token'],$device_type,$corp_id,$uid);

    $user_info = $employeeM->getEmployeeByTel($telephone);
    set_userinfo($corp_id,$telephone,$user_info);

    clear_token($telephone,[
        $user_info["system_token"],
        $user_info["web_token"],
        $user_info["pc_token"],
        $user_info["app_token"],
        $user_info["other_token"],
    ]);

    //更新登录信息
    $data =['lastloginip'=>$ip,'lastlogintime'=>time()];
    if ($employeeM->setEmployeeSingleInfo($telephone,$data) <= 0) {
        $result['message'] = '登录信息写入失败，联系管理员';
        $result['errnum'] = 7;
        return $result;
    }
    $result["status"] = true;
    return $result;
}

function logout($telephone=null,$token=null){
    if(!$telephone){
        $telephone = $telephone?:input('userid');
        $token = $token?:input('access_token');
        if(!$telephone){
            $token = get_token_by_cookie();
            $telephone = get_telephone_by_token($token);
        }else{
            return [];
        }
    }
    set_token_to_cookie(null);
    del_telephone_by_token($token);
    del_user_device_token_cache($telephone,$token);
    set_cache_by_tel($telephone,'userinfo',null);
}

/**
 * 设置用户设备信息
 * @param $device_type int 0:other,1:web,2:pc,3:ios:4:android
 * @return int
 * created by blu10p
 */
function set_user_device($telephone,$token,$device_type,$corp_id,$uid){
    $user_info = [
        'device_type'=>$device_type,
        'corp_id'=>$corp_id,
        'userid'=>$uid,
    ];
    set_cache_by_tel($telephone,$token,$user_info);
    return $user_info;
}

/**
 * 获取用户设备信息
 * @return int
 * created by blu10p
 */
function get_user_device($telephone,$token){
    $user_info = get_cache_by_tel($telephone,$token);
    return $user_info;
}

/**
 * 删除用户设备所属token
 * @return int
 * created by blu10p
 */
function del_user_device_token_cache($telephone,$token){
    set_cache_by_tel($telephone,$token,null);
    return true;
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
    $employM = new Employee($corp_id);
    $users = $employM->getEmployeeByTel($tel);
    return $users['id'];
}
/**
 * 获取公司id代号
 * @param $tel
 * @return bool|mixed|string
 * created by messhair
 */
function get_corpid ($tel = null) {
    $userinfo = get_userinfo();
    if (!empty($userinfo['corp_id'])) {
        return $userinfo['corp_id'];
    }
    if (!is_null($tel)) {
        $corp_id = UserCorporation::getUserCorp($tel);
        return $corp_id;
    }
    return false;
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

function getStructureIds($user_id = null){
    $userinfo = get_userinfo();
    if (!empty($userinfo['structure_ids'])) {
        return $userinfo['structure_ids'];
    }
    if (!is_null($user_id)) {
        $structureEmployee = new StructureEmployee();
        $struct_ids = $structureEmployee->getStructIdsByEmployee($user_id);
        return $struct_ids;
    }
    return false;
}

// 处理带Emoji的数据，type=0表示写入数据库前的emoji转为HTML，为1时表示HTML转为emoji码
function deal_emoji($msg, $type = 1) {
    if ($type == 0) {
        $msg = urlencode ( $msg );
        $msg = json_encode ( $msg );
    } else {

        $msg = preg_replace ( "#\\\u([0-9a-f]+)#ie", "iconv('UCS-2','UTF-8', pack('H4', '\\1'))", $msg );

        // $msg = preg_replace("#(\\\ue[0-9a-f]{3})#ie", "addslashes('\\1')",$msg);

        $msg = urldecode ( $msg );
        // $msg = json_decode ( $msg );
        // dump($msg);
        $msg = str_replace ( '"', "", $msg );
        // dump($msg);exit;
        /*if ($txt !== null) {
            $msg = $txt;
        }*/
    }

    return $msg;
}

function getDeviceTypeName($device_type){
    $device_type_name = null;
    switch ($device_type){//0:other,1:web,2:pc,3:ios:4:android
        case 1:
            $device_type_name = "web";
            break;
        case 2:
            $device_type_name = "pc";
            break;
        case 3:
            $device_type_name = "ios";
            break;
        case 4:
            $device_type_name = "android";
            break;
        default:
            $device_type_name = "其他设备";
    }
    return $device_type_name;
}

function getCommStatusArr($comm_status){
    $comm_status_arr = [];
    switch ($comm_status){
        case 1:
            $comm_status_arr=[
                "tend_to"=>0,
                "phone_correct"=>1,
                "profile_correct"=>1,
                "call_through"=>1,
                "is_wait"=>0,
            ];
            break;
        case 2:
            $comm_status_arr=[
                "tend_to"=>0,
                "phone_correct"=>0,
                "profile_correct"=>0,
                "call_through"=>0,
                "is_wait"=>0,
            ];
            break;
        case 3:
            $comm_status_arr=[
                "tend_to"=>0,
                "phone_correct"=>1,
                "profile_correct"=>0,
                "call_through"=>1,
                "is_wait"=>0,
            ];
            break;
        case 4:
            $comm_status_arr=[
                "tend_to"=>0,
                "phone_correct"=>1,
                "profile_correct"=>0,
                "call_through"=>0,
                "is_wait"=>0,
            ];
            break;
        case 5:
            $comm_status_arr=[
                "tend_to"=>0,
                "phone_correct"=>1,
                "profile_correct"=>1,
                "call_through"=>1,
                "is_wait"=>1,
            ];
            break;
        case 6:
            $comm_status_arr=[
                "tend_to"=>1,
                "phone_correct"=>1,
                "profile_correct"=>1,
                "call_through"=>1,
                "is_wait"=>0,
            ];
            break;
    }
    return $comm_status_arr;
}

function getCommStatusByArr($comm_status_arr){
    $comm_status = 0;
    $comm_status_str = $comm_status_arr['tend_to'].
        $comm_status_arr['phone_correct'].
        $comm_status_arr['profile_correct'].
        $comm_status_arr['call_through'].
        $comm_status_arr['is_wait'];
    switch ($comm_status_str){
        case "01110":
            $comm_status = 1;
            break;
        case "00000":
            $comm_status = 2;
            break;
        case "01010":
            $comm_status = 3;
            break;
        case "01000":
            $comm_status = 4;
            break;
        case "01111":
            $comm_status = 5;
            break;
        case "11110":
            $comm_status = 6;
            break;
    }
    return $comm_status;
}

function getYesNoName($val){
    $yes_no_name = null;
    switch ($val){
        case 0:
            $yes_no_name = "否";
            break;
        case 1:
            $yes_no_name = "是";
            break;
        default:
            $yes_no_name = "无";
    }
    return $yes_no_name;
}

function getPayTypeName($val){
    $pay_type_name = null;
    switch ($val){
        case 1:
            $pay_type_name = "现金";
            break;
        case 2:
            $pay_type_name = "银行转帐";
            break;
        default:
            $pay_type_name = "无";
    }
    return $pay_type_name;
}

function getSexName($sex){
    $sex_name = null;
    switch ($sex){
        case 0:
            $sex_name = "女";
            break;
        case 1:
            $sex_name = "男";
            break;
        default:
            $sex_name = "无";
    }
    return $sex_name;
}

function getCloseDegreeName($close_degree){
    $close_degree_name = null;
    switch ($close_degree){
        case 0:
            $close_degree_name = "不亲密";
            break;
        case 1:
            $close_degree_name = "亲密";
            break;
        default:
            $close_degree_name = "无";
    }
    return $close_degree_name;
}

function getDealCapabilityName($deal_capability){
    $deal_capability_name = null;
    switch ($deal_capability){
        case 1:
            $deal_capability_name = "普通人";
            break;
        case 2:
            $deal_capability_name = "决策人";
            break;
        case 3:
            $deal_capability_name = "分项决策人";
            break;
        case 4:
            $deal_capability_name = "商务决策人";
            break;
        case 5:
            $deal_capability_name = "技术决策人";
            break;
        case 6:
            $deal_capability_name = "财务决策人";
            break;
        case 7:
            $deal_capability_name = "使用人";
            break;
        case 8:
            $deal_capability_name = "意见影响人";
            break;
        default:
            $deal_capability_name = "无";
    }
    return $deal_capability_name;
}

function getCommStatusName($comm_status){
    $comm_status_name = null;
    switch ($comm_status){
        case 1:
            $comm_status_name = "无意向";
            break;
        case 2:
            $comm_status_name = "号码无效";
            break;
        case 3:
            $comm_status_name = "资料有误";
            break;
        case 4:
            $comm_status_name = "未接通";
            break;
        case 5:
            $comm_status_name = "待定";
            break;
        case 6:
            $comm_status_name = "有意向";
            break;
        default:
            $comm_status_name = "无";
    }
    return $comm_status_name;
}

function getBelongsToManageName($belongs_to){
    $belongs_to_name = null;
    switch ($belongs_to){
        case 1:
        case 2:
        $belongs_to_name = "未申领";
            break;
        case 3:
            $belongs_to_name = "跟进中";
            break;
        case 4:
            $belongs_to_name = "待处理";
            break;
        default:
            $belongs_to_name = "无";
    }
    return $belongs_to_name;
}

function getResourceFromName($resource_from){
    $resource_from_name = null;
    switch ($resource_from){
        case 1:
            $resource_from_name = "批量导入";
            break;
        case 2:
            $resource_from_name = "员工添加";
            break;
        case 3:
            $resource_from_name = "员工搜集";
            break;
        default:
            $resource_from_name = "无";
    }
    return $resource_from_name;
}
function getTakeTypeFromName($take_type){
    $take_type_name = null;
    switch ($take_type){
        case 1:
            $take_type_name = "转介绍";
            break;
        case 2:
            $take_type_name = "搜索";
            break;
        case 3:
            $take_type_name = "购买";
            break;
        default:
            $take_type_name = "无";
    }
    return $take_type_name;
}

function getInColumnName($in_column){
    $in_column_name = null;
    switch ($in_column){
        case 0:
            $in_column_name = "我的所有客户";
            break;
        case 1:
            $in_column_name = "待沟通";
            break;
        case 2:
            $in_column_name = "未跟进";
            break;
        case 3:
            $in_column_name = "正常跟进";
            break;
        case 4:
            $in_column_name = "停滞";
            break;
        case 5:
            $in_column_name = "待定";
            break;
        case 6:
            $in_column_name = "无意向";
            break;
        case 7:
            $in_column_name = "已成单";
            break;
        case 8:
            $in_column_name = "无效客户";
            break;
        default:
            $in_column_name = "无";
    }
    return $in_column_name;
}

function getApplyStatusList(){
    $sale_status_list = [];
    $sale_status_list[] = ["status"=>0,"name"=>"审核中"];
    $sale_status_list[] = ["status"=>1,"name"=>"通过"];
    $sale_status_list[] = ["status"=>2,"name"=>"驳回"];
    $sale_status_list[] = ["status"=>3,"name"=>"撤回"];
    return $sale_status_list;
}

function getApplyStatusColumn(){
    $sale_status_list = [];
    $sale_status_list[0] = "审核中";
    $sale_status_list[1] = "通过";
    $sale_status_list[2] = "驳回";
    $sale_status_list[3] = "撤回";
    return $sale_status_list;
}

function getApplyStatusName($sale_status){
    $sale_status_name = null;
    switch ($sale_status){
        case 0:
            $sale_status_name = "审核中";
            break;
        case 1:
            $sale_status_name = "通过";
            break;
        case 2:
            $sale_status_name = "驳回";
            break;
        case 3:
            $sale_status_name = "撤回";
            break;
        default:
            $sale_status_name = "无";
    }
    return $sale_status_name;
}

function getSaleStatusList(){
    $sale_status_list = [];
    $sale_status_list[] = ["status"=>0,"name"=>"无意向"];
    $sale_status_list[] = ["status"=>1,"name"=>"有意向"];
    $sale_status_list[] = ["status"=>2,"name"=>"预约拜访"];
    $sale_status_list[] = ["status"=>3,"name"=>"已拜访"];
    $sale_status_list[] = ["status"=>4,"name"=>"成单申请"];
    $sale_status_list[] = ["status"=>5,"name"=>"赢单"];
    $sale_status_list[] = ["status"=>6,"name"=>"输单"];
    $sale_status_list[] = ["status"=>7,"name"=>"作废"];
    $sale_status_list[] = ["status"=>8,"name"=>"发票申请"];
    $sale_status_list[] = ["status"=>9,"name"=>"已退款"];
    return $sale_status_list;
}

function getSaleStatusColumn(){
    $sale_status_list = [];
    $sale_status_list[0] = "无意向";
    $sale_status_list[1] = "有意向";
    $sale_status_list[2] = "预约拜访";
    $sale_status_list[3] = "已拜访";
    $sale_status_list[4] = "成单申请";
    $sale_status_list[5] = "赢单";
    $sale_status_list[6] = "输单";
    $sale_status_list[7] = "作废";
    $sale_status_list[8] = "发票申请";
    $sale_status_list[9] = "已退款";
    return $sale_status_list;
}

function getSaleStatusName($sale_status){
    $sale_status_name = null;
    switch ($sale_status){
        case 0:
            $sale_status_name = "无意向";
            break;
        case 1:
            $sale_status_name = "有意向";
            break;
        case 2:
            $sale_status_name = "预约拜访";
            break;
        case 3:
            $sale_status_name = "已拜访";
            break;
        case 4:
            $sale_status_name = "成单申请";
            break;
        case 5:
            $sale_status_name = "赢单";
            break;
        case 6:
            $sale_status_name = "输单";
            break;
        case 7:
            $sale_status_name = "作废";
            break;
        case 8:
            $sale_status_name = "发票申请";
            break;
        case 9:
            $sale_status_name = "已退款";
            break;
        default:
            $sale_status_name = "无";
    }
    return $sale_status_name;
}

function getEmployeeOnDutyName($on_duty){
    $on_duty_name = null;
    switch ($on_duty){
        case -1:
            $on_duty_name = "离职";
            break;
        case 1:
            $on_duty_name = "在职";
            break;
        case 2:
            $on_duty_name = "休假";
            break;
        default:
            $on_duty_name = "无";
    }
    return $on_duty_name;
}

function getImportResultName($import_result){
    $import_result_name = null;
    switch ($import_result){
        case 0:
            $import_result_name = "全部失败";
            break;
        case 1:
            $import_result_name = "部分失败";
            break;
        case 2:
            $import_result_name = "全部成功";
            break;
        default:
            $import_result_name = "无";
    }
    return $import_result_name;
}

function getContractAppliedStatusName($applied_status){
    $applied_status_name = null;
    switch ($applied_status){
        case 0:
            $applied_status_name = "审核中";
            break;
        case 1:
            $applied_status_name = "已通过";
            break;
        case 2:
            $applied_status_name = "已驳回";
            break;
        case 3:
            $applied_status_name = "已撤回";
            break;
        case 4:
            $applied_status_name = "待领取";
            break;
        case 5:
            $applied_status_name = "已领取";
            break;
        case 6:
            $applied_status_name = "已作废";
            break;
        case 7:
            $applied_status_name = "已收回";
            break;
        case 8:
            $applied_status_name = "已提醒";
            break;
        case 9:
            $applied_status_name = "已退款";
            break;
        default:
            $applied_status_name = "无";
    }
    return $applied_status_name;
}

function getBillStatusName($status){
    $status_name = null;
    switch ($status){
        case 0:
            $status_name = "审核中";
            break;
        case 1:
            $status_name = "已通过";
            break;
        case 2:
            $status_name = "已驳回";
            break;
        case 3:
            $status_name = "已撤回";
            break;
        case 4:
            $status_name = "待领取";
            break;
        case 5:
            $status_name = "已领取";
            break;
        case 6:
            $status_name = "已作废";
            break;
        case 7:
            $status_name = "已收回";
            break;
        case 8:
            $status_name = "已提醒";
            break;
        case 9:
            $status_name = "已退款";
            break;
        default:
            $status_name = "无";
    }
    return $status_name;
}

function getBusinessName($business){
    $corp_id = get_corpid();
    $business_index = get_cache_by_corp($corp_id,"business");
    if(empty($business_index)){
        $businessModel = new \app\common\model\Business();
        $business_index = $businessModel->getBusinessArray();
        set_cache_by_corp($corp_id,"business",$business_index,6000);
    }
    if(!isset($business_index[$business])){
        return false;
    }
    return $business_index[$business];
}

function createCustomersTraceItem(
    $uid,
    $time,
    $table,
    $id,
    $key,
    $customerOldData,
    $customerNewData,
    $updateItemName,
    $sub_name="",
    $option_name="更改了",
    $link_name="更改为"
){
    $customersTrace["add_type"] = 0;
    $customersTrace["operator_type"] = 0;
    $customersTrace["operator_id"] = $uid;
    $customersTrace["create_time"] = $time;
    $customersTrace["customer_id"] = $id;
    $customersTrace["db_table_name"] = $table;
    $customersTrace["db_field_name"] = $key;
    $customersTrace["old_value"] = isset($customerOldData[$key])?$customerOldData[$key]:"";
    $customersTrace["new_value"] = isset($customerNewData[$key])?$customerNewData[$key]:"";
    $customersTrace["value_type"] = isset($updateItemName[$key][1])?$updateItemName[$key][1]:"";
    $func_name = $customersTrace["value_type"];
    $customersTrace["option_name"] = $option_name;
    $customersTrace["sub_name"] = $sub_name;
    $customersTrace["item_name"] = isset($updateItemName[$key][0])?$updateItemName[$key][0]:"";
    $customersTrace["from_name"] = isset($updateItemName[$key][1])?$func_name($customersTrace["old_value"]):$customersTrace["old_value"];
    $customersTrace["link_name"] = $link_name;
    $customersTrace["to_name"] = isset($updateItemName[$key][1])?$func_name($customersTrace["new_value"]):$customersTrace["new_value"];
    $customersTrace["status_name"] = '';
    $customersTrace["remark"] = '';
    return $customersTrace;
}

/**
 * 时间戳格式化
 *
 * @param $time int
 * @param $format string
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
    if (empty ( $time ))
        return '';

    $time = $time === NULL ? time() : intval ( $time );
    return date ( $format, $time );
}
function time_format_html5($time = NULL) {
    if (empty ( $time ))
        return '';
    $time = $time === NULL ? time() : intval ( $time );
    return time_format($time,'Y-m-d')."T".time_format($time,'H:i:s');
}
function minutes_format_html5($time = NULL) {
    if (empty ( $time ))
        return '';
    $time = $time === NULL ? time() : intval ( $time );
    return time_format($time,'Y-m-d')."T".time_format($time,'H:i');
}
function day_format($time = NULL, $format = 'Y-m-d') {
    return time_format ( $time, $format );
}
function hour_format($time = NULL) {
    return time_format ( $time, 'H:i' );
}
function time_offset($time = NULL) {
    if (empty ( $time ))
        return '00:00';

    $mod = $time % 60;
    $min = ($time - $mod) / 60;

    $mod < 10 && $mod = '0' . $mod;
    $min < 10 && $min = '0' . $min;

    return $min . ':' . $mod;
}
function time_diff_day_time($time = NULL,$now_time=NULL) {
    if(!$time){
        $time = 0;
    }
    if(!$now_time){
        $now_time = time();
    }
    $all_minute = round(($now_time-$time)/60);
    $minute = $all_minute%60;
    $hour = floor($minute/60);
    $day = floor($hour/24);
    return $day."天".$hour.":".$minute;
}
/**
 * 根据图片ID获取图像文件路径
 * @param $id int 图片ID
 * @return mixed|string
 * created by blu10ph
 */
function get_img_path_by_id($id,$corp_id='') {
    $img_path = "";
    if (empty($id)) {
        return $img_path;
    }
    if (empty($corp_id)) {
        $corp_id = get_corpid();
    }
    $pictureInfo = cache($corp_id."_img.".$id);
    if(!$pictureInfo){
        $pictureModel = new PictureModel($corp_id);
        $pictureInfo = $pictureModel->get(1,0,["id"=>$id,"status"=>1]);
        cache($corp_id."_img_".$id,$pictureInfo);
    }
    if(!$pictureInfo){
        return $img_path;
    }
    if($pictureInfo["block"]){
        $img_path = config('image_block');
        return $img_path;
    }
    return $img_path;
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
        $img_path = $img_path.DS.date('Ymd',time());//相对路径
        $corp_id = get_corpid();
        $save_path = PUBLIC_PATH.DS."webroot".DS.$corp_id.DS.$img_path.DS;//物理路径
        if (!is_dir($save_path)) {
            mkdirs($save_path);
        }
        $img_path = DS."webroot".DS.$corp_id.DS.$img_path.DS.time().rand(10000,99999).'.tmp';//相对路径文件
        $save_path = PUBLIC_PATH.$img_path;//物理路径文件
        //var_exp($img_path,'$img_path');
        //var_exp($save_path,'$save_path');
        file_put_contents($save_path,$data);
        $arr=getimagesize($save_path);
        $img_type = explode(',',config('upload_image.image_ext'));
        //var_exp($arr,'$arr');
        //var_exp($img_type,'$img_type');
        $img_ext = '';
        foreach ($img_type as $val) {
            if (false !== strpos($arr['mime'],$val)) {
                $img_ext = $val;
                break;
            }
        }
        //var_exp($img_ext,'$img_ext',1);
        if ($img_ext == '') {
          $res['message'] = '未能识别上传图像格式，联系管理员';
        } else {
            $img_path = substr($img_path,0,-3).$img_ext;
            $new_save_path = substr($save_path,0,-3).$img_ext;
            rename($save_path,$new_save_path);
            $res = ['imgurl' => $img_path,'message' =>'SUCCESS','status'=>true];
        }
    } catch(\Exception $ex){
        $res['message'] = '存储头像失败，联系管理员';
        //$res['message'] = $ex->getMessage();
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
 * @param float $total 总金额 单位元，3.33
 * @param int $num 个数
 * @parame int $redtype 红包类型 1运气红包 2普通红包 3任务红包
 * @param float $min 最小红包金额
 * @return array
 * created by messhair
 */
function get_red_bonus ($total,$num,$redtype,$min=0.01) {
    $arr= array();
    if ($redtype ==1) {
        for ($i=1;$i<$num;$i++) {
            $safe_total=($total-($num-$i)*$min)/($num-$i);
            $safe_total = $safe_total<$min ?$min:$safe_total;
            $money=mt_rand($min*100,$safe_total*100)/100;
            $money=number_format($money, 2, '.', '');
            $total=$total-$money;
            $arr[].=$money;
        }
        $arr[].=number_format($total, 2, '.', '');
    } else {
        $each_money = $total/$num;
        for($i =0;$i<$num;$i++){
            $arr[$i] = number_format($each_money,2,'.','');
        }
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
function set_reset_code($tel,$code){
    //session('reset_code'.$tel,$code);
    set_cache_by_tel($tel,"reset_code",$code);
}
function get_reset_code($tel){
    //$code = session('reset_code'.$tel);
    $code = get_cache_by_tel($tel,"reset_code");
    return $code;
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
        set_reset_code($tel,$code);
        return ['status'=>true];
    } else {
        $content = '手机号'.$tel.'发送信息失败，原因为：'.$data['Result'];
        send_mail(config('system_email.user'),config('system_email.pass'),'wangqiwen@winbywin.com', '通信项目短信问题',config('system_email.from_name'), $content);
        return ['status'=>false,'message'=>$content];
    }
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
function mkdirs($dir,$mode=0755) {
    if(!is_dir($dir)) {
        if (!mkdirs(dirname($dir))){
            return false;
        }
        if(!mkdir($dir,$mode)){
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
    $corp_id = get_corpid();
    $path = PUBLIC_PATH.DS."webroot".DS.$corp_id . DS . 'download' . DS . date('Ymd');
    $mkdir_flg = true;
    if(!is_dir($path) && function_exists('mkdirs')){
        $mkdir_flg = mkdirs($path);
    }
    if(!$mkdir_flg){
        return false;
    }
    $savename = md5(microtime(true)).'.xlsx';
    $relative_path = $path . DS . $savename;
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

/**
 * pk项目类型对应的名称
 * @param pk项目类型
 * @return mixed
 */
function get_target_type_name($value){
    //1:通话数,2:商机数,3:成交额,4:成单数,5:拜访数,6:新增客户数,7:悬赏拜访对象
    $type_name = null;
    switch ($value){
        case 1:
            $type_name = "通话数";
            break;
        case 2:
            $type_name = "商机数";
            break;
        case 3:
            $type_name = "成交额";
            break;
        case 4:
            $type_name = "成单数";
            break;
        case 5:
            $type_name = "拜访数";
            break;
        case 6:
            $type_name = "新增客户数";
            break;
        case 7:
            $type_name = "悬赏拜访对象";
            break;
        default:
            $type_name = "无";
    }
    return $type_name;
}

/**
 * 通过员工的ids获取姓名数组，以id作为键值
 * @param $uids
 * @return false|PDOStatement|string|\think\Collection
 */
function get_employee_truename($uids){
    $corp_id = get_corpid();
    $employeeModel = new Employee($corp_id);
    $employeeNames = $employeeModel->getEmployeeNameByUserids($uids);
    return $employeeNames;
}
