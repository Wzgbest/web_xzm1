<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\ContractSetting as ContractModel;
use app\common\model\RoleEmployee as RoleEmployeeModel;
use app\crm\model\Contract as ContractAppliedModel;

class Contract extends Initialize{
    public function __construct(){
        parent::__construct();
    }
    public function index(){
        $contractSettingModel = new ContractModel($this->corp_id);
        $contracts = $contractSettingModel->getAllContract();
        //var_exp($contracts,'$contracts',1);
        $this->assign('contract_type_list',$contracts);
        return view();
    }
    public function contract_apply(){
        $contractSettingModel = new ContractModel($this->corp_id);
        $contracts = $contractSettingModel->getAllContract();
        //var_exp($contracts,'$contracts',1);
        $this->assign('contract_type_list',$contracts);
        $contract_json_arr = [];
        foreach($contracts as $contract){
            $contract_json["apply_1"] = $contract["apply_1"];
            $contract_json["apply_2"] = $contract["apply_2"];
            $contract_json["apply_3"] = $contract["apply_3"];
            $contract_json["apply_4"] = $contract["apply_4"];
            $contract_json["apply_5"] = $contract["apply_5"];
            $contract_json["apply_6"] = $contract["apply_6"];
            $contract_json_arr[$contract["id"]] = $contract_json;
        }
        $this->assign('contract_type_list_json',json_encode($contract_json_arr,true));
        $role_ids = [];
        $role_ids = array_merge($role_ids,array_column($contracts,"apply_1"));
        $role_ids = array_merge($role_ids,array_column($contracts,"apply_2"));
        $role_ids = array_merge($role_ids,array_column($contracts,"apply_3"));
        $role_ids = array_merge($role_ids,array_column($contracts,"apply_4"));
        $role_ids = array_merge($role_ids,array_column($contracts,"apply_5"));
        $role_ids = array_merge($role_ids,array_column($contracts,"apply_6"));
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
        $this->assign('role_employee_index',json_encode($role_employee_index,true));
        return view();
    }
    public function get_log(){
        return view();
    }
    public function apply(){
        $result = ['status'=>0 ,'info'=>"申请合同时发生错误！"];
        $contract_apply_str = input('contract_apply');
        $contract_apply = json_decode($contract_apply_str,true);
        if(empty($contract_apply_str)||empty($contract_apply)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        //var_exp($contract_apply,'$contract_apply',1);
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $contract_applied = [];
        $contract_applied_item["employee_id"] = $uid;
        $contract_applied_item["update_time"] = time();
        $contract_applied_item["create_time"] = $contract_applied_item["update_time"];
        $contract_applied_item["status"] = 0;
        $contract_applied_item["contract_apply_status"] = 1;
        foreach ($contract_apply as $apply){
            if(empty($apply["type"])){
                $result['info'] = "合同类型不能为空！";
                return json($result);
            }
            $contract_applied_item["contract_type"] = $apply["type"];
            $contract_applied_item["contract_num"] = $apply["num"];
            if(empty($apply["num"])){
                $result['info'] = "合同数量不能为空！";
                return json($result);
            }
            $contract_applied_item["contract_apply_1"] = $apply["apply_1"];
            if(empty($apply["num"])){
                $result['info'] = "合同一审人不能为空！";
                return json($result);
            }
            if(isset($apply["apply_2"])){
                $contract_applied_item["contract_apply_2"] = $apply["apply_2"];
            }else{
                $contract_applied_item["contract_apply_2"] = 0;
            }
            if(isset($apply["apply_3"])){
                $contract_applied_item["contract_apply_3"] = $apply["apply_3"];
            }else{
                $contract_applied_item["contract_apply_3"] = 0;
            }
            if(isset($apply["apply_4"])){
                $contract_applied_item["contract_apply_4"] = $apply["apply_4"];
            }else{
                $contract_applied_item["contract_apply_4"] = 0;
            }
            if(isset($apply["apply_5"])){
                $contract_applied_item["contract_apply_5"] = $apply["apply_5"];
            }else{
                $contract_applied_item["contract_apply_5"] = 0;
            }
            if(isset($apply["apply_6"])){
                $contract_applied_item["contract_apply_6"] = $apply["apply_6"];
            }else{
                $contract_applied_item["contract_apply_6"] = 0;
            }
            $contract_applied[] = $contract_applied_item;
        }
        //var_exp($contract_applied,'$contract_applied',1);
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        $add_flg = $contractAppliedM->addAllContract($contract_applied);
        if(!$add_flg){
            $result['info']='申请合同失败!';
            return json($result);
        }
        $result['status']=1;
        $result['info']='申请合同成功!';
        return $result;
    }
    public function retract(){
        $result = ['status'=>0 ,'info'=>"撤回合同申请时发生错误！"];
        $result['status']=1;
        $result['info']='撤回合同申请开发中!';
        return $result;
    }
    public function approved(){
        $result = ['status'=>0 ,'info'=>"通过合同申请时发生错误！"];
        $result['status']=1;
        $result['info']='通过合同申请开发中!';
        return $result;
    }
    public function rejected(){
        $result = ['status'=>0 ,'info'=>"驳回合同申请时发生错误！"];
        $result['status']=1;
        $result['info']='驳回合同申请开发中!';
        return $result;
    }
    public function invalid(){
        $result = ['status'=>0 ,'info'=>"作废合同时发生错误！"];
        $result['status']=1;
        $result['info']='作废合同开发中!';
        return $result;
    }
    public function received(){
        $result = ['status'=>0 ,'info'=>"已领取合同时发生错误！"];
        $result['status']=1;
        $result['info']='已领取合同开发中!';
        return $result;
    }
    public function remind(){
        $result = ['status'=>0 ,'info'=>"提醒领取合同时发生错误！"];
        $result['status']=1;
        $result['info']='提醒领取合同开发中!';
        return $result;
    }
    public function refunded(){
        $result = ['status'=>0 ,'info'=>"已退款时发生错误！"];
        $result['status']=1;
        $result['info']='已退款开发中!';
        return $result;
    }
    public function withdrawal(){
        $result = ['status'=>0 ,'info'=>"收回合同时发生错误！"];
        $result['status']=1;
        $result['info']='收回合同开发中!';
        return $result;
    }
}