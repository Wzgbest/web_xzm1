<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\common\controller\Initialize;
use think\Db;
use think\Controller;
use app\common\model\Employee;
use app\common\model\StructureEmployee;
use app\common\model\Meme;
use app\common\model\RoleRule;
use app\index\service\TQCallApi;
use app\index\model\SystemMessage as SystemMessageModel;

class Call extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        $call_config_result = $this->_get_tq_config();
//        var_exp($call_config_result,'$call_config_result',1);
        $this->assign("call_config",$call_config_result["data"]);
        return view();
    }
    public function getTqConfig(){
        $call_config_result = $this->_get_tq_config();
        return json($call_config_result);
    }
    protected function _get_tq_config(){
        $result = ['status'=>0 ,'info'=>"获取配置时发生错误！"];
        $userinfo = get_userinfo();
//        var_exp($userinfo["userinfo"],'$userinfo["userinfo"]',1);
        if(!$userinfo["userinfo"]["tq_uin"]||!$userinfo["userinfo"]["tq_strid"]){
            $result["info"] = "你不能打电话!";
            return $result;
        }
        $call_config = false;//get_cache_by_tel($this->telephone,"call_config");
        if(!$call_config){
            $tq_config = config('tq');
            $call_config["appid"] = $tq_config["appid"];
            $call_config["appkey"] = $tq_config["appkey"];
            $call_config["secretkey"] = strtoupper(md5($tq_config["appid"]."*(**)*".$tq_config["appkey"]));
            $call_config["admin_uin"] = $tq_config["admin_uin"];// "9796221";
            $call_config["uin"] = $userinfo["userinfo"]["tq_uin"];// "9796249";
            $call_config["strid"] = $userinfo["userinfo"]["tq_strid"];// "sdzhcs2";
            $call_config["time"] = time();
            $tqCallApi = new TQCallApi();
            $access_token_data = $tqCallApi->get_access_token($call_config["admin_uin"],$call_config["uin"],$call_config["strid"],$call_config["time"]);
//            var_exp($access_token_data,'$access_token_data',1);
            if(isset($access_token_data["errcode"])&&$access_token_data["errcode"]==0){
                $call_config["access_token"] = $access_token_data["access_token"];
                set_cache_by_tel($this->telephone,"call_config",$call_config,$tq_config["expire"]?:null);
            }else{
                $result["info"] = "电话TOKEN获取失败!";
                return $result;
            }
        }
        if(!$call_config){
            $result["info"] = "电话参数获取失败!";
            return $result;
        }
        $result["status"] = 1;
        $result["info"] = "电话参数获取成功!";
        $result["data"] = $call_config;
        return $result;
    }

    public function tq_webservice(){
        $result = ['status'=>0 ,'info'=>"调用远程方法时发生错误！"];
        $func_name = input("func_name","","string");
        $params = input("params/a","","string");
        if(!$func_name){
            $result["info"] = "方法参数错误!";
            return json($result);
        }
        if(empty($params)){
            $result["info"] = "参数列表错误!";
            return json($result);
        }
        //$params = ["sdzhcs1","9796221",md5("123456")];//e10adc3949ba59abbe56e057f20f883e
        //var_exp($func_name,'$func_name');
//        var_exp($params,'$params');

        $call_config = false;//get_cache_by_tel($this->telephone,"call_config");
        if(!$call_config) {
            $tq_config = config('tq');
            $call_config["admin_uin"] = $tq_config["admin_uin"];
            $call_config["admin_password"] = $tq_config["admin_password"];
            set_cache_by_tel($this->telephone,"call_config",$call_config,$tq_config["expire"]?:null);
        }
        foreach ($params as &$param){
            switch ($param){
                case '[adminuin]':
                    $param = $call_config["admin_uin"];
                    break;
                case '[adminpassword]':
                    $param = md5($call_config["admin_password"]);
                    break;
            }
        }
        //var_exp($params,'$params',1);
        $url="http://webservice.agent.tq.cn/Servers/services/ServerNew?wsdl";
        $client = new \SoapClient($url);
        $aryResult = call_user_func_array([$client,$func_name],$params);
        $result["status"] = 1;
        $result["info"] = "执行成功!";
        $result["data"] = $aryResult;
        return json($result);
    }
}
