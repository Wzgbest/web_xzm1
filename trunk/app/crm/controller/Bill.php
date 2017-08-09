<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\crm\model\Bill as BillModel;
use app\common\model\Employee as EmployeeModel;
use app\systemsetting\model\BillSetting as BillSettingModel;
use app\common\model\RoleEmployee as RoleEmployeeModel;
use app\systemsetting\model\ContractSetting as ContractSettingModel;
use app\crm\model\Contract as ContractAppliedModel;
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
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["in_column"]);
        $field = $this->_getCustomerField([]);
        try{
            $billM = new BillModel($this->corp_id);
            $bill_list = $billM->getBill($uid,$num,$p,$filter,$field,$order,$direction);
            //var_exp($bill_list,'$bill_list',1);
            $employee_ids = [];
            foreach ($bill_list as &$bill){
                $handle_status = $bill["handle_status"];
                if(
                    isset($bill["handle_".$handle_status]) &&
                    !empty($bill["handle_".$handle_status])
                ){
                    $temp_employee_ids = explode(",",$bill["handle_".$handle_status]);
                    if($temp_employee_ids==null){
                        continue;
                    }
                    $employee_ids = array_merge($employee_ids,$temp_employee_ids);
                    $bill["assessor"] = $bill["handle_".$handle_status];
                }
            }
            $employeeM = new EmployeeModel($this->corp_id);
            $employee_name_index = $employeeM->getEmployeeNameByUserids($employee_ids);
            foreach ($bill_list as &$bill){
                if(
                    isset($bill["assessor"])&&
                    !empty($bill["assessor"])
                ) {
                    $temp_employee_names = [];
                    $temp_employee_ids = explode(",",$bill["assessor"]);
                    if($temp_employee_ids==null){
                        continue;
                    }
                    foreach ($temp_employee_ids as $temp_employee_id){
                        if(isset($employee_name_index[$temp_employee_id])){
                            $temp_employee_names[] = $employee_name_index[$temp_employee_id];
                        }
                    }
                    $bill["assessor_name"] = implode(",",$temp_employee_names);
                }else{
                    $bill["assessor_name"] = '';
                }
            }
            $this->assign('list_data',$bill_list);
            $customers_count = $billM->getBillCount($uid,$filter);
            $this->assign("count",$customers_count);
            $listCount = $billM->getColumnNum($uid,$filter);
            $this->assign("listCount",$listCount);
            $billSettingModel = new BillSettingModel($this->corp_id);
            $bills = $billSettingModel->getBillNameIndex();
            //var_exp($bills,'$bills',1);
            $this->assign('bill_name',$bills);
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

        //所在列
        if(in_array("in_column", $filter_column)){
            $in_column = input("in_column",1,"int");
            if($in_column){
                $filter["in_column"] = $in_column;
            }
        }
        return $filter;
    }
    protected function _getCustomerField($field_column){
        $field = [];
        return $field;
    }

    public function bill_apply_check(){
        $result = ['status'=>0 ,'info'=>"检查发票申请时发生错误！"];
        $sale_id = input("sale_id",0,"int");
        if(!$sale_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $billM = new BillModel($this->corp_id);
        $bill_info = $billM->checkBillBySaleIdNot($sale_id,[2,3,6,9]);
        if(!empty($bill_info)){
            $result['info'] = "该销售机会已申请发票,请勿重复申请！";
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "该销售机会未申请发票！";
        return json($result);
    }

    public function bill_apply(){
        $sale_id = input("sale_id",0,"int");
        if(!$sale_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $billM = new BillModel($this->corp_id);
        $bill_info = $billM->checkBillBySaleIdNot($sale_id,[2,3,6,9]);
        if(!empty($bill_info)){
            $result['status'] = 0;
            $result['info'] = "该销售机会已申请发票,请勿重复申请！";
            return json($result);
        }
        $this->assign('sale_id',$sale_id);
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $saleChanceData = $saleChanceM->getSaleChance($sale_id);
        if(empty($saleChanceData)){
            $result['status'] = 0;
            $result['info'] = "未找到该销售机会！";
            return json($result);
        }
        $this->assign('sale_chance',$saleChanceData);
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContractData = $saleOrderContractM->getSaleOrderContractBySaleId($sale_id);
        if(empty($saleOrderContractData)){
            $result['status'] = 0;
            $result['info'] = "未找到该销售机会成单申请！";
            return json($result);
        }
        $this->assign('sale_order_contract',$saleOrderContractData);
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
        $sale_id = input("sale_id",null,"int");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        try{
            $billSettingModel = new BillSettingModel($this->corp_id);
            $contractSetting = $billSettingModel->getBillSettingById($id);
            //var_exp($contractSetting,'$contractSetting',1);
            if(empty($contractSetting)){
                exception("未找到发票设置!");
            }
            $contractSettingInfo["need_tax_id"] = $contractSetting["need_tax_id"];
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

            $status = [5,7,8];
            $contractAppliedModel = new ContractAppliedModel($this->corp_id);
            $contracts = $contractAppliedModel->getAllContractNoAndType($uid,$sale_id,$status);
            //var_exp($contractApplieds,'$contractApplieds',1);
            $contract_type_index = [];
            $contract_type_ids= [];
            foreach ($contracts as $contract){
                $tmp["id"] = $contract["id"];
                $tmp["contract_no"] = $contract["contract_no"];
                $contract_type_index[$contract["contract_type"]][] = $tmp;
                $contract_type_ids[] = $contract["contract_type"];
            }
            $contractSettingInfo["contract_type_index"] = $contract_type_index;

            $contractSettingModel = new ContractSettingModel($this->corp_id);
            $contracts = $contractSettingModel->getAllContractName($contract_type_ids);
            //var_exp($contracts,'$contracts',1);
            $contractSettingInfo["contract_list"] = $contracts;

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
        if(
            empty($sale_id)
        ){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $billM = new BillModel($this->corp_id);
        $bill_info = $billM->checkBillBySaleIdNot($sale_id,[2,3,6,9]);
        if(!empty($bill_info)){
            $result['info'] = "该销售机会已申请发票,请勿重复申请！";
            return json($result);
        }
        $bill_type = $bill_apply_arr["bill_type"];
        $contract_id = $bill_apply_arr["contract_id"];
        $product_type_arr = $bill_apply_arr["product_type"];
        $tax_num = '';
        $pay_way_arr = $bill_apply_arr["pay_way"];
        $handle_arr = $bill_apply_arr["handle"];
        if(
            empty($bill_type)||
            empty($contract_id)||
            empty($product_type_arr)||
            empty($pay_way_arr)||
            empty($handle_arr)
        ){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $billSettingModel = new BillSettingModel($this->corp_id);
        $contractSetting = $billSettingModel->getBillSettingById($bill_type);
        //var_exp($contractSetting,'$contractSetting',1);
        if(empty($contractSetting)){
            exception("未找到发票设置!");
        }
        if($contractSetting["need_tax_id"]==1){
            $tax_num = $bill_apply_arr["tax_num"];
            if(empty($tax_num)){
                $result['info'] = "参数错误！";
                return json($result);
            }
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
        $contract = $contractAppliedM->getContractNoInfo($contract_id);
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
        $data["pay_type"] = $pay_way_arr["way"]!="现金"?$pay_way_arr["bank_type"]:"现金";
        $data["handle_1"] = $handle_arr["handle_1"];
        $data["handle_2"] = isset($handle_arr["handle_2"])?$handle_arr["handle_2"]:0;
        $data["handle_3"] = isset($handle_arr["handle_3"])?$handle_arr["handle_3"]:0;
        $data["handle_4"] = isset($handle_arr["handle_4"])?$handle_arr["handle_4"]:0;
        $data["handle_5"] = isset($handle_arr["handle_5"])?$handle_arr["handle_5"]:0;
        $data["handle_6"] = isset($handle_arr["handle_6"])?$handle_arr["handle_6"]:0;
        $data["handle_status"] = 1;
        $data["handle_now"] = $data["handle_1"];
        $data["create_time"] = $time;
        $data["update_time"] = $time;
        $data["status"] = 0;
        //var_exp($data,'$data',1);

        try{
            $billM->link->startTrans();
            $bill_add_flg = $billM->addBill($data);
            if(!$bill_add_flg){
                exception("提交发票申请失败!");
            }
            foreach ($product_type_arr as &$product_type){
                $product_type["bill_id"] = $bill_add_flg;
            }
            //var_exp($product_type_arr,'$product_type_arr',1);
            $bill_item_add_flg = $billM->addAllBillItem($product_type_arr);
            if(!$bill_item_add_flg){
                exception("提交发票申请信息失败!");
            }
            $billM->link->commit();
        }catch (\Exception $ex){
            $billM->link->rollback();
            $result['info'] = $ex->getMessage();
            //$result['info'] = "提交发票申请失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='提交发票申请成功!';
        return $result;
    }
    public function retract(){
        $result = ['status'=>0 ,'info'=>"撤回发票申请时发生错误！"];
        $id = input("id",0,"int");
        $sale_id = input("sale_id",0,"int");
        if(!$id&&!$sale_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $update_flg = false;
        $billM = new BillModel($this->corp_id);
        if($id){
            $update_flg = $billM->retract($sale_id,$uid);
        }else{
            $update_flg = $billM->retractBySaleId($sale_id,$uid);
        }
        if(!$update_flg){
            $result['info'] = "撤回发票申请失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='撤回发票申请成功!';
        return $result;
    }
}