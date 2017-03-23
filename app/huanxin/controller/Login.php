<?php
namespace app\huanxin\controller;

use think\Controller;
use app\common\model\Employer;
use app\common\model\UserCorporation;
use app\common\model\EmployerScore;
use app\common\model\Occupation;
use think\Request;

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
//        file_put_contents('d:/hu.txt',json_encode($input,true),FILE_APPEND);
        $telephone = trim($input['telephone']);
        $password = trim($input['password']);
        $ip = $this->request->ip();
        $req_reg['status'] = false;
        if ($telephone == '' || $password == '') {
            $req_reg['message'] = '缺少必填信息';
            $req_reg['errnum'] = 1;
            return json_encode($req_reg, true);
        }
        if (!check_tel($telephone)) {
            $req_reg['message'] = '手机号码格式不正确';
            $req_reg['errnum'] = 2;
            return json_encode($req_reg,true);
        }
        $corp_id = UserCorporation::getUserCorp($telephone);
        if (empty($corp_id)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            $req_reg['errnum'] = 3;
            return json_encode($req_reg, true);
        }
        $model = new Employer($corp_id);
        $user_arr = $model->getEmployer($telephone);
        if (empty($user_arr)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            $req_reg['errnum'] = 3;
            return json_encode($req_reg, true);
        }
        if ($user_arr['password'] != md5($password)) {
            $req_reg['message'] = '密码错误';
            $req_reg['errnum'] = 4;
            return json_encode($req_reg, true);
        }
        if (empty($user_arr['lastlogintime'])) {
            $req_reg['message'] = '用户首次登陆，请修改密码';
            $req_reg['errnum'] = 5;
            return json_encode($req_reg,true);
        }
        //创建用户token，返回给app客户端
        $save_res=$model->createSystemToken($telephone);
        if($save_res['res']>0){
            $req_reg['access_token'] = $save_res['system_token'];
        }else{
            $req_reg['message'] = '获取token信息失败，联系网站后台管理员';
            $req_reg['errnum'] = 6;
            return json_encode($req_reg, true);
        }
        //获取用户积分
        $scoreM = new EmployerScore($corp_id);
        $score=$scoreM->getEmployerScore($user_arr['id']);
        //积分占比
        $per=$scoreM->getScoreListPer($score['score']);
        //公司职位
        $occuM = new Occupation($corp_id);
        $occup = $occuM->getOccupation($user_arr['id']);
        $data =['lastloginip'=>$ip,'lastlogintime'=>time()];
        if ($model->setEmployerSingleInfo($telephone,$data) <= 0) {
            $reg_reg['message'] = '登陆信息写入失败，联系管理员';
            $reg_reg['errnum'] = 7;
            return json_encode($reg_reg,true);
        }
        $req_reg['message'] = 'SUCCESS';
        $req_reg['status'] = true;
        $req_reg['nickname'] = $user_arr['truename'];
        $req_reg['userpic'] = $user_arr['userpic'];
        $req_reg['userscore'] = $score['score'];
        $req_reg['title'] = $score['title'];
        $req_reg['occupation'] = $occup['occu_name'];
        $req_reg['percentage'] = $per;
        return json_encode($req_reg, true);
    }
}