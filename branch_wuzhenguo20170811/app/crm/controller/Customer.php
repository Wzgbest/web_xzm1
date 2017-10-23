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
use app\crm\model\CustomerContact;
use app\crm\model\SaleChance;
use app\crm\model\CustomerTrace;
use app\crm\model\CustomerDelete as CustomerDelete;
use app\crm\model\CustomerNegotiate;
use app\systemsetting\model\CustomerSetting;
use app\common\model\Business;
use app\common\model\Employee as EmployeeModel;
use app\crm\model\SaleChance as SaleChanceModel;
use app\crm\model\SaleChanceVisit as SaleChanceVisitModel;
use app\crm\model\CustomerContact as CustomerContactModel;
use app\crm\model\CustomerTrace as CustomerTraceModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\common\model\ParamRemark;
use app\crm\model\CallRecord;
use app\task\model\TaskTarget;
use app\common\model\StructureEmployee;

class Customer extends Initialize{
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
            $saleChanceInfos = $saleChanceM->getNAmeAndMoneyByCustomerIds($customer_ids);
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
        $customerM = new CustomerContact($this->corp_id);
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
        $business = new Business($this->corp_id);
        $business_list = $business->getAllBusiness();
        $this->assign("business_list",$business_list);
        $businessFlowModel = new BusinessFlowModel($this->corp_id);
        $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
        $this->assign('business_flows',$business_flows);
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
        $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
        $visitData = $saleChanceVisitM->getLastVisitAndNum($info_array["id"]);
        //var_exp($visitData,'$visitData',1);
        if(empty($visitData)){
            $visitData["last_visit_time"] = "";
            $visitData["visit_num"] = "0";
        }
        $this->assign("visit_count",$visitData);
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
        $structureEmployeeModel = new StructureEmployee($this->corp_id);
        $structures = $structureEmployeeModel->getAllStructureAndEmployee();
        $structure_employee = [];
        foreach ($structures as $structure){
            $structure_employee[$structure["id"]] = explode(",",$structure["employee_ids"]);
        }
        $employM = new EmployeeModel($this->corp_id);
        $friendsInfos = $employM->getAllUsers();
        $employee_name = [];
        foreach ($friendsInfos as $friendsInfo){
            $employee_name[$friendsInfo["id"]] = $friendsInfo["nickname"];
        }
        $this->assign("structures",$structures);
        $this->assign("structure_employee",json_encode($structure_employee,true));
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
        $filter = $this->_getCustomerFilter(["take_type","grade","sale_chance","comm_status","customer_name","tracer","contact_name","in_column"]);
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
            $add_man = input("add_man");
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
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        $customerM = new CustomerModel($this->corp_id);
        $haveTel = $customerM->getCustomerByTel(['telephone'=>$customer['telephone']]);
        if (!empty($haveTel)) {
            return ['status'=>0 ,'info'=>"和其他客户手机号重复,请重新输入!"];
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
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        $customerM = new CustomerModel($this->corp_id);
        $customerOldData = $customerM->getCustomer($id);
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