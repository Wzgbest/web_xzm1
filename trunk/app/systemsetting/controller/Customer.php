<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\CustomerSetting;
use app\common\model\Structure;
use think\Exception;

class Customer extends Initialize{
    protected $_customerSettingModel = null;
    public function __construct(){
        parent::__construct();
        $this->_customerSettingModel = new CustomerSetting($this->corp_id);
    }
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        $structure = input("structure",0,'int');
        try{
            $map = null;
            if($structure){
                $map = "find_in_set('$structure', set_to_structure)";
            }
            $customerSettings = $this->_customerSettingModel->getAllCustomerSetting($map);
            $this->assign("listdata",$customerSettings);
            $structure_ids = [];
            $structure_ids_arr = array_column($customerSettings, 'set_to_structure');
            foreach ($structure_ids_arr as $id_str){
                $id_arr = explode(",",$id_str);
                if($id_arr){
                    $structure_ids = array_merge($structure_ids,$id_arr);
                }
            }
            $structure_ids = array_filter($structure_ids);
            $structure_ids = array_unique($structure_ids);
            $structure = new Structure($this->corp_id);
            $structureName = $structure->getStructureName($structure_ids);
            $this->assign("structure_name",$structureName);
            $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        return view();
    }

    public function add_page(){
        $customerSetting = [
            "id"=>"0",
            "setting_name"=>"",
            "protect_customer_day"=>"",
            "take_times_employee"=>"",
            "take_times_structure"=>"",
            "to_halt_day"=>"",
            "effective_call"=>"",
            "protect_customer_num"=>"",
            "public_sea_seen"=>"",
            "set_to_structure"=>"",
            "set_to_structure_arr"=>[],
        ];
        $this->assign("customerSetting",$customerSetting);
        $this->assign("structure_names_str","");
        $structure = new Structure($this->corp_id);
        $structures = $structure->getAllStructure();
        $this->assign("structures",$structures);
        $set_to_structure_args = '';
        $this->assign("set_to_structure_args",$set_to_structure_args);
        $this->assign("url",url("add"));
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view("edit_page");
    }

    public function edit_page(){
        $id = input("id");
        if(!$id){
            $this->error("参数错误!");
        }
        $map["id"] = $id;
        try{
            $customerSetting = $this->_customerSettingModel->getCustomerSetting(1,0,$map,"");
            //var_exp($customerSetting,'$customerSetting');
            $this->assign("customerSetting",$customerSetting);
            $set_to_structure_args = "set_to_structure[]=".implode("&set_to_structure[]=",$customerSetting["set_to_structure_arr"]);
            $this->assign("set_to_structure_args",$set_to_structure_args);
            $structure = new Structure($this->corp_id);
            $structures = $structure->getAllStructure();
            $this->assign("structures",$structures);
            $structure_names = $structure->getStructureName($customerSetting["set_to_structure_arr"]);
            $structure_names_str = implode(",",$structure_names);
            $this->assign("structure_names_str",$structure_names_str);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $this->assign("url",url("update"));
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view("edit_page");
    }

    public function table(){
        $result = ['status'=>0 ,'info'=>"查询客户设置发生错误！"];
        $num = 10;
        $structure = input("structure",0,'int');
        $p = input("p");
        $p = $p?:1;
        try{
            if($structure){
                $map = "find_in_set('$structure', set_to_structure)";
                $customerSettings = $this->_customerSettingModel->getCustomerSetting($num,$p,$map);
            }else{
                $customerSettings = $this->_customerSettingModel->getCustomerSetting($num,$p);
            }
            $result['data'] = $customerSettings;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }

    public function get(){
        $result = ['status'=>0 ,'info'=>"获取客户设置发生错误！"];
        $id = input("id");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $map["id"] = $id;
        try{
            $customerSetting = $this->_customerSettingModel->getCustomerSetting(1,0,$map,"");
            $result['data'] = $customerSetting;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }

    protected function _getCustomerSettingForInput(){
        $customerSetting['setting_name'] = input('setting_name');
        $customerSetting['protect_customer_day'] = input('protect_customer_day',0,'int');
        $customerSetting['take_times_employee'] = input('take_times_employee',0,'int');
        $customerSetting['take_times_structure'] = input('take_times_structure',0,'int');
        $customerSetting['to_halt_day'] = input('to_halt_day',0,'int');
        $customerSetting['effective_call'] = input('effective_call',0,'int');
        $customerSetting['protect_customer_num'] = input('protect_customer_num',0,'int');
        $customerSetting['public_sea_seen'] = input('public_sea_seen',0,'int');
//        $set_to_structure = input('set_to_structure',"",'string');
//        $set_to_structure_arr = explode(',',$set_to_structure);
        $set_to_structure_arr = input('set_to_structure/a');
        $set_to_structure_arr = array_map("intval",$set_to_structure_arr);
        $set_to_structure_arr = array_filter($set_to_structure_arr);
        $set_to_structure_arr = array_unique($set_to_structure_arr);
        $zero_flg = true;
        do{
            $zero_flg = array_search(0,$set_to_structure_arr);
            if($zero_flg){
                unset($set_to_structure_arr[$zero_flg]);
            }
        }while($zero_flg);
        $customerSetting['set_to_structure'] = implode(",",$set_to_structure_arr);
        return $customerSetting;
    }

    public function add(){
        $result = ['status'=>0 ,'info'=>"添加客户设置时发生错误！"];
        $customerSetting = $this->_getCustomerSettingForInput();
        try{
            $validate_result = $this->validate($customerSetting,'CustomerSetting');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $customerSettingAddFlg = $this->_customerSettingModel->addCustomerSetting($customerSetting);
            $result['data'] = $customerSettingAddFlg;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "添加成功！";
        return json($result);
    }

    public function update(){
        $result = ['status'=>0 ,'info'=>"更新客户设置时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $customerSetting = $this->_getCustomerSettingForInput();
        try{
            $validate_result = $this->validate($customerSetting,'CustomerSetting');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $customerSettingUpdateFlg = $this->_customerSettingModel->setCustomerSetting($id,$customerSetting);
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "更新成功！";
        return json($result);
    }

    public function del(){
        $result = ['status'=>0 ,'info'=>"删除客户设置时发生错误！"];
        $ids = input("ids");
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $ids_arr = explode(",",$ids);
        $map["id"] = array("in",$ids_arr);
        try{
            $customerSettingDelFlg = $this->_customerSettingModel->delCustomerSetting($map);
            if(!$customerSettingDelFlg){
                return json($result);
            }
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "删除成功！";
        return json($result);
    }
}