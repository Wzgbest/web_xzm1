<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\crm\model\Customer as CustomerModel;
use app\crm\model\SaleChance;
use app\crm\model\CustomerTrace;
use app\crm\model\CustomerDelete as CustomerDelete;
use app\crm\model\CustomerNegotiate;
use app\systemsetting\model\CustomerSetting;
use app\common\model\Business;
use app\common\model\Employee as EmployeeModel;
use app\crm\model\SaleChance as SaleChanceModel;
use app\crm\model\SaleChanceSignIn as SaleChanceSignInModel;
use app\crm\model\CustomerContact as CustomerContactModel;
use app\crm\model\CustomerTrace as CustomerTraceModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\common\model\ParamRemark;
use app\crm\model\CallRecord;
use app\task\model\TaskTarget;
use app\common\model\StructureEmployee;
use app\common\model\Structure;
use app\systemsetting\model\BusinessFlowItemLink;
use app\common\model\RoleEmployee as RoleEmployeeModel;
use app\crm\model\Contract as ContractAppliedModel;
use app\datacount\model\Datacount;

class Customer extends Initialize{
    protected $_activityBusinessFlowItem = [1,2,4];
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
        echo "crm/customer/index";
    }
    public function customer_manage(){
        $show_flg = false;
        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }
        if(!$show_flg){
            $this->error("没有权限查看客户列表");
        }
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["grade","resource_from","comm_status","take_type","tracer","guardian","add_man"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getManageCustomer($num,$p,$filter,$field,$order,$direction);

            $all_public_to_employee_ids = [];
            $all_public_to_department_ids = [];
            foreach ($customers_data as $customer){
                $public_to_employee = $customer["public_to_employee"];
                $public_to_employee_arr = explode(",",$public_to_employee);
                if($public_to_employee_arr){
                    $all_public_to_employee_ids = array_merge($all_public_to_employee_ids,$public_to_employee_arr);
                }

                $public_to_department = $customer["public_to_department"];
                $public_to_department_arr = explode(",",$public_to_department);
                if($public_to_department_arr){
                    $all_public_to_department_ids = array_merge($all_public_to_department_ids,$public_to_department_arr);
                }
            }

            $all_public_to_employee_ids = array_filter($all_public_to_employee_ids);
            $all_public_to_employee_ids = array_unique($all_public_to_employee_ids);
            $all_public_to_department_ids = array_filter($all_public_to_department_ids);
            $all_public_to_department_ids = array_unique($all_public_to_department_ids);
            $employeeM = new EmployeeModel($this->corp_id);
            $employee_name_idx = $employeeM->getEmployeeNameByUserids($all_public_to_employee_ids);
            $structure = new Structure($this->corp_id);
            $structure_name_idx = $structure->getStructureName($all_public_to_department_ids);

            foreach ($customers_data as &$customer){
                $public_to_employee = $customer["public_to_employee"];
                $public_to_employee_arr = explode(",",$public_to_employee);
                $public_to_employee_name_arr = [];
                if($public_to_employee_arr){
                    $public_to_employee_arr = array_filter($public_to_employee_arr);
                    $public_to_employee_arr = array_unique($public_to_employee_arr);
                    foreach ($public_to_employee_arr as $public_to_employee_id){
                        if(isset($employee_name_idx[$public_to_employee_id])){
                            $public_to_employee_name_arr[] = $employee_name_idx[$public_to_employee_id];
                        }
                    }
                }
                $customer["public_to_employee_name"] = implode(",",$public_to_employee_name_arr);

                $public_to_department = $customer["public_to_department"];
                $public_to_department_arr = explode(",",$public_to_department);
                $public_to_department_name_arr = [];
                if($public_to_department_arr){
                    $public_to_department_arr = array_filter($public_to_department_arr);
                    $public_to_department_arr = array_unique($public_to_department_arr);
                    foreach ($public_to_department_arr as $public_to_department_id){
                        if(isset($employee_name_idx[$public_to_department_id])){
                            $public_to_department_name_arr[] = $structure_name_idx[$public_to_department_id];
                        }
                    }
                }
                $customer["public_to_department_name"] = implode(",",$public_to_department_name_arr);
            }

            $this->assign("listdata",$customers_data);
            $customerM = new CustomerModel($this->corp_id);
            $customers_count = $customerM->getManageCustomerCount($filter,$order,$direction);
            $this->assign("count",$customers_count);
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
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }
    public function my_customer(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["take_type","grade","sale_chance","comm_status","customer_name","contact_name","in_column"]);
        $field = $this->_getCustomerField(["take_type","grade"]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getSelfCustomer($uid,$num,$p,$filter,$field,$order,$direction);
            $customer_ids = array_column($customers_data,"id");
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $saleChanceInfos = $saleChanceM->getNameAndMoneyByCustomerIds($customer_ids);
            //var_exp($saleChanceInfos,'$saleChanceInfos');
            //var_exp($customers_data,'$customers_data');
            foreach ($customers_data as &$customers){
                if(isset($saleChanceInfos[$customers["id"]])){
                    $saleChanceInfo = $saleChanceInfos[$customers["id"]];
                    $customers["sale_names"] = $saleChanceInfo["sale_names"];
                    $customers["all_guess_money"] = $saleChanceInfo["all_guess_money"];
                    $customers["all_final_money"] = $saleChanceInfo["all_final_money"];
                    $customers["all_payed_money"] = $saleChanceInfo["all_payed_money"];
                }
            }
            //var_exp($customers_data,'$customers_data');
            $this->assign("listdata",$customers_data);
            $customerM = new CustomerModel($this->corp_id);
            $customers_count = $customerM->getSelfCustomerCount($uid,$filter,$order,$direction);
            $this->assign("count",$customers_count);
            $listCount = $customerM->getColumnNum($uid,$filter);
            $this->assign("listCount",$listCount);
            $businessFlowModel = new BusinessFlowModel($this->corp_id);
            $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
            $this->assign('business_flows',$business_flows);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($customers_count/$num);
        $in_column = isset($filter["in_column"])?$filter["in_column"]:0;
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("in_column",$in_column);
        $this->assign("start_num",$customers_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$customers_count?$end_num:$customers_count);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }
    public function public_customer_pool(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];

        //获取客户配置
        $view_name="";
        $struct_ids = getStructureIds($uid);
        $customerSettingModel = new CustomerSetting();
        $searchCustomerList = $customerSettingModel->getCustomerSettingByStructIds($struct_ids);
        //var_exp($searchCustomerList,'$searchCustomerList',1);
        $public_flg = false;
        foreach ($searchCustomerList as $customerSetting){
            if($customerSetting["public_sea_seen"]==1){
                $public_flg = true;
                break;
            }
        }
        //var_exp($public_flg,'$public_flg',1);
        if($public_flg){
            $view_name="public_pool";
            $filter = $this->_getCustomerFilter(["resource_from","grade","customer_name"]);
            $field = $this->_getCustomerField([]);
            try{
                $customerM = new CustomerModel($this->corp_id);
                $customers_data = $customerM->getPublicPoolCustomer($uid,$num,$p,$filter,$field,$order,$direction);
                //var_exp($customers_data,'$customers_data',1);
                $this->assign("listdata",$customers_data);
                $customerM = new CustomerModel($this->corp_id);
                $customers_count = $customerM->getPublicPoolCustomerCount($uid,$filter,$order,$direction);
                $this->assign("count",$customers_count);
            }catch (\Exception $ex){
                $result['info'] = $ex->getMessage();
                return json($result);
            }
        }else{
            $view_name="anonymous_pool";
            $filter = $this->_getCustomerFilter(["resource_from","is_public","customer_name"]);
            $field = $this->_getCustomerField([]);
            try{
                $customerM = new CustomerModel($this->corp_id);
                $customers_data = $customerM->getPoolCustomer($uid,$num,$p,$filter,$field,$order,$direction);
                $this->assign("listdata",$customers_data);
                $customerM = new CustomerModel($this->corp_id);
                $customers_count = $customerM->getPoolCustomerCount($uid,$filter);
                $this->assign("count",$customers_count);
            }catch (\Exception $ex){
                $this->error($ex->getMessage());
            }
        }
        $max_page = ceil($customers_count/$num);
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("start_num",$customers_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$customers_count?$end_num:$customers_count);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view($view_name);
    }
    public function customer_pending(){
        return "crm/customer/customer_pending";
    }
    public function customer_subordinate(){
        return "crm/customer/customer_subordinate";
    }
    protected function _showCustomer(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $info_array = [];
        $info_array["id"] = $id;
        $this->assign("fr",input('fr'));
        $customerM = new CustomerModel($this->corp_id);
        $customerData = $customerM->getCustomer($id);
        $customerData["website_arr"] = explode(",",$customerData["website"]);
        $info_array["customer"] = $customerData;
        $customerM = new CustomerContactModel($this->corp_id);
        $customer_contact_num = $customerM->getCustomerContactCount($id);
        $info_array["customer_contact_num"] = $customer_contact_num;
        $customerM = new SaleChance($this->corp_id);
        $sale_chance_num = $customerM->getSaleChanceCount($id);
        $info_array["sale_chance_num"] = $sale_chance_num;
        $customerM = new CustomerTrace($this->corp_id);
        $customer_trace_num = $customerM->getCustomerTraceCount($id);
        $info_array["customer_trace_num"] = $customer_trace_num;
        $business = new Business($this->corp_id);
        $business_list = $business->getBusinessArray();
        $info_array["business_array"] = $business_list;
        $con['add_man']=array('in',array('0',$uid));
        $paramModel=new ParamRemark($this->corp_id);
        $param_array = $paramModel->getParamArray($con);
        $info_array["param_array"] = $param_array;
        return $info_array;
    }
    public function add_page(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $this->assign("fr",input('fr'));
        $customerData["customer_name"] = "";
        $this->assign("customer",$customerData);
        $sale_chance["prepay_time"]=time();
        $sale_chance["guess_money"]=0;
        $this->assign('sale_chance',$sale_chance);
        $business = new Business($this->corp_id);
        $business_list = $business->getAllBusiness();
        $this->assign("business_list",$business_list);
        $businessFlowModel = new BusinessFlowModel($this->corp_id);
        $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
        $this->assign('business_flows',$business_flows);
        $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
        $businessFlowItemLinks = $businessFlowItemLinkM->getAllBusinessFlowItemLink();
//        var_exp($businessFlowItemLinks,'$businessFlowItemLinks');
//        $this->assign('business_flow_item_links',$businessFlowItemLinks);
        $businessFlowItemIdx = [];
        $businessFlowRoleIdx = [];
        $role_ids = [];
        foreach ($businessFlowItemLinks as $businessFlowItemLink){
            if(in_array($businessFlowItemLink["item_id"],$this->_activityBusinessFlowItem)){
                $businessFlowItemIdx[$businessFlowItemLink["setting_id"]][]=[
                    "item_id"=>$businessFlowItemLink["item_id"],
                    "item_name"=>$businessFlowItemLink["item_name"]
                ];
                $businessFlowRoleIdx[$businessFlowItemLink["setting_id"]][$businessFlowItemLink["item_id"]]=[
                    'handle_1' => $businessFlowItemLink["handle_1"],
                    'handle_2' => $businessFlowItemLink["handle_2"],
                    'handle_3' => $businessFlowItemLink["handle_3"],
                    'handle_4' => $businessFlowItemLink["handle_4"],
                    'handle_5' => $businessFlowItemLink["handle_5"],
                    'handle_6' => $businessFlowItemLink["handle_6"]
                ];
                $role_ids[] = $businessFlowItemLink["handle_1"];
                $role_ids[] = $businessFlowItemLink["handle_2"];
                $role_ids[] = $businessFlowItemLink["handle_3"];
                $role_ids[] = $businessFlowItemLink["handle_4"];
                $role_ids[] = $businessFlowItemLink["handle_5"];
                $role_ids[] = $businessFlowItemLink["handle_6"];
            }
        }
//        var_exp($businessFlowItemIdx,'$businessFlowItemIdx');
//        var_exp($businessFlowRoleIdx,'$businessFlowRoleIdx');
        $role_ids = array_filter($role_ids);
        $role_ids = array_unique($role_ids);
//        $role_ids = array_merge($role_ids);
        $role_empM = new RoleEmployeeModel($this->corp_id);
        $employeeNameList = $role_empM->getEmployeeNameListbyRole($role_ids);
        //var_exp($employeeNameList,'$employeeNameList',1);
        $role_employee_index = [];
        foreach($employeeNameList as $employee_info){
            $role_id = $employee_info["role_id"];
            unset($employee_info["role_id"]);
            $role_employee_index[$role_id][] = $employee_info;
        }
        foreach($role_ids as $role_id){
            if(!isset($role_employee_index[$role_id])){
                $role_employee_index[$role_id] = [];
            }
        }
//        var_exp($role_employee_index,'$role_employee_index',1);
        $this->assign('business_flow_item_index',json_encode($businessFlowItemIdx,true));
        $this->assign('business_flow_role_index',json_encode($businessFlowRoleIdx,true));
        $this->assign('role_employee_index',json_encode($role_employee_index,true));

        $SaleChancesVisitData["visit_time"] = time();
        $SaleChancesVisitData["visit_place"] = "";
        $SaleChancesVisitData["partner_notice"] = 0;
        $SaleChancesVisitData["add_note"] = 0;
        $SaleChancesVisitData["location"] = "";
        $location = explode(",",$SaleChancesVisitData["location"]);
        $SaleChancesVisitData["lat"] = isset($location[0])&&!empty($location[0])?$location[0]:"36.7075";
        $SaleChancesVisitData["lng"] = isset($location[1])&&!empty($location[1])?$location[1]:"119.1324";
        $this->assign('saleChancesVisitData',$SaleChancesVisitData);

        $saleOrderContractData = [];
        $saleOrderContractItem = [];
        $inContractId = [];
        $saleOrderContractData["prod_desc"] = '';
        $saleOrderContractData["handle_1"] = '';
        $saleOrderContractData["handle_2"] = '';
        $saleOrderContractData["handle_3"] = '';
        $saleOrderContractData["handle_4"] = '';
        $saleOrderContractData["handle_5"] = '';
        $saleOrderContractData["handle_6"] = '';
        $saleOrderContractData["contract_handle_1"] = '';
        $saleOrderContractData["contract_handle_2"] = '';
        $saleOrderContractData["contract_handle_3"] = '';
        $saleOrderContractData["contract_handle_4"] = '';
        $saleOrderContractData["contract_handle_5"] = '';
        $saleOrderContractData["contract_handle_6"] = '';

        $saleOrderContractItem[] = [
            "contract_id"=>0,
            "order_money"=>0.00,
            "pay_money"=>0.00,
            "pay_type"=>0,
            "pay_name"=>'',
            "due_time"=>time(),
            "need_bill"=>0,
            "pay_bank"=>''
        ];
        //var_exp($saleOrderContractItem,'$saleOrderContractItem',1);
        $this->assign('saleOrderContractData',$saleOrderContractData);
        $this->assign('saleOrderContractItem',$saleOrderContractItem);
        $contract_status = 5;
        $contractAppliedModel = new ContractAppliedModel($this->corp_id);
        $contracts = $contractAppliedModel->getAllContractNoAndType($uid,null,[],$contract_status,null,$inContractId);
        //var_exp($contracts,'$contracts',1);
        $this->assign('contract_list',$contracts);
        $this->assign('empty','<option value="" class="empty">无</option>');
        $contract_type_index = [];
        $contract_bank_index = [];
        foreach($contracts as $contract){
            $contract_type_index[$contract["id"]] = $contract["contract_type_name"];
            $contract_bank_index[$contract["id"]] = $contract["bank_type"];
        }
        $this->assign('contract_type_name_json',json_encode($contract_type_index,true));
        $this->assign('contract_bank_name_json',json_encode($contract_bank_index,true));

        $con['add_man']=array('in',array('0',$uid));
        $paramModel=new ParamRemark($this->corp_id);
        $param_list = $paramModel->getAllParam($con);
        $this->assign("param_list",$param_list);
        $truename = $userinfo["truename"];
        $this->assign("truename",$truename);
        $now_time = time();
        $this->assign("now_time",$now_time);
        return view();
    }
    public function general(){
        $info_array = $this->_showCustomer();
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $show_flg = false;
        //客户跟踪人验证
        if($info_array["customer"]["handle_man"]==$uid){
            $show_flg = true;
        }

        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }

        //帮跟权限
        if(!$show_flg){
            $taskTargetM = new TaskTarget($this->corp_id);
            $taskTargetInfo = $taskTargetM->findTaskTargetByCustomerId($uid,$info_array["id"],$time);
            //var_exp($taskTargetInfo,'$taskTargetInfo',1);
            if(!empty($taskTargetInfo)){
                $show_flg = true;
            }
        }

        if(!$show_flg){
            $this->assign("error_msg","没有权限查看该客户");
            return view("no_permission");
        }
        $this->assign($info_array);
        $employeeM = new EmployeeModel($this->corp_id);
        $handle_employee = $employeeM->getEmployeeByUserid($info_array["customer"]["handle_man"]);
        $this->assign("handle_employee",$handle_employee);
        $add_employee = $employeeM->getEmployeeByUserid($info_array["customer"]["add_man"]);
        $this->assign("add_employee",$add_employee);
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $SaleChancesData = $saleChanceM->getAllSaleChancesByCustomerId($info_array["id"]);
        $this->assign("sale_chance",$SaleChancesData);
        $saleChanceSignInM = new SaleChanceSignInModel($this->corp_id);
        $signInData = $saleChanceSignInM->getLastSignInAndNum($info_array["id"]);
        //var_exp($signInData,'$signInData',1);
        if(empty($signInData)){
            $signInData["last_sign_in_time"] = "";
            $signInData["sign_in_num"] = "0";
        }
        $this->assign("sign_in_count",$signInData);
        $callRecordM = new CallRecord($this->corp_id);
        $callRecordData = $callRecordM->getLastCallRecordAndNum($info_array["id"]);
        //var_exp($callRecordData,'$callRecordData',1);
        if(empty($callRecordData)){
            $callRecordData["last_call_time"] = "";
            $callRecordData["call_out_num"] = "0";
            $callRecordData["call_in_num"] = "0";
        }
        $this->assign("call_count",$callRecordData);
        $customerM = new CustomerContactModel($this->corp_id);
        $customerContactData = $customerM->getAllCustomerContactsByCustomerId($info_array["id"]);
        $this->assign("customer_contact",$customerContactData);
        return view();
    }
    public function show(){
        $info_array = $this->_showCustomer();
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $show_flg = false;
        //客户跟踪人验证
        if($info_array["customer"]["handle_man"]==$uid){
            $show_flg = true;
        }

        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }

        if(!$show_flg){
            $taskTargetM = new TaskTarget($this->corp_id);
            $taskTargetInfo = $taskTargetM->findTaskTargetByCustomerId($uid,$info_array["id"],$time);
            //var_exp($taskTargetInfo,'$taskTargetInfo',1);
            if(!empty($taskTargetInfo)){
                $show_flg = true;
            }
        }

        if(!$show_flg){
            $this->assign("error_msg","没有权限查看该客户");
            return view("no_permission");
        }
        $this->assign($info_array);
        return view();
    }
    public function edit(){
        $info_array = $this->_showCustomer();
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $show_flg = false;
        //客户跟踪人验证
        if($info_array["customer"]["handle_man"]==$uid){
            $show_flg = true;
        }

        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }

        if(!$show_flg){
            $taskTargetM = new TaskTarget($this->corp_id);
            $taskTargetInfo = $taskTargetM->findTaskTargetByCustomerId($uid,$info_array["id"],$time);
            //var_exp($taskTargetInfo,'$taskTargetInfo',1);
            if(!empty($taskTargetInfo)){
                $show_flg = true;
            }
        }

        if(!$show_flg){
            $this->assign("error_msg","没有权限编辑该客户");
            return view("no_permission");
        }
        $this->assign($info_array);
        return view();
    }
    public function change_customers_visible_range_page(){
        return view();
    }
    public function change_customers_to_employee_page(){
//        if(($this->checkRule('crm/customer/customer_manage/change_customers_to_employee'))){
//            $this->noRole(2);
//        }
        $structureEmployeeModel = new StructureEmployee($this->corp_id);
        $structures = $structureEmployeeModel->getAllStructureAndEmployee();
        $structure_employee = [];
        $structure_list = [];
        foreach ($structures as $structure){
            $structure_employee[$structure["id"]] = explode(",",$structure["employee_ids"]);
            $structure_list[$structure["id"]] = ["pid"=>$structure["struct_pid"],"name"=>$structure["struct_name"]];
        }
        $employM = new EmployeeModel($this->corp_id);
        $friendsInfos = $employM->getAllUsers();
        $employee_name = [];
        foreach ($friendsInfos as $friendsInfo){
            $employee_name[$friendsInfo["id"]] = $friendsInfo["nickname"];
        }
        $this->assign("structures",$structures);
        $this->assign("structure_employee",json_encode($structure_employee,true));
        $this->assign("structure_list",json_encode($structure_list,true));
        $this->assign("employee_name",json_encode($employee_name,true));
        return view();
    }


    public function manage(){
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $show_flg = false;
        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }
        if(!$show_flg){
            $result['info'] = "没有权限查看客户列表";
            return json($result);
        }
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $filter = $this->_getCustomerFilter(["belongs_to","resource_from","comm_status","take_type","tracer","guardian","add_man"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getManageCustomer($num,$p,$filter,$field,$order,$direction);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    public function pool(){
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        //获取客户配置
        $struct_ids = getStructureIds($uid);
        $customerSettingModel = new CustomerSetting();
        $searchCustomerList = $customerSettingModel->getCustomerSettingByStructIds($struct_ids);
        $public_flg = false;
        foreach ($searchCustomerList as $customerSetting){
            if(!$customerSetting["public_sea_seen"]==1){
                $public_flg = true;
                break;
            }
        }
        if($public_flg){
            $result = $this->public_pool();
        }else{
            $result = $this->anonymous_pool();
        }
        return json($result);
    }
    protected function public_pool(){
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["resource_from","grade","customer_name"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getPublicPoolCustomer($uid,$num,$p,$filter,$field,$order,$direction);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    protected function anonymous_pool(){
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["resource_from","is_public","customer_name"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getPoolCustomer($uid,$num,$p,$filter,$field,$order,$direction);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    public function self(){
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $num=0;
        $p=0;
        $get_all = input("get_all",0,"int");
        if(!$get_all){
            $num = input('num',$this->paginate_list_rows,'int');
            $p = input("p",1,"int");
        }
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["phone","take_type","grade","sale_chance","comm_status","customer_name","tracer","contact_name","in_column"]);
        $field = $this->_getCustomerField(["take_type","grade"]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getSelfCustomer($uid,$num,$p,$filter,$field,$order,$direction);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    public function subordinate(){
        //TODO 权限验证?
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["take_type","grade","sale_chance","belongs_to","comm_status","customer_name","tracer","contact_name","in_column"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getSubordinateCustomer($uid,$num,$p,$filter,$field,$order,$direction);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    public function pending(){//TODO
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $filter = $this->_getCustomerFilter([]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getPendingCustomer($num,$p,$filter,$field,$order,$direction);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    protected function _getCustomerFilter($filter_column){
        $filter = [];
        if(in_array("belongs_to", $filter_column)){//客户状态
            $belongs_to = input("belongs_to",0,"int");
            if($belongs_to && in_array($belongs_to,[1,2,3])){//TODO 维护状态??
                $filter["belongs_to"] = $belongs_to;
            }
        }
        if(in_array("tracer", $filter_column)){//TODO 跟踪人??
            $tracer = input("tracer");
            if($tracer){
                $filter["tracer"] = $tracer;
            }
        }
        if(in_array("guardian", $filter_column)){//TODO 维护人??
            $guardian = input("guardian");
            if($guardian){
                $filter["guardian"] = $guardian;
            }
        }
        if(in_array("add_man", $filter_column)){//添加人
            $add_man = input("add_man","","string");
            if($add_man){
                $filter["add_man"] = $add_man;
            }
        }
        if(in_array("resource_from", $filter_column)){//客户来源
            $resource_from = input("resource_from",0,"int");
            if($resource_from && in_array($resource_from,[1,2,3])){
                $filter["resource_from"] = $resource_from;
            }
        }
        if(in_array("take_type", $filter_column)){//获取途径
            $take_type = input("take_type",0,"int");
            if($take_type){
                $filter["take_type"] = $take_type;
            }
        }
        if(in_array("grade", $filter_column)){//客户级别
            $grade = input("grade","","string");
            if($grade){
                $filter["grade"] = $grade;
            }
        }
        if(in_array("customer_name", $filter_column)){//客户名称
            $customer_name = input("customer_name","","string");
            if($customer_name){
                $filter["customer_name"] = $customer_name;
            }
        }
        if(in_array("contact_name", $filter_column)){//联系人名称
            $contact_name = input("contact_name","","string");
            if($contact_name){
                $filter["contact_name"] = $contact_name;
            }
        }
        if(in_array("phone", $filter_column)){//电话
            $phone = input("phone","","string");
            if($phone){
                $filter["phone"] = $phone;
            }
        }
        if(in_array("comm_status", $filter_column)){//沟通状态
            $comm_status = input("comm_status",0,"int");
            if($comm_status){
                $filter["comm_status"] = $comm_status;
            }
        }
        if(in_array("sale_chance", $filter_column)){//商机业务
            $comm_status = input("sale_chance",0,"int");
            if($comm_status){
                $filter["sale_chance"] = $comm_status;
            }
        }
        if(in_array("is_public", $filter_column)){//可见范围
            $is_public = input("is_public",0,"int");
            if($is_public){
                $filter["is_public"] = $is_public;
            }
        }

        //所在列
        if(in_array("in_column", $filter_column)){
            $in_column = input("in_column",0,"int");
            if($in_column){
                $filter["in_column"] = $in_column;
            }
        }
        return $filter;
    }
    protected function _getCustomerField($field_column){
        $field = [];
        $fields = input('field',"",'string');
        $fields_arr = explode(',',$fields);
        $fields_arr = array_filter($fields_arr);
        $fields_arr = array_unique($fields_arr);
        if(in_array("customer_name", $field_column) && in_array("customer_name", $fields_arr)){//客户名称
            $field[] = "customer_name";
        }
        if(in_array("take_type", $field_column) && in_array("take_type", $fields_arr)){//获取途径
            $field[] = "take_type";
        }
        if(in_array("grade", $field_column) && in_array("grade", $fields_arr)){//客户级别
            $field[] = "grade";
        }
        if(in_array("comm_status", $field_column) && in_array("comm_status", $fields_arr)){//沟通状态
            $field[] = "comm_status";
        }
        if(in_array("sale_biz_names", $field_column) && in_array("sale_biz_names", $fields_arr)){//商机
            $field[] = "sale_biz_names";
        }
        if(in_array("all_guess_money", $field_column) && in_array("all_guess_money", $fields_arr)){//商机
            $field[] = "all_guess_money";
        }
        if(in_array("all_final_money", $field_column) && in_array("all_final_money", $fields_arr)){//商机
            $field[] = "all_final_money";
        }
        if(in_array("contact_name", $field_column) && in_array("contact_name", $fields_arr)){//商机
            $field[] = "contact_name";
        }
        if(in_array("phone_first", $field_column) && in_array("phone_first", $fields_arr)){//商机
            $field[] = "phone_first";
        }
        if(in_array("last_trace_time", $field_column) && in_array("last_trace_time", $fields_arr)){//上次跟进时间
            $field[] = "last_trace_time";
        }
        if(in_array("save_time_str", $field_column) && in_array("save_time_str", $fields_arr)){//剩余保有时间
            $field[] = "save_time_str";
        }
        if(in_array("contract_due_time_str", $field_column) && in_array("contract_due_time_str", $fields_arr)){//合同到期时间
            $field[] = "contract_due_time_str";
        }
        if(in_array("remind_time_str", $field_column) && in_array("remind_time_str", $fields_arr)){//提醒时间
            $field[] = "remind_time_str";
        }
        if(in_array("in_column", $field_column) && in_array("in_column", $fields_arr)){//所在列
            $field[] = "in_column";
        }
        return $field;
    }
    public function get_column_num(){
        $result = ['status'=>0 ,'info'=>"查询客户列信息时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["take_type","grade","customer_name","contact_name","comm_status","sale_chance"]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $listCount = $customerM->getColumnNum($uid,$filter);
            $result['data'] = $listCount;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询客户列信息成功！";
        return json($result);
    }
    public function take_public_customers_to_self(){
        //TODO 权限验证?
        $result = ['status'=>0 ,'info'=>"变更客户时发生错误！"];
        $ids = input('ids/a');
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        try{
            $customerM = new CustomerModel($this->corp_id);
            $releaseFlg = $customerM->takeCustomers($ids,$uid);
            //TODO add trace
            if(!$releaseFlg){
                exception('变更客户失败!');
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "变更客户成功！";
        return json($result);
    }
    public function take_customers_to_self(){
        $result = ['status'=>0 ,'info'=>"申领客户时发生错误！"];
        $ids = input('ids/a');
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        try{
            //TODO 检查申领次数
            $customerM = new CustomerModel($this->corp_id);
            $releaseFlg = $customerM->takeCustomers($ids,$uid);
            //TODO add trace
            if(!$releaseFlg){
                exception('申领客户时发生错误!');
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "申领客户成功！";
        return json($result);
    }
    public function release_customers(){
        $result = ['status'=>0 ,'info'=>"释放客户时发生错误！"];
        $ids = input('ids/a');
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        try{
            $customerM = new CustomerModel($this->corp_id);
            $releaseFlg = $customerM->releaseCustomers($ids,$uid);
            //TODO add trace
            if(!$releaseFlg){
                exception('释放客户失败!');
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "释放客户成功！";
        return json($result);
    }
    public function imposed_release_customers(){
        //TODO 权限验证?
        $result = ['status'=>0 ,'info'=>"强制释放客户时发生错误！"];
        $ids = input('ids/a');
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerM = new CustomerModel($this->corp_id);
            $releaseFlg = $customerM->releaseCustomers($ids);
            //TODO add trace
            if(!$releaseFlg){
                exception('强制释放客户失败!');
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $recevies_uids = $customerM->employeesIdsByCustomers($ids);
        save_msg("你有客户被强制释放了，请到公海池查看！","/crm/customer/public_customer_pool",$recevies_uids,4,6,$uid);
        $result['status'] = 1;
        $result['info'] = "强制释放客户成功！";
        return json($result);
    }
    public function change_customers_to_employee(){
        //TODO 权限验证?
        $result = ['status'=>0 ,'info'=>"重分客户时发生错误！"];
        $ids = input('ids/a');
        $uid = input('uid',0,"int");
        if(!$ids || !$uid){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerM = new CustomerModel($this->corp_id);
            $releaseFlg = $customerM->takeCustomers($ids,$uid);
            //var_exp($releaseFlg,'$releaseFlg',1);
            //TODO add trace
            if(!$releaseFlg){
                exception('重分客户时发生错误!');
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "重分客户成功！";
        return json($result);
    }
    public function send_customer_group_message(){
        $result = ['status'=>0 ,'info'=>"群发短信时发生错误！"];
        $ids = input('ids/a');
        //var_exp($ids,'$ids',1);
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        //TODO 获取手机号, send_sms ($tel,$code,$content);
        $result['info'] = "群发短信功能开发中！";
        return json($result);
    }
    public function change_customers_visible_range(){
        //TODO 权限验证?
        $result = ['status'=>0 ,'info'=>"更改客户可见范围失败！"];
        $ids = input('ids/a');
        //var_exp($ids,'$ids',1);
        if(empty($ids)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $is_public = input('is_public');
        $employees = input('employees/a',[]);
        $departments = input('departments/a',[]);
        if(!$is_public && !($employees || $departments)){
            $result['info'] = "参数错误,不是全员可见必须选择部门或员工！";
            return json($result);
        }
        $is_public = $is_public?1:0;
        $employees_str = implode(",",$employees);
        $departments_str = implode(",",$departments);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $releaseFlg = $customerM->changeCustomersVisibleRange($ids,$is_public,$employees_str,$departments_str);
            //var_exp($releaseFlg,'$releaseFlg',1);
            //TODO add trace
            if(!$releaseFlg){
                exception('更改客户可见范围时发生错误!');
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "更改客户可见范围成功！";
        return json($result);
    }
    public function get_customer_general(){
        $result = ['status'=>0 ,'info'=>"获取客户信息时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();

        $customerM = new CustomerModel($this->corp_id);
        $customerData = $customerM->getCustomer($id);

        $show_flg = false;
        //客户跟踪人验证
        if($customerData["handle_man"]==$uid){
            $show_flg = true;
        }

        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }

        //帮跟权限
        if(!$show_flg){
            $taskTargetM = new TaskTarget($this->corp_id);
            $taskTargetInfo = $taskTargetM->findTaskTargetByCustomerId($uid,$id,$now_time);
            //var_exp($taskTargetInfo,'$taskTargetInfo',1);
            if(!empty($taskTargetInfo)){
                $show_flg = true;
            }
        }

        if(!$show_flg){
            $result['info'] = "没有权限查看该客户";
            return json($result);
        }

        try{
            $customerM = new CustomerModel($this->corp_id);
            $customerData = $customerM->getCustomer($id);
            if(empty($customerData)){
                exception("未找到客户!");
            }
            $employeeM = new EmployeeModel($this->corp_id);
            $handle_employee = $employeeM->getEmployeeByUserid($customerData["handle_man"]);
            $customerData["handle_employee"] = $handle_employee;
            $add_employee = $employeeM->getEmployeeByUserid($customerData["add_man"]);
            $customerData["add_employee"] = $add_employee;
            $customerM = new SaleChanceModel($this->corp_id);
            $SaleChancesData = $customerM->getAllSaleChancesByCustomerId($id);
            $customerData["sale_chance"] = $SaleChancesData;
            $customerM = new CustomerContactModel($this->corp_id);
            $customerContactData = $customerM->getAllCustomerContactsByCustomerId($id);
            $customerData["customer_contact"] = $customerContactData;
            $result['data'] = $customerData;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取客户信息成功！";
        return json($result);
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取客户信息时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();

        $customerM = new CustomerModel($this->corp_id);
        $customerData = $customerM->getCustomerAndHaveVisit($id);

        $show_flg = false;
        //客户跟踪人验证
        if($customerData["handle_man"]==$uid){
            $show_flg = true;
        }

        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }

        //帮跟权限
        if(!$show_flg){
            $taskTargetM = new TaskTarget($this->corp_id);
            $taskTargetInfo = $taskTargetM->findTaskTargetByCustomerId($uid,$id,$now_time);
            //var_exp($taskTargetInfo,'$taskTargetInfo',1);
            if(!empty($taskTargetInfo)){
                $show_flg = true;
            }
        }

        if(!$show_flg){
            $result['info'] = "没有权限查看该客户";
            return json($result);
        }

        $result['data'] = $customerData;
        $result['status'] = 1;
        $result['info'] = "获取客户信息成功！";
        return json($result);
    }

    public function get_self_customer_phone(){
        $result = ['status'=>0 ,'info'=>"获取我的所有客户电话信息时发生错误！"];

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $order = input("order","id","string");
        $direction = input("direction","desc","string");

        try{
            $customer_phone = [];
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getSelfCustomer($uid,0,0,[],[],$order,$direction);
            $customer_ids = [];
            foreach ($customers_data as $customer) {
                $phone_item = [
                    "id" => $customer["id"],
                    "name" => $customer["customer_name"],
                    "phone" => $customer["telephone"],
                    "type" => "customer",
                    "num" => 1,
                ];
                $customer_phone[] = $phone_item;
                $customer_ids[] = $customer["id"];
            }
            $customerM = new CustomerContactModel($this->corp_id);
            $customer_contact_list = $customerM->getCustomersPhone($customer_ids);
            foreach ($customer_contact_list as $customer_contact){
                $suffixs = ["first","second","third"];
                $idx = 1;
                foreach ($suffixs as $suffix){
                    if(empty($customer_contact["phone_".$suffix])){
                        continue;
                    }
                    $phone_item = [
                        "id"=>$customer_contact["id"],
                        "name"=>$customer_contact["contact_name"],
                        "phone"=>$customer_contact["phone_".$suffix],
                        "type"=>"contact",
                        "num"=>$idx,
                    ];
                    $customer_phone[] = $phone_item;
                    $idx++;
                }
            }
            $result['data'] = $customer_phone;
        }catch (\Exception $ex){
            $result["info"] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取我的所有客户电话信息成功！";
        return json($result);
    }
    
    public function getCustomerBusiness(){
        $result = ['status'=>0 ,'info'=>"获取客户行业列表时发生错误！"];
        $business = new Business($this->corp_id);
        $business_list = $business->getAllBusiness();
        $result['data'] = $business_list;
        $result['status'] = 1;
        $result['info'] = "获取客户行业列表成功！";
        return json($result);
    }

    protected function _getCustomerForInput($mode){
        // add customer page
        if($mode){
            $userinfo = get_userinfo();
            $uid = $userinfo["userid"];
            $customer['belongs_to'] = input('belongs_to',0,'int');
            $customer['add_man'] = $uid;
            $customer['add_time'] = time();
            $customer['handle_man'] = ($customer['belongs_to']==2)?0:$uid;
        }

        $customer['customer_name'] = input('customer_name','','string');
        $customer['telephone'] = input('telephone','','string');

        $customer['resource_from'] = input('resource_from',0,'int');
        $customer['take_type'] = input('take_type',0,'int');
        $customer['grade'] = input('grade','','string');

        $customer['field1'] = input('field1',0,'int');
        $customer['field2'] = input('field2',0,'int');
        $customer['field'] = input('field',0,'int');
        $customer['prov'] = input('prov','','string');
        $customer['city'] = input('city','','string');
        $customer['dist'] = input('dist','','string');
        $customer['address'] = input('address');
        $customer['location'] = input('location');
        $customer['lat'] = "".number_format(input('lat',0,'float'),6,".","");
        $customer['lng'] = "".number_format(input('lng',0,'float'),6,".","");
        $customer['website'] = input('website','','string');
        $customer['remark'] = input('remark','','string');

        $customer["last_edit_time"] = time();
        return $customer;
    }
    protected function _getCustomerNegotiateForInput(){
        $comm_status = input('comm_status',0,'int');
        $customerNegotiate = getCommStatusArr($comm_status);
        return $customerNegotiate;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建客户时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        //TODO 添加权限验证
        $customer = $this->_getCustomerForInput("all");
        if(!in_array($customer['belongs_to'],[2,3])){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $validate_result = $this->validate($customer,'Customer');
        //验证字段
        if(true !== $validate_result){
            $result["info"] = $validate_result;
            return json($result);
        }
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        $customerM = new CustomerModel($this->corp_id);
        $haveName = $customerM->getCustomerByName($customer['customer_name']);
        //var_exp($haveName,'$haveName',1);
        if (!empty($haveName)) {
            $result['info']="该名称的客户已存在!";
            return json($result);
        }
        try{
            $customerM->link->startTrans();
            $customerId = $customerM->addCustomer($customer);
            if(!$customerId){
                exception('添加客户失败!');
            }
            $customerNegotiate["customer_id"] = $customerId;
            $customerNegotiateM = new CustomerNegotiate($this->corp_id);
            $customersNegotiateId = $customerNegotiateM->addCustomerNegotiate($customerNegotiate);
            if(!$customersNegotiateId){
                exception('添加客户沟通状态失败!');
            }

            $datacount["uid"] = $uid;
            $datacount["time"] = time();
            $datacount["type"] = 6;
            $datacount["link_id"] = $customerId;
            $datacount["num"] = 1;
            $datacountM = new Datacount();
            $data_count_flg  = $datacountM->addDatacount($datacount);
            if(!$data_count_flg){
                exception('添加客户统计失败!');
            }
            $result['data'] = $customerId;
            $customerM->link->commit();
        }catch (\Exception $ex){
            $customerM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "新建客户信息成功！";
        return json($result);
    }
    public function getUpdateItemNameAndType(){
        $itemName["customer_name"] = ["客户名称"];
        $itemName["telephone"] = ["联系电话"];
        $itemName["resource_from"] = ["客户来源"];
        $itemName["grade"] = ["客户级别"];
        $itemName["field1"] = ["客户行业[1]",'getBusinessName'];
        $itemName["field2"] = ["客户行业[2]",'getBusinessName'];
        $itemName["field"] = ["客户行业[3]",'getBusinessName'];
        $itemName["prov"] = ["省份"];
        $itemName["city"] = ["城市"];
        $itemName["dist"] = ["区县"];
        $itemName["address"] = ["详细地址"];
        $itemName["location"] = ["详细定位"];
        $itemName["lat"] = ["坐标纬度"];
        $itemName["lng"] = ["坐标经度"];
        $itemName["website"] = ["公司官网"];
        $itemName["remark"] = ["备注"];

        $itemName["comm_status"] = ["沟通状态","getCommStatusName"];
        return $itemName;
    }
    public function update(){
        $result = ['status'=>0 ,'info'=>"保存客户时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();

        $customerM = new CustomerModel($this->corp_id);
        $customerData = $customerM->getCustomer($id);

        $show_flg = false;
        //客户跟踪人验证
        if($customerData["handle_man"]==$uid){
            $show_flg = true;
        }

        //TODO 管理员权限验证
        if(!$show_flg) {
            $show_flg = true;
        }

        //帮跟权限 屏蔽
//        if(!$show_flg){
//            $taskTargetM = new TaskTarget($this->corp_id);
//            $taskTargetInfo = $taskTargetM->findTaskTargetByCustomerId($uid,$id,$now_time);
//            //var_exp($taskTargetInfo,'$taskTargetInfo',1);
//            if(!empty($taskTargetInfo)){
//                $show_flg = true;
//            }
//        }

        if(!$show_flg){
            $result['info'] = "没有权限编辑该客户";
            return json($result);
        }

        $customer = $this->_getCustomerForInput(0);
        $validate_result = $this->validate($customer,'Customer');
        //验证字段
        if(true !== $validate_result){
            $result["info"] = $validate_result;
            return json($result);
        }
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        $customerM = new CustomerModel($this->corp_id);
        $customerOldData = $customerM->getCustomer($id);
        if($customerOldData["customer_name"]!=$customer['customer_name']){
            $haveName = $customerM->getCustomerByName($customer['customer_name'],$id);
            //var_exp($haveName,'$haveName',1);
            if (!empty($haveName)) {
                $result['info']="该名称的客户已存在!";
                return json($result);
            }
        }
        $customer["comm_status"] = input('comm_status',0,'int');

        //var_exp($customerOldData,'$customerOldData');
        $updateItemName = $this->getUpdateItemNameAndType();
        $customerIntersertData = array_intersect_key($customerOldData,$customer);
        $customerIntersertData = array_intersect_key($customerIntersertData,$updateItemName);
        //var_exp($customerIntersertData,'$customerIntersertData');
        //var_exp($customer,'$customer');
        $customerDiffData = array_diff_assoc($customerIntersertData,$customer);
        //var_exp($customerDiffData,'$customerDiffData',1);
        $table = 'customer';
        $customersTraces = [];
        foreach ($customerDiffData as $key=>$customerDiff){
            $customersTrace = createCustomersTraceItem($uid,$now_time,$table,$id,$key,$customerOldData,$customer,$updateItemName);
            $customersTraces[] = $customersTrace;
        }
        //var_exp($customersTraces,'$customersTraces',1);
        unset($customer["comm_status"]);
        try{
            $customerM->link->startTrans();

            $customersFlg = $customerM->setCustomer($id,$customer);
            /*if(!$customersFlg){
                exception('添加客户失败!');
            }*/

            $customerNegotiateM = new CustomerNegotiate($this->corp_id);
            $customersNegotiateFlg = $customerNegotiateM->updateCustomerNegotiate($id,$customerNegotiate);
            /*if(!$customersNegotiateFlg){
                exception('更新客户沟通状态失败!');
            }*/

            if(!empty($customersTraces)){
                $customerM = new CustomerTraceModel($this->corp_id);
                $customerTraceflg = $customerM->addMultipleCustomerMessage($customersTraces);
                if(!$customerTraceflg){
                    exception('提交客户跟踪数据失败!');
                }
            }

            $customerM->link->commit();
        }catch (\Exception $ex){
            $customerM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存客户信息成功！";
        return json($result);
    }
    public function update_comm_status(){
        $result = ['status'=>0 ,'info'=>"保存客户沟通结果时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        $customerNegotiateM = new CustomerNegotiate($this->corp_id);
        $customerM = new CustomerModel($this->corp_id);
        $customerOldData = $customerM->getCustomer($id);
        $customer["comm_status"] = input('comm_status',0,'int');
        $updateItemName = $this->getUpdateItemNameAndType();
        $table = 'customer_negotiate';
        $customersTraces = [];
        if($customer["comm_status"]!=$customerOldData["comm_status"]){
            $key="comm_status";
            $customersTrace = createCustomersTraceItem($uid,$now_time,$table,$id,$key,$customerOldData,$customer,$updateItemName);
            $customersTraces[] = $customersTrace;
        }
        try{
            $customerNegotiateM->link->startTrans();
            
            $customersNegotiateFlg = $customerNegotiateM->updateCustomerNegotiate($id,$customerNegotiate);
            if(!$customersNegotiateFlg){
                exception('更新客户沟通状态失败!');
            }
            
            if(!empty($customersTraces)){
                $customerM = new CustomerTraceModel($this->corp_id);
                $customerTraceflg = $customerM->addMultipleCustomerMessage($customersTraces);
                if(!$customerTraceflg){
                    exception('提交客户跟踪数据失败!');
                }
            }
            
            $customerNegotiateM->link->commit();
        }catch (\Exception $ex){
            $customerNegotiateM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存客户沟通结果成功！";
        return json($result);
    }
    public function del(){
        if((!$this->checkRule('crm/customer/customer_manage/exportCustomer'))){
            $result=$this->noRole();
            return $result;
        }
        $result = ['status'=>0 ,'info'=>"删除客户信息时发生错误！"];
        $ids = input('ids/a');
        //var_exp($ids,'$ids',1);
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        //TODO 删除权限验证
        try{
            $customerDeleteM = new CustomerDelete($this->corp_id);
            $customersDeleteFlg = $customerDeleteM->moveInDelMultipleCustomer($ids);
            if(!$customersDeleteFlg){
                exception('删除客户失败!');
            }
            //TODO add trace
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "删除客户信息成功！";
        return json($result);
    }
}