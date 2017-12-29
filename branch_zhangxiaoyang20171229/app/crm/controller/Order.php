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

class Order extends Initialize{
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
        $filter = $this->_getCustomerFilter(["in_column","bill_type","product_type","pay_type","apply_employee","customer_name","tax_num"]);
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
            $payTypeName = $billM->getAllPayTypeName();
            //var_exp($payTypeName,'$payTypeName',1);
            $this->assign('pay_type_name',$payTypeName);
            $productTypeName = $billM->getAllProductTypeName();
            //var_exp($productTypeName,'$productTypeName',1);
            $this->assign('product_type_name',$productTypeName);
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
        //return view();
        return "/crm/order/index";
    }
    protected function _getCustomerFilter($filter_column){
        $filter = [];

        //对应业务
        if(in_array("bill_type", $filter_column)){
            $in_column = input("bill_type",0,"int");
            if($in_column){
                $filter["bill_type"] = $in_column;
            }
        }

        //产品类型
        if(in_array("product_type", $filter_column)){
            $in_column = input("product_type",'',"string");
            if($in_column){
                $filter["product_type"] = $in_column;
            }
        }

        //收款银行
        if(in_array("pay_type", $filter_column)){
            $in_column = input("pay_type",'',"string");
            if($in_column){
                $filter["pay_type"] = $in_column;
            }
        }

        //申请人
        if(in_array("apply_employee", $filter_column)){
            $in_column = input("apply_employee",'',"string");
            if($in_column){
                $filter["apply_employee"] = $in_column;
            }
        }

        //客户名称
        if(in_array("customer_name", $filter_column)){
            $in_column = input("customer_name",'',"string");
            if($in_column){
                $filter["customer_name"] = $in_column;
            }
        }

        //公司税号
        if(in_array("tax_num", $filter_column)){
            $in_column = input("tax_num",'',"string");
            if($in_column){
                $filter["tax_num"] = $in_column;
            }
        }

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
}