<?php
/**
 * Created by messhair
 * Date: 17-2-17
 */
namespace app\huanxin\controller;

use think\Controller;
use app\huanxin\model\UserCorporation;
use app\huanxin\model\Employer;
use app\huanxin\service\Api;

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
        $friendsInfo = get_struct_name($friendsInfo, $corp_id);
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
        $code = 123456;//测试开启
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
            $employer->setEmployerSingleInfo($userid, $data);
            $info['status'] = true;
            $info['message'] = '修改成功，请重新登陆';
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
//        file_put_contents('/tmp/res.png',base64_decode($user_pic));
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
     * 验证用户id,token
     * @param $userid
     * @param $access_token
     * @return array
     */
    protected function checkUserAccess($userid, $access_token)
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
        return $info;
    }
}