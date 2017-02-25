<?php
namespace app\huanxin\controller;

use think\Controller;
use app\huanxin\model\Employer;
use app\huanxin\model\UserCorporation;
use app\huanxin\model\EmployerScore;
use app\huanxin\model\Occupation;

class Login extends Controller
{
    public function index()
    {
        return 'index';
    }

    public function verifyLogin()
    {
        $input = input('param.');
//        file_put_contents('d:/hu.txt',json_encode($input,true),FILE_APPEND);
        $telephone = trim($input['telephone']);
        $password = trim($input['password']);
        $req_reg['status'] = false;
        if ($telephone == '' || $password == '') {
            $req_reg['message'] = '缺少必填信息';
            return json_encode($req_reg, true);
        }
        if (!check_tel($telephone)) {
            $req_reg['message'] = '手机号码格式不正确';
            return json_encode($req_reg,true);
        }
        $corp_id = UserCorporation::getUserCorp($telephone);
        if (empty($corp_id)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            return json_encode($req_reg, true);
        }
        $model = new Employer($corp_id);
        $user_arr = $model->getEmployer($telephone);
        if (empty($user_arr)) {
            $req_reg['message'] = '用户不存在或用户未划分公司归属';
            return json_encode($req_reg, true);
        }
        if ($user_arr['password'] != md5($password)) {
            $req_reg['message'] = '密码错误';
            return json_encode($req_reg, true);
        }

        $save_res=$model->createSystemToken($telephone);
        if($save_res['res']>0){
            $req_reg['access_token'] = $save_res['system_token'];
        }else{
            $req_reg['message'] = '获取token信息失败，联系网站后台管理员';
            return json_encode($req_reg, true);
        }
        $scoreM = new EmployerScore($corp_id);
        $score=$scoreM->getEmployerScore($user_arr['id']);
        $per=$scoreM->getScoreListPer($score['score']);
        $occuM = new Occupation($corp_id);
        $occup = $occuM->getOccupation($user_arr['id']);
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