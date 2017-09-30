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

class Login extends Controller
{
    public function index()
    {
        return 'index';
    }

    /**
     * app端登录
     * @param telephone
     * @param password
     * @return string
     */
    public function verifyLogin()
    {
        $input = input('param.');
        $telephone = trim($input['telephone']);
        $password = trim($input['password']);
        $ip = $this->request->ip();
        $req_reg['status'] = false;
        if ($telephone == '' || $password == '') {
            $req_reg['message'] = '缺少必填信息';
            $req_reg['errnum'] = 1;
            return json($req_reg);
        }
        if (!check_tel($telephone)) {
            $req_reg['message'] = '手机号码格式不正确';
            $req_reg['errnum'] = 2;
            return json($req_reg);
        }
        $corp_id = get_corpid($telephone);
        if (empty($corp_id)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            $req_reg['errnum'] = 3;
            return json($req_reg);
        }
        //验证用户信息
        $model = new Employee($corp_id);
        $user_arr = $model->getEmployeeByTel($telephone);
        if (empty($user_arr)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            $req_reg['errnum'] = 3;
            return json($req_reg);
        }
        if ($user_arr['password'] != md5($password)) {
            $req_reg['message'] = '密码错误';
            $req_reg['errnum'] = 4;
            return json($req_reg);
        }
        if (empty($user_arr['lastlogintime'])) {
            $req_reg['message'] = '用户首次登陆，请修改密码';
            $req_reg['errnum'] = 5;
            return json($req_reg);
        }
        //创建用户token，返回给app客户端
        $save_res=$model->createSystemToken($telephone);
        if($save_res['res']>0){
            $req_reg['access_token'] = $save_res['system_token'];
        }else{
            $req_reg['message'] = '获取token信息失败，联系网站后台管理员';
            $req_reg['errnum'] = 6;
            return json($req_reg);
        }
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

        //更新登录信息
        $data =['lastloginip'=>$ip,'lastlogintime'=>time()];
        if ($model->setEmployeeSingleInfo($telephone,$data) <= 0) {
            $reg_reg['message'] = '登录信息写入失败，联系管理员';
            $reg_reg['errnum'] = 7;
            return json($reg_reg);
        }

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
}