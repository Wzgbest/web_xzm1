<?php
/**
 * Created by: messhair
 * Date: 2017/5/5
 */
namespace app\login\controller;

use app\common\model\UserCorporation;
use app\common\model\Employee;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return view();
    }

    /**
     * web端登录验证
     * @return mixed
     * created by messhair
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
            return $req_reg;
        }
        if (!check_tel($telephone)) {
            $req_reg['message'] = '手机号码格式不正确';
            $req_reg['errnum'] = 2;
            return $req_reg;
        }
        $corp_id = UserCorporation::getUserCorp($telephone);
        if (empty($corp_id)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            $req_reg['errnum'] = 3;
            return $req_reg;
        }
        $model = new Employee($corp_id);
        $user_arr = $model->getEmployeeByTel($telephone);
        if (empty($user_arr)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            $req_reg['errnum'] = 3;
            return $req_reg;
        }
        if ($user_arr['password'] != md5($password)) {
            $req_reg['message'] = '密码错误';
            $req_reg['errnum'] = 4;
            return $req_reg;
        }
        if (empty($user_arr['lastlogintime'])) {
            $req_reg['message'] = '用户首次登陆，请修改密码';
            $req_reg['errnum'] = 5;
            return $req_reg;
        }
        $data =['lastloginip'=>$ip,'lastlogintime'=>time()];
        if ($model->setEmployeeSingleInfo($telephone,$data) <= 0) {
            $reg_reg['message'] = '登录信息写入失败，联系管理员';
            $reg_reg['errnum'] = 7;
            return $reg_reg;
        }
        set_userinfo($corp_id,$telephone,$user_arr);


        $this->redirect('index/index/index');
        
//        $req_reg['message'] = '登录成功!';
//        $req_reg['errnum'] = 0;
//        $req_reg['status'] = true;
//        return $req_reg;
    }
    public function logout(){
        logout();
        $this->redirect('index/index/index');
    }
}