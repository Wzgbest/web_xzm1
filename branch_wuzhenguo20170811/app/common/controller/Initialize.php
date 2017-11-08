<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\common\controller;

use think\Controller;
use app\common\model\Employee;
use app\common\model\UserCorporation;
use app\common\model\RoleRule;
use think\Request;

class Initialize extends Controller
{
    protected $corp_id;
    protected $uid;
    protected $telephone;
    protected $access_token;
    protected $device_type;
    protected $rule_white_list;

    public function _initialize(){
        $telephone = input('userid','',"string");
        $access_token = input('access_token','',"string");
        $cookie_access_token = get_token_by_cookie();
        if (is_web($telephone,$access_token,$cookie_access_token)){
            $this->device_type = 1;
            $this->access_token = $cookie_access_token;
            //var_exp($this->access_token,'$this->access_token',1);
            if(!$this->access_token){
                $this->redirectToLogin();
            }
            $this->telephone = get_telephone_by_token($this->access_token);
        }else{
            if (!($telephone&&$access_token)){
                $info['message'] = '用户id为空或token为空';
                $info['errnum'] = 101;
                $this->returnAjaxError($info);
            }
            $this->telephone = $telephone;
            $this->access_token = $access_token;
        }
        //var_exp($this->telephone,'$this->telephone');
        //var_exp($this->access_token,'$this->access_token',1);
        $info = check_telephone_and_token($this->telephone,$this->access_token);
        if(!$this->device_type){
            $this->device_type = $info["device_type"];
        }
        if($info["status"]==false) {
            if($this->device_type==1){
                $this->redirectToLogin();
            }else{
                $this->returnAjaxError($info);
            }
        }

        $this->uid = $info["userinfo"]["id"];
        $this->corp_id = $info["corp_id"];
        set_userinfo($this->corp_id,$this->telephone,$info["userinfo"]);

        //权限白名单
        $this->rule_white_list = [
            "index/index/index",
            "datacount/index/summary",
            "index/index/map",
            "index/index/select_window",
            "index/index/developing",
        ];

        $request = Request::instance();
        $path = $request->path();
//        var_exp($path,'$path');
        $path_arr = explode("/",$path);
        $rule_name_arr = [];
        for($i=0;$i<3;$i++){
            if(!isset($path_arr[$i])){
                break;
            }
            $rule_name_arr[$i] = $path_arr[$i];
        }
//        var_exp($rule_name_arr,'$rule_name_arr');
        $rule_name = implode("/",$rule_name_arr);
        if(!$this->checkRule($rule_name)){
            $this->noRole();
        }
    }
    protected function checkRule($rule_name){
        if(in_array($rule_name,$this->rule_white_list)){
            return true;
        }
        $check_flg = false;
        $hav_rules = false;//get_cache_by_tel($this->telephone,"hav_rules");
        if(!$hav_rules){
            $roleRuleM = new RoleRule($this->corp_id);
            $hav_rules = $roleRuleM->getRuleNamesByUid($this->uid);
            set_cache_by_tel($this->telephone,"hav_rules",$hav_rules,600);
        }
//        var_exp($hav_rules,'$hav_rules');
        if(in_array($rule_name,$hav_rules)){
            $check_flg = true;
        }
        return $check_flg;
    }
    protected function returnAjaxError($info){
        echo json_encode($info);
        exit;
    }
    public function redirectToLogin(){
        $this->redirect('/login/index/index');
    }
    public function noRole(){
        //TODO 无访问权限返回页面
        if($this->device_type==1){
            $this->error("没有权限!");
        }else{
            $info['message'] = '没有权限';
            $info['errnum'] = 1;
            $this->returnAjaxError($info);
        }
    }
}