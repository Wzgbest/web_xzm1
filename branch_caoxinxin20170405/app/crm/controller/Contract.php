<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\ContractSetting as ContractModel;
use app\common\model\RoleEmployee as RoleEmployeeModel;
use app\crm\model\Contract as ContractAppliedModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\common\model\Employee as EmployeeModel;

class Contract extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter([]);
        $field = $this->_getCustomerField([]);
        $filter["employee_id"] = $uid;
        try{
            $contractAppliedModel = new ContractAppliedModel($this->corp_id);
            $contractApplieds = $contractAppliedModel->getContractApplied($num,$p,$filter,$field,$order,$direction);
            //var_exp($contractApplieds,'$contractApplieds',1);
            $employee_ids = [];
            foreach ($contractApplieds as &$contractApplied){
                $contract_apply_status = $contractApplied["contract_apply_status"];
                if(
                    isset($contractApplied["contract_apply_".$contract_apply_status]) &&
                    !empty($contractApplied["contract_apply_".$contract_apply_status])
                ){
                    $employee_ids[] = $contractApplied["contract_apply_".$contract_apply_status];
                    $contractApplied["assessor"] = $contractApplied["contract_apply_".$contract_apply_status];
                }
            }
            $employeeM = new EmployeeModel($this->corp_id);
            $employee_name_index = $employeeM->getEmployeeNameByUserids($employee_ids);
            foreach ($contractApplieds as &$contractApplied){
                if(
                    isset($contractApplied["assessor"])&&
                    isset($employee_name_index[$contractApplied["assessor"]])
                ) {
                    $contractApplied["assessor_name"] = $employee_name_index[$contractApplied["assessor"]];
                }else{
                    $contractApplied["assessor_name"] = '';
                }
            }
            $this->assign('list_data',$contractApplieds);
            $customers_count = $contractAppliedModel->getContractAppliedCount($filter);
            $this->assign("count",$customers_count);
            $contractSettingModel = new ContractModel($this->corp_id);
            $contracts = $contractSettingModel->getAllContract();
            //var_exp($contracts,'$contracts',1);
            $this->assign('contract_type_list',$contracts);
            $businessFlowModel = new BusinessFlowModel($this->corp_id);
            $business_flows = $businessFlowModel->getAllBusinessFlow();
            //var_exp($business_flows,'$business_flows',1);
            $this->assign('business_flow_list',$business_flows);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($customers_count/$num);
        $in_column = isset($filter["in_column"])?$filter["in_column"]:0;
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("in_column",$in_column);
        $this->assign("start_num",$customers_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$customers_count?$end_num:$customers_count);
        return view();
    }
    protected function _getCustomerFilter($filter_column){
        $filter = [];
        return $filter;
    }
    protected function _getCustomerField($field_column){
        $field = [];
        return $field;
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
        $contractSettingModel = new ContractModel($this->corp_id);
        $contracts = $contractSettingModel->getAllContract();
        $contract_index = [];
        foreach($contracts as $contract){
            $contract_index[$contract["id"]] = $contract;
        }
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
            $contract_setting = $contract_index[$apply["type"]];
            if(empty($contract_setting)){
                $result['info'] = "参数错误！";
                return json($result);
            }
            $contract_num = $apply["num"];
            if(!empty($contract_setting["max_apply"])&&$contract_num>$contract_setting["max_apply"]){
                $result['info'] = $contract_setting["contract_name"]."类合同数量超过最大申请数！";
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
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        $update_flg = $contractAppliedM->retract($id,$uid);
        if(!$update_flg){
            $result['info'] = "撤回合同申请失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='撤回合同申请成功!';
        return $result;
    }
}