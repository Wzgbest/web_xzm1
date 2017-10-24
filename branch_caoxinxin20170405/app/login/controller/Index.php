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
    public function index(){
        return view();
    }

    /**
     * web端登录验证
     * @return mixed
     * created by messhair
     */
    public function verifyLogin(){
        $input = input('param.');
        $telephone = trim($input['telephone']);
        $password = trim($input['password']);
        $device_type = 1;
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
        set_token_to_cookie($result['access_token']);
        if(!$result["status"]){
            $req_reg["message"] = $result["message"];
            $req_reg["errnum"] = $result["errnum"];
            return json($req_reg);
        }
        $this->redirect('index/index/index');
        
//        $req_reg['message'] = '登录成功!';
//        $req_reg['errnum'] = 0;
//        $req_reg['status'] = true;
//        return $req_reg;
    }
    public function logout(){
        logout();
        set_token_to_cookie(null);
        $this->redirect('index/index/index');
    }
}