<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\BillSetting as BillSettingModel;
use app\common\model\RoleEmployee as RoleEmployeeModel;
use app\systemsetting\model\ContractSetting as ContractSettingModel;
use app\crm\model\Contract as ContractAppliedModel;

class Bill extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }

    public function get_bill_setting(){
        $result = ['status'=>0 ,'info'=>"获取发票设置时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        try{
            $billSettingModel = new BillSettingModel($this->corp_id);
            $contractSetting = $billSettingModel->getBillSettingById($id);
            //var_exp($contractSetting,'$contractSetting',1);
            if(empty($contractSetting)){
                exception("未找到发票设置!");
            }
            $contractSetting["bill_type"] = explode(",",$contractSetting["bill_type"]);
            $contractSetting["bank_type"] = explode(",",$contractSetting["bank_type"]);
            $role_ids = [];
            $role_ids[] = $contractSetting["handle_1"];
            $role_ids[] = $contractSetting["handle_2"];
            $role_ids[] = $contractSetting["handle_3"];
            $role_ids[] = $contractSetting["handle_4"];
            $role_ids[] = $contractSetting["handle_5"];
            $role_ids[] = $contractSetting["handle_6"];
            $role_ids = array_filter($role_ids);
            $role_ids = array_unique($role_ids);
            $role_ids = array_merge($role_ids);
            //var_exp($role_ids,'$role_ids',1);
            $role_empM = new RoleEmployeeModel($this->corp_id);
            $employeeNameList = $role_empM->getEmployeeNameListbyRole($role_ids);
            //var_exp($employeeNameList,'$employeeNameList',1);
            $role_employee_index = [];
            foreach($employeeNameList as $employeeinfo){
                $role_id = $employeeinfo["role_id"];
                unset($employeeinfo["role_id"]);
                $role_employee_index[$role_id][] = $employeeinfo;
            }
            //var_exp($role_employee_index,'$role_employee_index',1);
            $contractSetting["role_employee_index"] = $role_employee_index;

            $contractSettingModel = new ContractSettingModel($this->corp_id);
            $contracts = $contractSettingModel->getAllContractName();
            //var_exp($contracts,'$contracts',1);
            $contractSetting["contract_list"] = $contracts;

            $status = [5,7,8];
            $contractAppliedModel = new ContractAppliedModel($this->corp_id);
            $contracts = $contractAppliedModel->getAllContract($uid,$status);
            //var_exp($contractApplieds,'$contractApplieds',1);
            $contract_type_index = [];
            foreach ($contracts as $contract){
                $contract_type_index[$contract["contract_type"]][] = $contract;
            }
            $contractSetting["contract_type_index"] = $contract_type_index;

            $result['data'] = $contractSetting;
        }catch (\Exception $ex){
            $result["info"] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取成功！";
        return json($result);
    }
}