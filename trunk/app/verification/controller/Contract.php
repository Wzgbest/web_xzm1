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
        //$filter["employee_id"] = $uid; // 审核人
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
    public function approved(){
        $result = ['status'=>0 ,'info'=>"通过合同申请时发生错误！"];
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $contractApplied = $contractAppliedM->getContract($id);
        if(empty($contractApplied)){
            $result['info'] = "未找到合同申请！";
            return json($result);
        }
        $contract_apply_status = $contractApplied["contract_apply_status"];
        if(empty($contract_apply_status) || $contract_apply_status>6){
            $result['info'] = "审批流程出现问题,请联系管理员！";
            return json($result);
        }
        $contract_apply_status = $contract_apply_status+1;
        $applied_data["contract_apply_status"] = $contract_apply_status;
        if(
            $contract_apply_status!=6 &&
            !empty($contractApplied["contract_apply_".($contract_apply_status+1)])
        ){
            //还有下一步审批,转为下一个人审批
            $contractAppliedFlg = $contractAppliedM->setContract($id,$applied_data);
            if(!$contractAppliedFlg){
                $result['info'] = "审批失败！";
                return json($result);
            }
        }else{
            //最后一步审批,审批通过,生成合同,改为待领取
            $applied_data["status"] = 1;
            $contractAppliedFlg = $contractAppliedM->setContract($id,$applied_data);
            if(!$contractAppliedFlg){
                $result['info'] = "审批失败！";
                return json($result);
            }
            $contractSettingModel = new ContractModel($this->corp_id);
            $contract_setting = $contractSettingModel->getContractSettingById($contractApplied["contract_type"]);
            $contract_num = $contractApplied["contract_num"];
            $now_contract_no = $contract_setting["current_contract"];
            $end_contract_no = $now_contract_no+$contract_num-1;
            $contract_prefix = $contract_setting["contract_prefix"];
            if($end_contract_no>$contract_setting["end_num"]){
                $result['info'] = "审批失败,剩余合同号数量不足！";
                return json($result);
            }
            $contract_arr = [];
            $contract_item["applied_id"] = $id;
            $contract_item["update_time"] = $time;
            $contract_item["create_time"] = $time;
            $contract_item["status"] = 4;
            for($contract_no=$now_contract_no;$contract_no<=$end_contract_no;$contract_no++){
                $contract_item["contract_no"] = $contract_prefix.$contract_no;
                $contract_arr[] = $contract_item;
            }
            $contractCreateFlg = $contractAppliedM->createContractNos($contract_arr);
            if(!$contractCreateFlg){
                $result['info'] = "审批失败,生成合同号时出现错误！";
                return json($result);
            }
            $contractSettingModel = new ContractModel($this->corp_id);
            $contract_setting_flg = $contractSettingModel->setContractSetting(
                $contractApplied["contract_type"],
                ["current_contract"=>["exp","current_contract + ".$contract_num]]//$contract_num
            );
            if(!$contract_setting_flg){
                $result['info'] = "审批失败,更新合同当前合同号时出现错误！";
                return json($result);
            }
        }
        $result['status']=1;
        $result['info']='通过合同申请成功!';
        return $result;
    }
    public function rejected(){
        $result = ['status'=>0 ,'info'=>"驳回合同申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        $update_flg = $contractAppliedM->rejected($id,$uid);
        if(!$update_flg){
            $result['info'] = "驳回合同失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='驳回合同申请成功!';
        return $result;
    }
    public function invalid(){
        $result = ['status'=>0 ,'info'=>"作废合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='作废合同开发中!';
        return $result;
    }
    public function received(){
        $result = ['status'=>0 ,'info'=>"已领取合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='已领取合同开发中!';
        return $result;
    }
    public function remind(){
        $result = ['status'=>0 ,'info'=>"提醒领取合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='提醒领取合同开发中!';
        return $result;
    }
    public function refunded(){
        $result = ['status'=>0 ,'info'=>"已退款时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='已退款开发中!';
        return $result;
    }
    public function withdrawal(){
        $result = ['status'=>0 ,'info'=>"收回合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='收回合同开发中!';
        return $result;
    }
}