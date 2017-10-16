<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\common\controller;

use think\Controller;
use app\common\model\Employee;
use app\common\model\UserCorporation;

class Initialize extends Controller
{
    protected $corp_id;
    protected $uid;
    protected $telephone;
    protected $access_token;
    protected $device_type;

    public function _initialize(){
        $this->telephone = input('userid','',"string");
        $this->access_token = input('access_token','',"string");
        if (!($this->telephone && $this->access_token)) {
            $this->device_type = 1;
            $this->access_token = get_token_by_cookie();
            //var_exp($this->access_token,'$this->access_token',1);
            if(!$this->access_token){
                $this->redirectToLogin();
            }
            $this->telephone = get_telephone_by_token($this->access_token);
        }
        //var_exp($this->telephone,'$this->telephone');
        //var_exp($this->access_token,'$this->access_token',1);
        $info = check_telephone_and_token($this->telephone,$this->access_token);
        $this->device_type = $info["device_type"];
        if($info["status"]==false) {
            if($this->device_type==1){
                $this->redirectToLogin();
            }else{
                $this->return_error($info);
            }
        }

        $this->uid = $info["userinfo"]["id"];
        $this->corp_id = $info["corp_id"];
        set_userinfo($this->corp_id,$this->telephone,$info["userinfo"]);
    }
    protected function return_error($info){
        echo json_encode($info);
        exit;
    }
    public function redirectToLogin(){
        $this->redirect('/login/index/index');
    }
}