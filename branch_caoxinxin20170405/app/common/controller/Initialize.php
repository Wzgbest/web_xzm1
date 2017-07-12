<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\common\controller;

use think\Controller;
use app\common\model\Employee;

class Initialize extends Controller
{
    protected $corp_id;
    public function _initialize(){
        $userinfo = get_userinfo();
        if (empty($userinfo)) {
            $userid = input('userid');
            $access_token = input('access_token');
            $info['status'] = false;
            if (empty($userid) || empty($access_token)) {
                $info['message'] = '用户id为空或token为空';
                $info['errnum'] = 101;
                $this->sendErrorToApp($info);
            }
            if (!check_tel($userid)) {
                $info['message'] = '用户名格式不正确';
                $info['errnum'] = 102;
                $this->sendErrorToApp($info);
            }
            $corp_id = get_corpid($userid);
            if ($corp_id == false) {
                $info['message'] = '用户不存在';
                $info['errnum'] = 103;
                $this->sendErrorToApp($info);
            }
            $this->employM = new Employee($corp_id);
            $userinfo = $this->employM->getEmployeeByTel($userid);
            if ($userinfo['system_token'] != $access_token) {
                $info['message'] = 'token不正确，请重新登陆';
                $info['errnum'] = 104;
                $this->sendErrorToApp($info);
            }
            $userinfo = set_userinfo($corp_id,$userid,$userinfo);
        }
        if (empty($userinfo)) {
            $this->redirect('/login/index/index');
        }
        $this->corp_id = get_corpid();
    }
    protected function sendErrorToApp($info){
        echo json_encode($info);
        exit;
    }
}