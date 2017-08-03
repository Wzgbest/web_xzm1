<?php
namespace app\verification\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\ContractSetting as ContractModel;
use app\crm\model\Contract as ContractAppliedModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\common\model\Employee as EmployeeModel;
use app\common\model\Structure as StructureModel;

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
            $struM = new StructureModel($this->corp_id);
            $structs = $struM->getAllStructure();
            $this->assign("structs",$structs);
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
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
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
}