<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\ContractSetting as ContractModel;
use app\common\model\RoleEmployee as RoleEmployeeModel;

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
        $result['status']=1;
        $result['info']='申请合同开发中!';
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