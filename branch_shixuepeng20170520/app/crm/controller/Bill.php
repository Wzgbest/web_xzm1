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

    public function bill_apply(){
        return view();
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
            $contractSettingInfo["handle_1"] = $contractSetting["handle_1"];
            $contractSettingInfo["handle_2"] = $contractSetting["handle_2"];
            $contractSettingInfo["handle_3"] = $contractSetting["handle_3"];
            $contractSettingInfo["handle_4"] = $contractSetting["handle_4"];
            $contractSettingInfo["handle_5"] = $contractSetting["handle_5"];
            $contractSettingInfo["handle_6"] = $contractSetting["handle_6"];
            $contractSettingInfo["product_type"] = explode(",",$contractSetting["product_type"]);
            $contractSettingInfo["bank_type"] = explode(",",$contractSetting["bank_type"]);
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
            $contractSettingInfo["role_employee_index"] = $role_employee_index;

            $contractSettingModel = new ContractSettingModel($this->corp_id);
            $contracts = $contractSettingModel->getAllContractName();
            //var_exp($contracts,'$contracts',1);
            $contractSettingInfo["contract_list"] = $contracts;

            $status = [5,7,8];
            $contractAppliedModel = new ContractAppliedModel($this->corp_id);
            $contracts = $contractAppliedModel->getAllContractNoAndType($uid,$status);
            //var_exp($contractApplieds,'$contractApplieds',1);
            $contract_type_index = [];
            foreach ($contracts as $contract){
                $tmp["id"] = $contract["id"];
                $tmp["contract_no"] = $contract["contract_no"];
                $contract_type_index[$contract["contract_type"]][] = $tmp;
            }
            $contractSettingInfo["contract_type_index"] = $contract_type_index;

            $result['data'] = $contractSettingInfo;
        }catch (\Exception $ex){
            $result["info"] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取成功！";
        return json($result);
    }

    public function apply(){
        $bill_apply = input("bill_apply");
        var_exp($bill_apply,'$bill_apply',1);
    }
    public function retract(){
        $result = ['status'=>0 ,'info'=>"撤回发票申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $update_flg = false;
        if(!$update_flg){
            $result['info'] = "撤回发票申请失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='撤回发票申请成功!';
        return $result;
    }
}