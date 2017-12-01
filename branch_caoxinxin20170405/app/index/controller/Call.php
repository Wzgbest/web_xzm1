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
use app\crm\model\CallRecord;

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
        $call_config = get_cache_by_tel($this->telephone,"call_config");
        if(!$call_config){
            $tq_config = config('tq');
            $call_config_tmp["appid"] = $tq_config["appid"];
            $call_config_tmp["appkey"] = $tq_config["appkey"];
            $call_config_tmp["secretkey"] = strtoupper(md5($tq_config["appid"]."*(**)*".$tq_config["appkey"]));
            $call_config_tmp["admin_uin"] = $tq_config["admin_uin"];// "9796221";
            $call_config_tmp["admin_password"] = $tq_config["admin_password"];// "123456";
            $call_config_tmp["uin"] = $userinfo["userinfo"]["tq_uin"];// "9796249";
            $call_config_tmp["strid"] = $userinfo["userinfo"]["tq_strid"];// "sdzhcs2";
            $call_config_tmp["time"] = time();
            $tqCallApi = new TQCallApi();
            $access_token_data = $tqCallApi->get_access_token($call_config_tmp["admin_uin"],$call_config_tmp["uin"],$call_config_tmp["strid"],$call_config_tmp["time"]);
//            var_exp($access_token_data,'$access_token_data',1);
            if(isset($access_token_data["errcode"])&&$access_token_data["errcode"]==0){
                $call_config = $call_config_tmp;
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
        $aryResult = $this->tq_webservice_call($func_name,$params);
        $result["status"] = 1;
        $result["info"] = "执行成功!";
        $result["data"] = $aryResult;
        return json($result);
    }

    protected function tq_webservice_call($func_name,$params){
        $call_config = get_cache_by_tel($this->telephone,"call_config");
        if(!$call_config) {
            $tq_config = config('tq');
            $call_config["admin_uin"] = $tq_config["admin_uin"];
            $call_config["admin_password"] = $tq_config["admin_password"];
//            set_cache_by_tel($this->telephone,"call_config",$call_config,$tq_config["expire"]?:null);
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
        return $aryResult;
    }

    public function tq_call_record_save(){
        $result = ['status'=>0 ,'info'=>"保存TQ通话记录时发生错误！"];
        $tq_hangup_id = input("tq_hangup_id","","string");
        if(empty($tq_hangup_id)){
            $result["info"] = "参数错误!";
            return json($result);
        }
        $callRecord = new CallRecord($this->corp_id);
        $callRecordList = $callRecord->getCallRecordByPhoneRecId($tq_hangup_id);
        if(!empty($callRecordList)){
            $result["info"] = "TQ通话记录已存在!";
            return json($result);
        }

        $func_name = "getPhoneRecordById";
        $params = ["[adminuin]","[adminpassword]",$tq_hangup_id];
        //var_exp($func_name,'$func_name');
        //var_exp($params,'$params');
        $aryResult = $this->tq_webservice_call($func_name,$params);
        if(is_numeric($aryResult)){
            $result["info"] = "获取TQ通话记录发生错误,错误码:".$aryResult;
            return json($result);
        }

        $aryResult = str_replace('encoding="gb2312"', 'encoding="utf-8"', $aryResult);//preg_replace('encoding="gb2312"', 'encoding="utf-8"', $aryResult);
        //var_exp($aryResult,'$aryResult');
        $xml_obj = simplexml_load_string($aryResult,null,LIBXML_NOCDATA);
        //var_exp($xml_obj,'$xml_obj');
        $json_str = json_encode($xml_obj,true);
        //var_exp($json_str,'$json_str');
        $json_obj = json_decode($json_str,true);
        //var_exp($json_obj,'$json_obj');
        $size = $json_obj["Size"];
        if(!$size>0){
            $result["info"] = "获取TQ通话记录发生错误,未能获取大小!";
            return json($result);
        }
        //var_exp($size,'$size');

        $call_record_list = [];
        for ($i=1;$i<=$size;$i++){
            $tag = "ID".$i;
            //var_exp($tag,'$tag');
            $item = $json_obj[$tag];
            var_exp($item,'$item');
            $mapped = [
                "start_time"=>"begin_time"
            ];
            $default = [
                "call_type"=>0,
                "client_uin"=>0,
                "client_id"=>0,
            ];
            $item_temp["userid"] = $this->uid;
            $item_temp["customer_id"] = 0;
            $item_temp["contactor_id"] = 0;
            $item_temp["is_customer"] = 0;
            foreach ($item as $item_field=>$item_value){
                $field = strtolower($item_field);
                $field_name = $field;
                if(isset($mapped[$field])){
                    $field_name = $mapped[$field];
                }
                if(is_array($item_value)&&empty($item_value)){
                    if(isset($default[$field_name])){
                        $item_value = $default[$field_name];
                    }else{
                        $item_value = '';
                    }
                }
                $item_temp[$field_name] = $item_value;
            }
            $item_temp["main_phone"] = $item_temp["caller_id"];
            var_exp($item_temp,'$item_temp');
            $call_record_list[] = $item_temp;
        }

        $add_flg = $callRecord->addCallRecordList($call_record_list);
        //            var_exp($add_flg,'$add_flg');
        if(!$add_flg) {
            $result["info"] = "保存TQ通话记录到数据库时发生错误!";
            return json($result);
        }

        $result["status"] = 1;
        $result["info"] = "保存TQ通话记录成功!";
        return json($result);
    }

    public function tq_call_record_mark_customer(){
        $result = ['status'=>0 ,'info'=>"绑定客户到TQ通话记录时发生错误！"];
        $tq_hangup_id = input("tq_hangup_id","","string");
        $customer_id = input("customer_id",0,"int");
        $contactor_id = input("contactor_id",0,"int");
        if(empty($tq_hangup_id)||!$customer_id){
            $result["info"] = "参数错误!";
            return json($result);
        }

//        $result["status"] = 1;
//        $result["info"] = "执行成功!";
        return json($result);
    }
}
