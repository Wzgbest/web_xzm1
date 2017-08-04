<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\BillSetting as BillSettingModel;
use app\common\model\RoleEmployee as RoleEmployeeModel;
use app\systemsetting\model\ContractSetting as ContractSettingModel;
use app\crm\model\Contract as ContractAppliedModel;
use app\crm\model\Bill as BillModel;
use app\crm\model\SaleChance as SaleChanceModel;
use app\crm\model\Customer as CustomerModel;
use app\crm\model\SaleOrderContract as SaleOrderContractModel;

class Bill extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }

    public function index(){
        return "/crm/bill/index";
    }

    public function bill_apply(){
        $sale_id = input("sale_id",0,"int");
        if(!$sale_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $this->assign('sale_id',$sale_id);
        $billSettingModel = new BillSettingModel($this->corp_id);
        $bills = $billSettingModel->getBillNameIndex();
        //var_exp($bills,'$bills',1);
        $this->assign('bill_name',$bills);
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
        $result = ['status'=>0 ,'info'=>"提交发票申请时发生错误！"];
        $bill_apply = input("bill_apply");
        //var_exp($bill_apply,'$bill_apply',1);
        $bill_apply_arr = json_decode($bill_apply,true);
        //var_exp($businessFlowSetting,'$businessFlowSetting');
        //var_exp($link_arr,'$link_arr');
        if(empty($bill_apply_arr)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $sale_id = $bill_apply_arr["sale_id"];
        //$sale_id += 1;//TODO 测试
        $bill_type = $bill_apply_arr["bill_type"];
        $contract_number = $bill_apply_arr["contract_number"];
        $tax_num = $bill_apply_arr["tax_num"];
        $product_type_arr = $bill_apply_arr["product_type"];
        $pay_way_arr = $bill_apply_arr["pay_way"];
        $handle_arr = $bill_apply_arr["handle"];
        if(
            empty($sale_id)||
            empty($bill_type)||
            empty($contract_number)||
            empty($tax_num)||
            empty($product_type_arr)||
            empty($pay_way_arr)||
            empty($handle_arr)
        ){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $sale_chance = $saleChanceM->getSaleChance($sale_id);
        if(empty($sale_chance)){
            $result['info'] = "未找到发票对应的销售机会！";
            return json($result);
        }
        $customerM = new CustomerModel($this->corp_id);
        $customers_data = $customerM->getCustomer($sale_chance["customer_id"]);
        if(empty($customers_data)){
            $result['info'] = "未找到发票对应的客户！";
            return json($result);
        }
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContract = $saleOrderContractM->getSaleOrderContractBySaleId($sale_id);
        if(empty($saleOrderContract)){
            $result['info'] = "未找到发票对应的成单申请！";
            return json($result);
        }
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContract = $saleOrderContractM->getSaleOrderContractBySaleId($sale_id);
        if(empty($saleOrderContract)){
            $result['info'] = "未找到发票对应的成单申请！";
            return json($result);
        }
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        $contract = $contractAppliedM->getContract($contract_number);
        if(empty($contract)){
            $result['info'] = "未找到所选合同！";
            return json($result);
        }
        $bill_money = 0;
        foreach ($product_type_arr as $product_type){
            $bill_money += $product_type["product_type_money"];
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time=time();

        $data["customer_id"] = $customers_data["id"];
        $data["sale_id"] = $sale_id;
        $data["operator"] = $uid;
        $data["bill_type"] = $bill_type;
        $data["order_id"] = $saleOrderContract["id"];
        $data["contract_id"] = $contract["id"];
        $data["contract_no"] = $contract["contract_no"];
        $data["customer_name"] = $customers_data["customer_name"];
        $data["tax_num"] = $tax_num;
        $data["bill_money"] = $bill_money;
        $data["pay_type"] = $pay_way_arr["way"]?$pay_way_arr["bank_type"]:"现金";
        $data["handle_1"] = $handle_arr["handle_1"];
        $data["handle_2"] = isset($handle_arr["handle_2"])?$handle_arr["handle_2"]:0;
        $data["handle_3"] = isset($handle_arr["handle_3"])?$handle_arr["handle_3"]:0;
        $data["handle_4"] = isset($handle_arr["handle_4"])?$handle_arr["handle_4"]:0;
        $data["handle_5"] = isset($handle_arr["handle_5"])?$handle_arr["handle_5"]:0;
        $data["handle_6"] = isset($handle_arr["handle_6"])?$handle_arr["handle_6"]:0;

        $billM = new BillModel($this->corp_id);
        try{
            $billM->link->startTrans();
            $bill_add_flg = $billM->addBill($data);
            if(!$bill_add_flg){
                exception("提交发票申请失败!");
            }
            foreach ($product_type_arr as &$product_type){
                $product_type["bill_id"] = $bill_add_flg;
            }
            $bill_item_add_flg = $billM->addAllBillItem($product_type_arr);
            if(!$bill_item_add_flg){
                exception("提交发票申请信息失败!");
            }
            $billM->link->commit();
        }catch (\Exception $ex){
            $billM->link->rollback();
            //$result['info'] = $ex->getMessage();
            $result['info'] = "提交发票申请失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='提交发票申请成功!';
        return $result;
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