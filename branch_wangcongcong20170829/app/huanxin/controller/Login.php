<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\huanxin\controller;

use think\Controller;
use app\common\model\Employee;
use app\common\model\EmployeeScore;
use app\common\model\RoleEmployee;
use app\common\model\StructureEmployee;
use app\huanxin\service\Api;

class Login extends Controller{
    public function index(){
        return 'index';
    }

    /**
     * app端登录
     * @param telephone
     * @param password
     * @return string
     */
    public function verifyLogin(){
        $input = input('param.');
        $telephone = trim($input['telephone']);
        $password = trim($input['password']);
        $device_type = input('device_type',0,'int');
        $ip = $this->request->ip();
        $result = check_telphone_and_password($telephone,$password);
        if(!$result["status"]){
            $req_reg["message"] = $result["message"];
            $req_reg["errnum"] = $result["errnum"];
            return json($req_reg);
        }
        $corp_id = $result["corp_id"];
        $user_arr = $result["user_info"];
        $result = login($corp_id,$user_arr["id"],$telephone,$device_type,$ip);
        if(!$result["status"]){
            $req_reg["message"] = $result["message"];
            $req_reg["errnum"] = $result["errnum"];
            return json($req_reg);
        }
        $req_reg["access_token"] = $result["access_token"];

        set_online($telephone,true,$device_type);

        //获取用户积分
        $scoreM = new EmployeeScore($corp_id);
        $score=$scoreM->getEmployeeScore($user_arr['id']);
        //积分占比
        $per=$scoreM->getScoreListPer($score['score']);

        //获取用户在公司职位
        $roleEM = new RoleEmployee($corp_id);
        $roleList = $roleEM->getRolebyEmployeeId($user_arr['id']);


        $structureEmployeeModel = new StructureEmployee($corp_id);
        $structure = $structureEmployeeModel->findEmployeeStructure($user_arr['id']);

        //所有员工信息
        //$data_all = $model->getAllUsers();
        //cache('employee_info'.$telephone,null);
        $req_reg['message'] = 'SUCCESS';
        $req_reg['status'] = true;
        $req_reg['errnum'] = 0;
        $req_reg['nickname'] = $user_arr['truename'];
        $req_reg['userpic'] = $user_arr['userpic'];
        $req_reg['userscore'] = $score['score'];
        $req_reg['title'] = $score['title'];
        $req_reg['occupation'] = $roleList;
        $req_reg['percentage'] = $per;
        //$req_reg['totaluser'] = $data_all;
        $req_reg['structure'] = $structure;
        $req_reg['loginname'] = $corp_id."_".$user_arr['id'];
        return json($req_reg);
    }

    public function verifyOnline(){
        $input = input('param.');
        $telephone = trim($input['telephone']);
        $password = trim($input['password']);
        $device_type = input('device_type',0,'int');
        $result = check_telphone_and_password($telephone,$password);
        if(!$result["status"]){
            $req_reg["message"] = $result["message"];
            $req_reg["errnum"] = $result["errnum"];
            return json($req_reg);
        }
        $req_reg['message'] = 'SUCCESS';
        $req_reg['status'] = true;
        $req_reg['data'] = get_online($telephone,$device_type);
        return json($req_reg);
    }

    public function logout(){
        $telephone = input('userid','',"string");
        $access_token = input('access_token','',"string");
        $req_reg['status'] = false;
        $info = check_telephone_and_token($telephone,$access_token);
        if($info["status"]==false) {
            $req_reg["message"] = $info["message"];
            $req_reg["errnum"] = $info["errnum"];
            return json($req_reg);
        }
        $device_type = $info["device_type"];
        logout($telephone,$access_token);
        set_online($telephone,false,$device_type);
        $req_reg['message'] = 'SUCCESS';
        $req_reg['status'] = true;
        return json($req_reg);
    }

    /**
     * 生成短信验证码，返回app
     * @return string {"status":true/false,"message":""}
     */
    public function getResetSmsCode(){
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
        $code = str_pad(rand(0, 999999),4,"0",STR_PAD_LEFT);
        //var_exp($code,'$code',1);
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
        $newpass = md5(input('param.newpassword'));
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
        $ini_code = get_reset_code($userid);
        if ($ini_code != $code) {
            $info['message'] = '手机验证码不正确';
            $info['errnum'] = 4;
            return json($info);
        }
        $employee = new Employee($corp_id);
        $r_userid = $employee->getEmployeeByTel($userid);
        $apiM = new Api();
        $reset = $apiM ->resetPassword($corp_id."_".$r_userid["id"],$newpass);
//        $res = action('Api/resetPassword',['user'=>$userid,'newpass'=>$newpass]);
//        dump($res);exit;
        if ($reset['action']=='set user password') {
            $data['password'] = $newpass;
            $corp_id = get_corpid($userid);
            if($r_userid["lastlogintime"]==0){
                $data['lastlogintime'] = 1;
            }
            $employee->setEmployeeSingleInfo($userid, $data);
            write_log($r_userid['id'],1,'用户修改登录密码',$corp_id);
            $info['status'] = true;
            $info['message'] = '修改成功，请重新登陆';
            $info['errnum'] = 0;
            set_reset_code($userid,null);
        } else {
            $info['message'] = '修改环信密码失败，联系管理员';
            $info['errnum'] = 5;
        }
        return json($info);
    }
}
