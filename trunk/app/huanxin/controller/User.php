<?php
/**
 * Created by messhair
 * Date: 17-2-17
 */
namespace app\huanxin\controller;

use think\Controller;
use app\huanxin\model\UserCorporation;
use app\huanxin\model\Employer;

class User
{
    /**
     * 根据用户id,access_token 获取所有用户列表
     * @return string
     *  ["telephone"] => string(11) "13322223333"
        ["userpic"] => string(53) "http://mat1.gtimg.com/www/images/qq2012/qqlogo_1x.png"
        ["nickname"] => string(31) "zhongxun_xiaoshou_jack_nickname"
        ["occupation"] => string(1) "6"
        ["structid"] => string(12) "销售一部"
     */
    public function getFriendsInfo()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $info['status'] = false;
        if (empty($userid) || empty($access_token)) {
            $info['message'] = '用户id为空或token为空';
            return json_encode($info,true);
        }
        if (!check_tel ($userid)) {
            $info['message'] = '用户名格式不正确';
            return json_encode($info,true);
        }
        $corp_id = UserCorporation::getUserCorp($userid);
        if (empty($corp_id)) {
            $info['message'] = '用户不存在';
            return json_encode($info,true);
        }
        $employM = new Employer($corp_id);
        $userinfo = $employM->getEmployer($userid);
        if ($userinfo['system_token'] != $access_token) {
            $info['message'] ='token不正确，请重新登陆';
            return json_encode($info,true);
        }
        $friendsInfo = $employM->getAllUsers();
        $friendsInfo = get_struct_name($friendsInfo,$corp_id);
        $info['message'] = 'SUCCESS';
        $info['status'] = true;
        $info['friendsInfo'] = $friendsInfo;
        return json_encode($info,true);
    }

    /**
     * 根据用户userid,access_token查找所有其他telephone
     * @return string
     */
    public function getFriendsTelephone()
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $info['status'] = false;
        if (empty($userid) || empty($access_token)) {
            $info['message'] = '用户id为空或token为空';
            return json_encode($info,true);
        }
        if (!check_tel ($userid)) {
            $info['message'] = '用户名格式不正确';
            return json_encode($info,true);
        }
        $corp_id = UserCorporation::getUserCorp($userid);
        if (empty($corp_id)) {
            $info['message'] = '用户不存在';
            return json_encode($info,true);
        }
        $employM = new Employer($corp_id);
        $userinfo = $employM->getEmployer($userid);
        if ($userinfo['system_token'] != $access_token) {
            $info['message'] ='token不正确，请重新登陆';
            return json_encode($info,true);
        }
        $friends_tel=$employM->getAllTels();
        $info=['status' => true,'message'=>'SUCCESS','friendsTel'=>$friends_tel];
        return json_encode($info,true);
    }
}