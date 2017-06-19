<?php
/**
 * Created by messhair
 * Date: 17-2-17
 */
namespace app\huanxin\controller;

use app\common\model\UserCorporation;
use app\common\model\Employer;
use app\huanxin\model\TakeCash;
use app\huanxin\service\Api;
use app\huanxin\service\TakeCash as TakeCashService;
use app\huanxin\model\TakeCash as TakeCashModel;
use app\common\model\Corporation;
use app\common\model\CorporationCash;

class User
{
    public $employM;
    public $errnum;

    /**
     * 根据用户id,access_token 获取所有用户列表
     * @return string {"status":true/false,"message":"","friendsInfo":[]}
     *  friendsInfo=>["telephone"] => string(11) "13322223333"
     * ["userpic"] => string(53) "http://mat1.gtimg.com/www/images/qq2012/qqlogo_1x.png"
     * ["nickname"] => string(31) "zhongxun_xiaoshou_jack_nickname"
     * ["occupation"] => string(1) "6"
     * ["structid"] => string(12) "销售一部"
     */
    public function getFriendsInfo()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        /**
         * TODO  更改此处查询部门
         */
        $friendsInfo = $this->employM->getAllUsers();
        $info['message'] = 'SUCCESS';
        $info['status'] = true;
        $info['friendsInfo'] = $friendsInfo;
        return json($info);
    }

    /**
     * 根据用户userid,access_token 获取所有telephone
     * @return string {"status":true/false,"message":"","friendsTel":[]}
     */
    public function getFriendsTelephone()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        $friends_tel = $this->employM->getAllTels();
        $info = ['status' => true, 'message' => 'SUCCESS', 'friendsTel' => $friends_tel];
        return json($info);
    }

    /**
     * 生成短信验证码，返回app
     * @return string {"status":true/false,"message":""}
     */
    public function getSmsCode()
    {
        $userid = input('param.userid');
        $info['status'] = false;
        if (!check_tel($userid)) {
            $info['message'] = '手机号码格式不正确';
            $info['errnum'] = 1;
            return json($info);
        }
        if (!get_corpid($userid)) {
            $info['message'] = '非系统用户';
            $info['errnum'] = 2;
            return json($info);
        }
        $code = rand(100000, 999999);
        $code = 123456;//TODO 测试开启
        $content = '【咕果】感谢您使用本产品，您的手机验证码为' . $code . '请及时填写';
        $res = send_sms($userid, $code, $content);
        if ($res['status'] == false) {
            $info['message'] = $res['message'];
            $info['errnum'] = 3;
            return json($info);
        }
        $info['message'] = '发送成功';
        $info['status'] = true;
        return json($info);
    }

    /**
     * 验证码校验，更改密码
     * @return string {"status":true/false,"message":""}
     */
    public function resetPassword()
    {
        $userid = input('param.userid');
        $newpass = input('param.newpassword');
        $code = input('param.smscode');
        $info['status'] = false;
        if (!$code) {
            $info['message'] = '手机验证码为空';
            $info['errnum'] = 1;
            return json($info);
        }
        if (!check_tel($userid)) {
            $info['message'] = '手机号码格式不正确';
            $info['errnum'] = 2;
            return json($info);
        }
        $corp_id = get_corpid($userid);
        if (!$corp_id) {
            $info['message'] = '非系统用户';
            $info['errnum'] = 3;
            return json($info);
        }
        $ini_code = session('reset_code' . $userid);
        if ($ini_code != $code) {
            $info['message'] = '手机验证码不正确';
            $info['errnum'] = 4;
            return json($info);
        }
        $apiM = new Api();
        $reset = $apiM ->resetPassword($userid,$newpass);
//        $res = action('Api/resetPassword',['user'=>$userid,'newpass'=>$newpass]);
//        dump($res);exit;
        if ($reset['action']=='set user password') {
            $data['password'] = md5($newpass);
            $corp_id = get_corpid($userid);
            $employer = new Employer($corp_id);
            $r_userid = $employer->getEmployer($userid);
            $employer->setEmployerSingleInfo($userid, $data);
            write_log($r_userid['id'],1,'用户修改登录密码',$corp_id);
            $info['status'] = true;
            $info['message'] = '修改成功，请重新登陆';
            $info['errnum'] = 0;
            session('reset_code'.$userid, null);
        } else {
            $info['message'] = '修改环信密码失败，联系管理员';
            $info['errnum'] = 5;
        }
        return json($info);
    }

    /**
     * 修改app头像
     * @param userid
     * @param access_token
     * @param userpic
     * @param microc  c++客户端传递  1
     * @return array|mixed|string
     */
    public function modifyUserPic()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $user_pic = input('param.userpic');
        $is_c = input('param.microc');
        if ($is_c) {
            $user_pic = str_replace(' ','+',$user_pic);
        }
        $info['status'] = false;
        if ($user_pic == '') {
            $info['message'] = '未设置头像';
            $info['errnum'] = 1;
            return json($info);
        }
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        $img_path = get_app_img($user_pic);
        if (false ==$img_path['status']) {
            $img_path['errnum'] = 2;
            return json($img_path);
        }
        $data = ['userpic'=>$img_path['imgurl']];
        $res = $this->employM->setEmployerSingleInfo($userid,$data);
        return json($img_path);
    }

    /**
     * 创建支付密码
     * @return array|string
     */
    public function createPayPassword()
    {
        $userid = input('param.userid');
        $password = input('param.paypassword');
        $access_token = input('param.access_token');
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        $paypass = $this->employM->getEmployer($userid);
        if (!empty($paypass['pay_password'])) {
            return json(['status'=>false,'errnum'=>1,'message'=>'支付密码已设置，请勿重复设置']);
        }
        $info['status'] = false;
        $r = $this->employM->setEmployerSingleInfo($userid,['pay_password'=>md5($password)]);
        if ($r >0) {
            $info['message'] = '支付密码设置成功';
            $info['errnum'] = 0;
            $info['status'] = true;
        } else {
            $info['message'] = '支付密码设置失败';
            $info['errnum'] = 2;
        }
        return json($info);
    }

    /**
     * 修改环信app支付密码
     * @return string
     */
    public function resetPayPassword()
    {
        $userid = input('param.userid');
        $newpass = input('param.newpaypassword');
        $code = input('param.smscode');
        $info['status'] = false;
        if (!$code) {
            $info['message'] = '手机验证码不正确';
            $info['errnum'] = 1;
            return json($info);
        }
        if (empty($newpass)) {
            $info['message'] = '支付密码不能为空';
            $info['errnum'] = 2;
            return json($info);
        }
        if (!check_tel($userid)) {
            $info['message'] = '手机号码格式不正确';
            $info['errnum'] = 3;
            return json($info);
        }
        $corp_id = get_corpid($userid);
        if (!$corp_id) {
            $info['message'] = '非系统用户';
            $info['errnum'] = 4;
            return json($info);
        }
        $ini_code = session('reset_code' . $userid);
        if ($ini_code != $code) {
            $info['message'] = '手机验证码不正确';
            $info['errnum'] = 5;
            return json($info);
        }
        $data = ['pay_password'=>md5($newpass)];
        $corp_id = get_corpid($userid);
        $employer = new Employer($corp_id);
        $r_userid = $employer->getEmployer($userid);
        $r = $employer->setEmployerSingleInfo($userid,$data);
        if ($r >= 0) {
            session('reset_code'.$userid,null);
            write_log($r_userid['id'],1,'用户修改支付密码',$corp_id);
            $info['status'] = true;
            $info['errnum'] = 0;
            $info['message'] = '修改支付密码成功';
        } else {
            $info['message'] = '修改支付密码失败';
            $info['errnum'] = 6;
        }
        return json($info);
    }

    /**
     * 用户支付密码验证
     * @param userid
     * @param access_token
     * @param paypassword
     * @return array|string
     */
    public function checkPayPassword()
    {
        $userid = input('param.userid');
        $password = input('param.paypassword');
        $access_token = input('param.access_token');
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }

        $info['status'] = false;
        if (empty($chk_info['userinfo']['pay_password'])) {
            $info['message'] = '用户支付密码未设置';
            $info['errnum'] = 1;
            return json($info);
        }
        if ($chk_info['userinfo']['pay_password'] !=md5($password)) {
            $info['message'] = '用户支付密码错误';
            $info['errnum'] = 2;
            return json($info);
        }
        $info['status'] = true;
        $info['message'] = '验证用户支付密码成功';
        $info['errnum'] = 0;
        return json($info);
    }

    /**
     * 查询余额
     * @param userid
     * @param access_token
     * @return array|string
     */
    public function showLeftMoney()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }

        $params = json(['userid'=>$chk_info['userinfo']['id'],'corp_id'=>$chk_info['corp_id']]);
        $b = \think\Hook::listen('check_over_time_red',$params);
        if (!$b[0]) {
            return json(['status'=>false,'errnum'=>1,'message'=>'账户余额查询请求失败，联系管理员']);
        }
        $res = $this->employM->getEmployer($userid);
        $left_money = $res['left_money'];
        $left_money = number_format($left_money/100, 2, '.', '');
        return json(['status'=>true,'message'=>'SUCCESS','errnum'=>0,'left_money'=>$left_money]);
    }

    /**
     * 设置用户支付宝账号
     * @param userid
     * @param access_token
     * @param alipay_account 支付宝账号，手机号或者邮箱格式
     * @return array|string
     */
    public function setAlipayAccount()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $alipay_account = input('param.alipay_account');
        $info['status'] = false;
        if (!check_alipay_account($alipay_account)) {
            $info['message'] = '支付宝账号格式不正确';
            $info['errnum'] = 1;
            return json($info);
        }
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        $data = ['alipay_account'=>$alipay_account];
        $r = $this->employM->setSingleEmployerInfobyId($chk_info['userinfo']['id'],$data);
        if ($r >= 0) {
            $info['status'] = true;
            $info['message'] = '设置支付宝账号成功';
            $info['errnum'] = 0;
        } else {
            $info['message'] = '设置支付宝账号失败';
            $info['errnum'] = 2;
        }
        return json($info);
    }


    /**
     * 用户从系统提现到个人支付宝
     * @param userid 用户tel
     * @param paypassword 支付密码
     * @param access_token
     * @param take_money 单位元，3.33
     * @return string
     */
    public function transMoney()
    {
        $userid = input('param.userid');
        $password = input('param.paypassword');
        $access_token = input('param.access_token');
        $take_money = input('param.take_money');
        $info['status'] = false;
        if (empty($take_money)) {
            $info['message'] = '提现金额不能为空';
            $info['errnum'] = 1;
            return json($info);
        }
        if (intval($take_money*100) < config('take_cash.min_money')) {
            $info['message'] = '提现金额过少';
            $info['errnum'] = 2;
            return json($info);
        }
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$take_money)) {
            $info['message'] = '提现金额格式不正确';
            $info['errnum'] = 3;
            return json($info);
        }
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        if (empty($chk_info['userinfo']['alipay_account'])) {
            $info['message'] = '您的支付宝收款账号未设置';
            $info['errnum'] = 4;
            return json($info);
        }
        if (empty($chk_info['userinfo']['pay_password'])) {
            $info['message'] = '您的支付密码未设置';
            $info['errnum'] = 5;
            return json($info);
        }
        if (md5($password) != $chk_info['userinfo']['pay_password']) {
            $info['message'] = '支付密码错误';
            $info['errnum'] = 6;
            return json($info);
        }
        $fen_money = intval($take_money*100);
        if ($chk_info['userinfo']['left_money'] < $fen_money) {
            $info['message'] = '余额不足，无法提现';
            $info['errnum'] = 7;
            return json($info);
        }
        $corp_left_money = Corporation::getCorporation($chk_info['corp_id']);
        if ($corp_left_money['corp_left_money'] < $fen_money) {
            $info['message'] = '贵公司账户余额不足，无法提现';
            $info['errnum'] = 8;
            return json($info);
        }
        $order_num = 'guguo_tran_money'.date('YmdHis',time()).time();
        $handle_cash = new TakeCashService();
        $trans_data = [
            'order_num' =>$order_num,
            'recv_account'=>$chk_info['userinfo']['alipay_account'],
            'take_money'    =>$take_money,
            'remark'    =>'用户提现，金额为'.$take_money.'元'
        ];
        $res = $handle_cash->handleCash($trans_data);
        if (!$res['status']) {
            return json($res);
        }

        //employer表更改
        $change_data = [
            'left_money'=>['exp',"left_money - $fen_money"]
        ];
        //take_cash表更改
        $record_data = [
            'userid'         =>$chk_info['userinfo']['id'],
            'take_money'     => -$fen_money,
            'status'          => 1,
            'alipay_account' =>$chk_info['userinfo']['alipay_account'],
            'took_time'       =>time(),
            'order_number'    =>$order_num,
            'remark'    =>'用户提现，金额为'.$fen_money.'分'
        ];
        //corporation表更改
        $de_corp_money = ['corp_left_money' =>['exp',"corp_left_money - $fen_money"]];
        //corporation_cash表更改
        $corp_cash_data = [
            'corp_id' =>$chk_info['userinfo']['corpid'],
            'money' => -$fen_money,
            'create_time' => time(),
            'status' => 1,
            'remark' =>'员工提现',
            'to_userid' =>$chk_info['userinfo']['id']
        ];
        $takeCashM = new TakeCashModel();
        $corp_cashM = new CorporationCash();
        $this->employM->link->startTrans();
        Corporation::startTrans();
        try{
            $de_money = $this->employM->setSingleEmployerInfobyId($chk_info['userinfo']['id'],$change_data);
            $take_cash = $takeCashM->addOrderNumber($record_data);
            $de_corp_money = Corporation::setCorporationInfo($chk_info['corp_id'],$de_corp_money);
            $corp_cash_rec = $corp_cashM->addCorporationCashInfo($corp_cash_data);
        }catch (\Exception $e){
            $this->employM->link->rollback();
            Corporation::rollback();
        }
        if ($de_money > 0 && $take_cash > 0 && $de_corp_money > 0 && $corp_cash_rec > 0) {
            $this->employM->link->commit();
            Corporation::commit();
            write_log($chk_info['userinfo']['id'],4,'用户提现，金额为'.$fen_money.'分',$chk_info['corp_id']);
            $info['status'] = true;
            $info['message'] = '用户提现成功，请登陆支付宝查看';
            $info['errnum'] = 0;
            $info['order_number'] = $order_num;
        } else {
            $this->employM->link->rollback();
            Corporation::rollback();
            $info['message'] = '用户提现成功，写入后台记录失败，请联系管理员';
            $info['errnum'] = 9;
            write_log($chk_info['userinfo']['id'],4,'用户提现，金额为'.$fen_money.'分',$chk_info['corp_id']);
            send_mail(config('system_email.user'),config('system_email.pass'),'wangqiwen@winbywin.com','提现问题',config('system_email.from_name'),'向员工转账成功，后台记录更改失败'.json_encode($trans_data,true));
        }
        return json($info);
    }

    /**
     * 用户之间转账
     * @param userid 转出tel
     * @param access_token
     * @param touserid 收款tel
     * @param paypassword 支付密码
     * @param money 金额，单位元 3.33
     * @return string
     */
    public function transMoneyUserToUser()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $to_user = input('param.touserid');
        $pay_pass = input('param.paypassword');
        $money = input('param.money');
        $take_money = intval($money*100);
        $info['status'] = false;

        if ($take_money < 1 ) {
            $info['message'] = '转账金额过少';
            $info['errnum'] = 1;
            return json($info);
        }
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$money)) {
            $info['message'] = '转账金额格式不正确';
            $info['errnum'] = 2;
            return json($info);
        }
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        if (!check_tel($to_user)) {
            $info['message'] = '用户名格式不正确';
            $info['errnum'] = 3;
            return $info;
        }
        $to_userinfo = $this->employM->getEmployer($to_user);
        if (empty($to_userinfo)) {
            $info['message'] = '接收转账的用户不存在';
            $info['errnum'] = 4;
            return json($info);
        }
        if (empty($chk_info['userinfo']['pay_password'])) {
            $info['message'] = '您的支付密码未设置';
            $info['errnum'] = 5;
            return json($info);
        }
        if (md5($pay_pass) != $chk_info['userinfo']['pay_password']) {
            $info['message'] = '支付密码错误';
            $info['errnum'] = 6;
            return json($info);
        }
        if ($chk_info['userinfo']['left_money'] < $take_money) {
            $info['message'] = '账户余额不足，无法转账';
            $info['errnum'] = 7;
            return json($info);
        }

        //用户余额减少
        $de_data = [
            'left_money' => ['exp',"left_money - $take_money"]
        ];
        //用户余额增加
        $in_data = [
            'left_money' => $to_userinfo['left_money'] + $take_money
        ];
        $time = time();
        //take_cash表转出记录
        $cash_from_data = [
            'userid'=>$chk_info['userinfo']['id'],
            'take_money'=>-$take_money,
            'status'=>1,
            'took_time'=>$time,
            'to_userid'=>$to_userinfo['id'],
            'remark' => '从余额转出'
        ];
        //take_cash表转入记录
        $cash_to_data = [
            'userid'=>$to_userinfo['id'],
            'take_money'=>$take_money,
            'status'=>2,
            'took_time'=>$time,
            'from_userid'=>$chk_info['userinfo']['id'],
            'remark' => '收到转账'
        ];
        $cashM = new TakeCash();
        $this->employM->link->startTrans();
        try{
            $de = $this->employM->setEmployerSingleInfo($userid,$de_data);
            $in = $this->employM->setEmployerSingleInfo($to_user,$in_data);
            $from_r = $cashM->addOrderNumber($cash_from_data);
            $to_r = $cashM->addOrderNumber($cash_to_data);
        }catch (\Exception $e) {
            $this->employM->link->rollback();
        }
        if ($de > 0 && $in > 0 && $from_r >0 && $to_r > 0) {
            $this->employM->link->commit();
            write_log($chk_info['userinfo']['id'],3,'用户app转账成功，转至用户id'.$to_userinfo['id'].',转账金额'.$take_money.'分',$chk_info['corp_id']);
            $info['status'] = true;
            $info['errnum'] = 0;
            $info['message'] = '转账成功';
        } else {
            $this->employM->link->rollback();
            write_log($chk_info['userinfo']['id'],3,'用户app转账失败，转至用户id'.$to_userinfo['id'].',转账金额'.$take_money.'分',$chk_info['corp_id']);
            $info['message'] = '转账失败';
            $info['errnum'] = 8;
        }
        return json($info);
    }

    /**
     * 验证用户id,token
     * @param $userid 用户tel
     * @param $access_token
     * @return array
     */
    public function checkUserAccess($userid, $access_token)
    {
        $info['status'] = false;
        if (empty($userid) || empty($access_token)) {
            $info['message'] = '用户id为空或token为空';
            $info['errnum'] = 101;
            return $info;
        }
        if (!check_tel($userid)) {
            $info['message'] = '用户名格式不正确';
            $info['errnum'] = 102;
            return $info;
        }
        $corp_id = get_corpid($userid);
        if ($corp_id == false) {
            $info['message'] = '用户不存在';
            $info['errnum'] = 103;
            return $info;
        }
        $this->employM = new Employer($corp_id);
        $userinfo = $this->employM->getEmployerByTel($userid);
        if ($userinfo['system_token'] != $access_token) {
            $info['message'] = 'token不正确，请重新登陆';
            $info['errnum'] = 104;
            return $info;
        }
        $info['message'] = 'SUCCESS';
        $info['status'] = true;
        $info['corp_id'] = $corp_id;
        $info['userinfo'] = $userinfo;
        return $info;
    }
}