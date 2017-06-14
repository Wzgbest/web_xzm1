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
use think\Exception;

class Customer extends Initialize{
    protected $_customerSettingModel = null;
    public function __construct(){
        parent::__construct();
        $corp_id = get_corpid();
        $this->_customerSettingModel = new CustomerSetting($corp_id);
    }
    public function index(){
        $uri = "systemsetting/Customer/index";
        return view('index',["uri"=>$uri]);
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
        $customerSetting['protect_customer_day'] = input('protect_customer_day',0,'int');
        $customerSetting['take_times_employer'] = input('take_times_employer',0,'int');
        $customerSetting['take_times_structure'] = input('take_times_structure',0,'int');
        $customerSetting['to_halt_day'] = input('to_halt_day',0,'int');
        $customerSetting['effective_call'] = input('effective_call',0,'int');
        $customerSetting['protect_customer_num'] = input('protect_customer_num',0,'int');
        $customerSetting['public_sea_seen'] = input('public_sea_seen',0,'int');
        $set_to_structure = input('set_to_structure',"",'string');
        $set_to_structure_arr = explode(',',$set_to_structure);
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