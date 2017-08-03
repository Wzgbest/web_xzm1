<?php
/**
 * Created by messhair.
 * Date: 2017/5/25
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\ContractSetting as ContractModel;
use think\Request;
use app\common\model\Role as RoleModel;

class Contract extends Initialize{
    protected $_contractSettingModel = null;
    protected $apply_max = 6;
    public function __construct(){
        parent::__construct();
        $corp_id = get_corpid();
        $this->_contractSettingModel = new ContractModel($corp_id);
    }
    public function index(){
        $contracts = $this->_contractSettingModel->getAllContract();
        //var_exp($contracts,'$contracts',1);
        $this->assign('listdata',$contracts);
        return view();
    }
    public function add_page(){
        $contractSetting = [
            "id"=>"0",
            "contract_name"=>"",
            "contract_prefix"=>"",
            "start_num"=>"",
            "end_num"=>"",
            "current_contract"=>"",
            "max_apply"=>"",
            "apply_1"=>"",
            "apply_2"=>"",
            "apply_3"=>"",
            "apply_4"=>"",
            "apply_5"=>"",
            "apply_6"=>"",
            "create_contract_num_1"=>"",
            "create_contract_num_2"=>"",
            "create_contract_num_3"=>"",
            "create_contract_num_4"=>"",
            "create_contract_num_5"=>"",
            "create_contract_num_6"=>"",
        ];
        $this->assign("contractSetting",$contractSetting);
        $roleM = new RoleModel();
        $roles = $roleM->getAllRole();
        $this->assign('roles',$roles);
        $this->assign('roles_json',json_encode($roles));
        $this->assign('applys',json_encode([]));
        $this->assign('apply_max',$this->apply_max);
        $this->assign("url",url("add"));
        return view("edit_page");
    }

    public function edit_page(){
        $id = input("id");
        if(!$id){
            $this->error("参数错误!");
        }
        $map["id"] = $id;
        $contractSetting = [];
        try{
            $contractSetting = $this->_contractSettingModel->getContractSetting(1,0,$map,"");
            $this->assign("contractSetting",$contractSetting);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $applys = [];
        $applys[0] = [
            "apply"=>$contractSetting["apply_1"],
            "create_contract_num"=>$contractSetting["create_contract_num_1"]
        ];
        if($contractSetting["apply_2"]>0){
            $applys[1] = [
                "apply"=>$contractSetting["apply_2"],
                "create_contract_num"=>$contractSetting["create_contract_num_2"]
            ];
        }
        if($contractSetting["apply_3"]>0){
            $applys[2] = [
                "apply"=>$contractSetting["apply_3"],
                "create_contract_num"=>$contractSetting["create_contract_num_3"]
            ];
        }
        if($contractSetting["apply_4"]>0){
            $applys[3] = [
                "apply"=>$contractSetting["apply_4"],
                "create_contract_num"=>$contractSetting["create_contract_num_4"]
            ];
        }
        if($contractSetting["apply_5"]>0){
            $applys[4] = [
                "apply"=>$contractSetting["apply_5"],
                "create_contract_num"=>$contractSetting["create_contract_num_5"]
            ];
        }
        if($contractSetting["apply_6"]>0){
            $applys[5] = [
                "apply"=>$contractSetting["apply_6"],
                "create_contract_num"=>$contractSetting["create_contract_num_6"]
            ];
        }
        $roleM = new RoleModel();
        $roles = $roleM->getAllRole();
        $this->assign('roles',$roles);
        $this->assign('roles_json',json_encode($roles));
        $this->assign('applys',json_encode($applys));
        $this->assign('apply_max',$this->apply_max);
        $this->assign("url",url("update",["id"=>$id]));
        return view("edit_page");
    }

    protected function _getContractSettingForInput(){
        $contractSetting['contract_name'] = input('contract_name');
        $contractSetting['contract_prefix'] = input('contract_prefix');
        $contractSetting['start_num'] = input('start_num',0,'int');
        $contractSetting['end_num'] = input('end_num',0,'int');
        $contractSetting['current_contract'] = input('current_contract',0,'int');
        $contractSetting['max_apply'] = input('max_apply',0,'int');
        $contractSetting['apply_1'] = input('apply_1',0,'int');
        $contractSetting['apply_2'] = input('apply_2',0,'int');
        $contractSetting['apply_3'] = input('apply_3',0,'int');
        $contractSetting['apply_4'] = input('apply_4',0,'int');
        $contractSetting['apply_5'] = input('apply_5',0,'int');
        $contractSetting['apply_6'] = input('apply_6',0,'int');
        $contractSetting['create_contract_num_1'] = input('create_contract_num_1',0,'int');
        $contractSetting['create_contract_num_2'] = input('create_contract_num_2',0,'int');
        $contractSetting['create_contract_num_3'] = input('create_contract_num_3',0,'int');
        $contractSetting['create_contract_num_4'] = input('create_contract_num_4',0,'int');
        $contractSetting['create_contract_num_5'] = input('create_contract_num_5',0,'int');
        $contractSetting['create_contract_num_6'] = input('create_contract_num_6',0,'int');
        return $contractSetting;
    }

    public function add(){
        $result = ['status'=>0 ,'info'=>"添加合同设置时发生错误！"];
        $contractSetting = $this->_getContractSettingForInput();
        try{
            $validate_result = $this->validate($contractSetting,'ContractSetting');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $contractSettingAddFlg = $this->_contractSettingModel->addContractSetting($contractSetting);
            $result['data'] = $contractSettingAddFlg;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "添加成功！";
        return json($result);
    }

    public function get(){
        $result = ['status'=>0 ,'info'=>"获取合同设置时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $contractSetting = $this->_contractSettingModel->getContractSettingById($id);
            if(empty($contractSetting)){
                exception("未找到合同设置!");
            }
            $result['data'] = $contractSetting;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取成功！";
        return json($result);
    }

    public function update(){
        $result = ['status'=>0 ,'info'=>"更新合同设置时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $contractSetting = $this->_getContractSettingForInput();
        try{
            $validate_result = $this->validate($contractSetting,'ContractSetting');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $contractSettingUpdateFlg = $this->_contractSettingModel->setContractSetting($id,$contractSetting);
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "更新成功！";
        return json($result);
    }

    public function del(){
        $result = ['status'=>0 ,'info'=>"删除合同设置时发生错误！"];
        $ids = input("ids");
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $ids_arr = explode(",",$ids);
        $map["id"] = array("in",$ids_arr);
        try{
            $contractSettingDelFlg = $this->_contractSettingModel->delContractSetting($map);
            if(!$contractSettingDelFlg){
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