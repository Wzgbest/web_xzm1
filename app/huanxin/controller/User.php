<?php
/**
 * Created by messhair
 * Date: 17-2-17
 */
namespace app\huanxin\controller;

use app\common\model\UserCorporation;
use app\common\model\Employer;
use app\huanxin\service\Api;
use app\huanxin\service\OverTimeRedEnvelope;

class User
{
    protected $employM;

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
            return $chk_info;
        }
        $friendsInfo = $this->employM->getAllUsers();
        $friendsInfo = get_struct_name($friendsInfo, $chk_info['corp_id']);
        $info['message'] = 'SUCCESS';
        $info['status'] = true;
        $info['friendsInfo'] = $friendsInfo;
        return json_encode($info, true);
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
            return $chk_info;
        }
        $friends_tel = $this->employM->getAllTels();
        $info = ['status' => true, 'message' => 'SUCCESS', 'friendsTel' => $friends_tel];
        return json_encode($info, true);
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
            return json_encode($info, true);
        }
        if (!get_corpid($userid)) {
            $info['message'] = '非系统用户';
            return json_encode($info,true);
        }
        $code = rand(100000, 999999);
        $code = 123456;//TODO 测试开启
        $content = '【咕果】感谢您使用本产品，您的手机验证码为' . $code . '请及时填写';
        $res = send_sms($userid, $code, $content);
        if ($res['status'] == false) {
            $info['message'] = $res['message'];
            return json_encode($info, true);
        }
        $info['message'] = '发送成功';
        $info['status'] = true;
        return json_encode($info, true);
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
            $info['message'] = '手机验证码不正确';
            return json_encode($info, true);
        }
        if (!check_tel($userid)) {
            $info['message'] = '手机号码格式不正确';
            return json_encode($info, true);
        }
        $corp_id = get_corpid($userid);
        if (!$corp_id) {
            $info['message'] = '非系统用户';
            return json_encode($info,true);
        }
        $ini_code = session('reset_code' . $userid);
        if ($ini_code != $code) {
            $info['message'] = '手机验证码不正确';
            return json_encode($info, true);
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
            session('reset_code'.$userid, null);
        } else {
            $info['message'] = '修改环信密码失败，联系管理员';
        }
        return json_encode($info, true);
    }

    /**
     * 修改app头像
     * @return array|mixed|string
     */
    public function modifyUserPic()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $user_pic = input('param.userpic');
        $info['status'] = false;
        if ($user_pic == '') {
            $info['message'] = '未设置头像';
            return json_encode($info,true);
        }
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return $chk_info;
        }
        $img_path = get_app_img($user_pic);
        if (false ==$img_path['status']) {
            return $img_path;
        }
        $data = ['userpic'=>$img_path['imgurl']];
        $res = $this->employM->setEmployerSingleInfo($userid,$data);
        return json_encode($img_path,true);
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
            return $chk_info;
        }
        $paypass = $this->employM->getEmployer($userid);
        if (!empty($paypass['pay_password'])) {
            return json_encode(['status'=>false,'message'=>'支付密码已设置，请勿重复设置'],true);
        }
        $info['status'] = false;
        $r = $this->employM->setEmployerSingleInfo($userid,['pay_password'=>md5($password)]);
        if ($r >0) {
            $info['message'] = '支付密码设置成功';
            $info['status'] = true;
        } else {
            $info['message'] = '支付密码设置失败';
        }
        return json_encode($info,true);
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
            return json_encode($info, true);
        }
        if (empty($newpass)) {
            $info['message'] = '支付密码不能为空';
            return json_encode($info, true);
        }
        if (!check_tel($userid)) {
            $info['message'] = '手机号码格式不正确';
            return json_encode($info, true);
        }
        $corp_id = get_corpid($userid);
        if (!$corp_id) {
            $info['message'] = '非系统用户';
            return json_encode($info,true);
        }
        $ini_code = session('reset_code' . $userid);
        if ($ini_code != $code) {
            $info['message'] = '手机验证码不正确';
            return json_encode($info, true);
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
            $info['message'] = '修改支付密码成功';
        } else {
            $info['message'] = '修改支付密码失败';
        }
        return json_encode($info, true);
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
            return $chk_info;
        }

        $info['status'] = false;
        if (empty($chk_info['userinfo']['pay_password'])) {
            $info['message'] = '用户支付密码未设置';
            return json_encode($info,true);
        }
        if ($chk_info['userinfo']['pay_password'] !=md5($password)) {
            $info['message'] = '用户支付密码错误';
            return json_encode($info,true);
        }
        $info['status'] = true;
        $info['message'] = '验证用户支付密码成功';
        return json_encode($info,true);
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
            return $chk_info;
        }
        $redM = new OverTimeRedEnvelope($chk_info['userinfo']['id'],$chk_info['corp_id']);
        $b = $redM->sendBackOverTimeRed();
        if (!$b) {
            return json_encode(['status'=>false,'message'=>'账户余额查询请求失败，联系管理员'],true);
        }
        $res = $this->employM->getEmployer($userid);
        $left_money = $res['left_money'];
        $left_money = number_format($left_money/100, 2, '.', '');
        return json_encode(['status'=>true,'message'=>'SUCCESS','left_money'=>$left_money],true);
    }

    public function setAlipayAccount()
    {
        //TODO
    }

    public function depositMoney()
    {
        //TODO
    }

    public function transMoney()
    {
        $userid = input('param.userid');
        $password = input('param.paypassword');
        $access_token = input('param.access_token');
        $take_money = input('param.take_money');
        $info['status'] = false;
        if (empty($take_money)) {
            $info['message'] = '提现金额不能为空';
            return json_encode($info,true);
        }
        if (intval($take_money*100) < config('take_cash.min_money')) {
            $info['message'] = '提现金额过少';
            return json_encode($info,true);
        }
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$take_money)) {
            $info['message'] = '提现金额格式不正确';
            return json_encode($info,true);
        }
        $chk_info = $this->checkUserAccess($userid, $access_token);
        if (!$chk_info['status']) {
            return $chk_info;
        }
        if (empty($chk_info['alipay_account'])) {
            $info['message'] = '您的支付宝收款账号未设置';
            return json_encode($info,true);
        }
        if (empty($chk_info['pay_password'])) {
            $info['message'] = '您的支付密码未设置';
            return json_encode($info,true);
        }
        if (md5($password) != $chk_info['pay_password']) {
            $info['message'] = '支付密码错误';
            return json_encode($info,true);
        }
        $take_money = intval($take_money*100);
        if ($chk_info['take_money'] < $take_money) {
            $info['message'] = '余额不足，无法提现';
            return json_encode($info,true);
        }

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
            return $info;
        }
        if (!check_tel($userid)) {
            $info['message'] = '用户名格式不正确';
            return $info;
        }
        $corp_id = get_corpid($userid);
        if ($corp_id == false) {
            $info['message'] = '用户不存在';
            return $info;
        }
        $this->employM = new Employer($corp_id);
        $userinfo = $this->employM->getEmployer($userid);
        if ($userinfo['system_token'] != $access_token) {
            $info['message'] = 'token不正确，请重新登陆';
            return $info;
        }
        $info['message'] = 'SUCCESS';
        $info['status'] = true;
        $info['corp_id'] = $corp_id;
        $info['userinfo'] = $userinfo;
        return $info;
    }
}