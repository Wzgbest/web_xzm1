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
use app\crm\model\SaleChance as SaleChanceModel;
use app\crm\model\SaleChanceVisit as SaleChanceVisitModel;
use app\crm\model\SaleOrderContract as SaleOrderContractModel;
use app\crm\model\CustomerTrace;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\systemsetting\model\BusinessFlowItem;
use app\systemsetting\model\BusinessFlowItemLink;
use app\common\model\RoleEmployee as RoleEmployeeModel;
use app\crm\model\Contract as ContractAppliedModel;
use app\crm\model\CustomerTrace as CustomerTraceModel;
use app\common\model\ParamRemark;
use app\crm\model\SaleOrderContractItem;
use app\task\model\TaskTarget;
use app\crm\model\SaleChanceSignIn as SaleChanceSignInModel;

class SaleChance extends Initialize{
    protected $_activityBusinessFlowItem = [1,2,4];
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
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
        $filter = $this->_getCustomerFilter(["in_column","business_id","sale_status","sale_name","customer_name"]);
        $field = $this->_getCustomerField([]);
        $filter["employee_id"] = $uid;
        try{
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $SaleChancesData = $saleChanceM->getAllSaleChancesByPage($uid,$num,$p,$filter,$field,$order,$direction);
            //var_exp($SaleChancesData,'$SaleChancesData',1);
            $this->assign("list_data",$SaleChancesData);
            $customers_count = $saleChanceM->getAllSaleChanceCount($uid,$filter);
            $this->assign("count",$customers_count);
            $listCount = $saleChanceM->getAllColumnNum($uid,$filter);
            $this->assign("listCount",$listCount);
            $businessFlowModel = new BusinessFlowModel($this->corp_id);
            $business_flow_names = $businessFlowModel->getAllBusinessFlowName();
            //var_exp($business_flow_names,'$business_flow_names',1);
            $this->assign('business_flow_names',$business_flow_names);
            $businessFlowModel = new BusinessFlowModel($this->corp_id);
            $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
            //var_exp($business_flows,'$business_flows',1);
            $this->assign('business_flows',$business_flows);
            $businessFlowItemM = new BusinessFlowItem($this->corp_id);
            $businessFlowItems = $businessFlowItemM->getAllBusinessFlowItem("id asc");
            $this->assign('business_flow_items',$businessFlowItems);
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

        //对应业务
        if(in_array("business_id", $filter_column)){
            $in_column = input("business_id",0,"int");
            if($in_column){
                $filter["business_id"] = $in_column;
            }
        }

        //业务状态
        if(in_array("sale_status", $filter_column)){
            $in_column = input("sale_status",0,"int");
            if($in_column){
                $filter["sale_status"] = $in_column;
            }
        }

        //商机名称
        if(in_array("sale_name", $filter_column)){
            $in_column = input("sale_name",'',"string");
            if($in_column){
                $filter["sale_name"] = $in_column;
            }
        }

        //客户名称
        if(in_array("customer_name", $filter_column)){
            $in_column = input("customer_name",'',"string");
            if($in_column){
                $filter["customer_name"] = $in_column;
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
    public function sale_chance_subordinate(){
        return "crm/sale_chance/sale_chance_subordinate";
    }
    protected function _showSaleChance(){
        $customer_id = input('customer_id',0,'int');
        if(!$customer_id){
            $this->error("参数错误！");
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $this->assign("customer_id",$customer_id);
        $this->assign("fr",input('fr'));
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $SaleChancesData = $saleChanceM->getAllSaleChancesByCustomerId($customer_id);
        //var_exp($SaleChancesData,'$SaleChancesData');

        $sale_id_arr = array_column($SaleChancesData,"id");
        $saleOrderContractItemM = new SaleOrderContractItem($this->corp_id);
        $saleOrderContractItemArr = $saleOrderContractItemM->getContractItemBySaleIdArr($sale_id_arr);
        $saleOrderContractItemIdx = [];
        for($key=0;$key<count($saleOrderContractItemArr);$key++){
            $saleOrderContractItem = $saleOrderContractItemArr[$key];
            $saleOrderContractItemIdx[$saleOrderContractItem["sale_id"]][] = $saleOrderContractItem;
        }
        //$this->assign("sale_contract_idx",$saleOrderContractItemIdx);
        //$this->assign("sale_contract_arr",$saleOrderContractItemArr);
        foreach ($SaleChancesData as &$SaleChances){
            if(isset($SaleChances["location"])){
                $location = explode(",",$SaleChances["location"]);
                $SaleChances["lat"] = isset($location[0])&&!empty($location[0])?$location[0]:"36.7075";
                $SaleChances["lng"] = isset($location[1])&&!empty($location[1])?$location[1]:"119.1324";
            }else{
                $SaleChances["lat"] = "36.7075";
                $SaleChances["lng"] = "119.1324";
            }
            if(isset($SaleChances["sign_in_location"])){
                $location = explode(",",$SaleChances["sign_in_location"]);
                $SaleChances["sign_in_lat"] = isset($location[0])&&!empty($location[0])?$location[0]:"36.7075";
                $SaleChances["sign_in_lng"] = isset($location[1])&&!empty($location[1])?$location[1]:"119.1324";
            }else{
                $SaleChances["sign_in_lat"] = "36.7075";
                $SaleChances["sign_in_lng"] = "119.1324";
            }
            if(isset($saleOrderContractItemIdx[$SaleChances["id"]])){
                $SaleChances["contract_arr"] = $saleOrderContractItemIdx[$SaleChances["id"]];
            }else{
                $SaleChances["contract_arr"] = [];
            }
        }
        //var_exp($SaleChancesData,'$SaleChancesData',1);
        $this->assign("sale_chance",$SaleChancesData);

        $customerContactM = new CustomerContact($this->corp_id);
        $customer_contact_num = $customerContactM->getCustomerContactCount($customer_id);
        $this->assign("customer_contact_num",$customer_contact_num);
        $sale_chance_num = $saleChanceM->getSaleChanceCount($customer_id);
        $this->assign("sale_chance_num",$sale_chance_num);
        $customerTraceM = new CustomerTrace($this->corp_id);
        $customer_trace_num = $customerTraceM->getCustomerTraceCount($customer_id);
        $this->assign("customer_trace_num",$customer_trace_num);
        $businessFlowModel = new BusinessFlowModel($this->corp_id);
        $business_flow_names = $businessFlowModel->getAllBusinessFlowName();
        //var_exp($business_flow_names,'$business_flow_names',1);
        $this->assign('business_flow_names',$business_flow_names);
    }
    public function show(){
        $this->_showSaleChance();
        return view();
    }
    public function add_page(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $truename = $userinfo["truename"];
        $this->assign("fr",input('fr'));
        $this->assign("customer_id",input('customer_id',0,"int"));
        $businessFlowModel = new BusinessFlowModel($this->corp_id);
        $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
        //var_exp($business_flows,'$business_flows',1);
        $this->assign('business_flows',$business_flows);
        $sale_chance["prepay_time"]=time();
        $this->assign('sale_chance',$sale_chance);
        $this->assign('true_name',$truename);

        $con['add_man']=array('in',array('0',$uid));
        $paramModel=new ParamRemark($this->corp_id);
        $param_list = $paramModel->getAllParam($con);//标签备注列表
        $this->assign("param_list",$param_list);

        return view();
    }
    protected function _showSaleChanceEdit(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }
        $this->assign("id",$id);
        $this->assign("fr",input('fr'));
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $SaleChancesData = $saleChanceM->getSaleChance($id);
        if(empty($SaleChancesData)){
            $this->error("未找到销售机会！");
        }
        $this->assign("sale_chance",$SaleChancesData);
        $customerM = new CustomerModel($this->corp_id);
        $customerData = $customerM->getCustomer($SaleChancesData["customer_id"]);
        $this->assign("customer",$customerData);

        $businessFlowModel = new BusinessFlowModel($this->corp_id);
        $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
        //var_exp($business_flows,'$business_flows',1);
        $this->assign('business_flows',$business_flows);
        $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
        $businessFlowItemLinks = $businessFlowItemLinkM->getItemLinkById($SaleChancesData["business_id"]);
        //var_exp($businessFlowItemLinks,'$businessFlowItemLinks');
        $this->assign('business_flow_item_links',$businessFlowItemLinks);
        $businessFlowItemLinkIndex = array_column($businessFlowItemLinks,"id");
        $this->assign('business_flow_item_link_index',$businessFlowItemLinkIndex);
        $now_item = $SaleChancesData["sale_status"];
        $next_item = 0;
        for($i=0;$i<count($businessFlowItemLinks);$i++){
            if($businessFlowItemLinks[$i]["item_id"] == $now_item){
                if($i+1<count($businessFlowItemLinks)){
                    $next_item = $businessFlowItemLinks[$i+1]["item_id"];
                    break;
                }
            }
        }
        //var_exp($businessFlowItemLinks,'$businessFlowItemLinks');
        //var_exp([$now_item,$next_item],'$now_and_next_item');
        $this->assign('now_item',$now_item);
        $this->assign('next_item',$next_item);
        $this->assign('now_and_next_item',[$now_item,$next_item]);

        $show_visit = false;
        if(
            $now_item == 2
            ||$next_item == 2
        ){
            $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
            $SaleChancesVisitData = $saleChanceVisitM->getSaleChanceVisitBySaleId($id);
            if(empty($SaleChancesVisitData)){
                $SaleChancesVisitData["visit_time"] = time();
                $SaleChancesVisitData["visit_place"] = "";
                $SaleChancesVisitData["partner_notice"] = 0;
                $SaleChancesVisitData["add_note"] = 0;
                $SaleChancesVisitData["location"] = $customerData["lat"].",".$customerData["lng"];
            }
            $location = explode(",",$SaleChancesVisitData["location"]);
            $SaleChancesVisitData["lat"] = isset($location[0])&&!empty($location[0])?$location[0]:"36.7075";
            $SaleChancesVisitData["lng"] = isset($location[1])&&!empty($location[1])?$location[1]:"119.1324";
            $this->assign('saleChancesVisitData',$SaleChancesVisitData);
            $show_visit = true;
        }
        $this->assign('show_visit',$show_visit);

        $show_fine = false;
        if(
            $now_item==4
            ||$next_item==4
        ){
            $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
            $saleOrderContractData = $saleOrderContractM->getSaleOrderContractBySaleId($id);
            $saleOrderContractItemM = new SaleOrderContractItem($this->corp_id);
            $saleOrderContractItem = [];
            $inContractId = [];
            if(empty($saleOrderContractData)){
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
            }else{
                $saleOrderContractItem = $saleOrderContractItemM->getContractItemBySaleId($id);
                $inContractId = array_column($saleOrderContractItem,"contract_id");
            }
            //var_exp($saleOrderContractItem,'$saleOrderContractItem',1);
            $this->assign('saleOrderContractData',$saleOrderContractData);
            $this->assign('saleOrderContractItem',$saleOrderContractItem);
            $businessFlowItemLink = $businessFlowItemLinkM->findItemLinkByItemId($SaleChancesData["business_id"],4);
            //var_exp($businessFlowItemLink,'$businessFlowItemLink',1);
            $this->assign('business_flow_item_link',$businessFlowItemLink);
            $role_ids = [];
            $role_ids[] = $businessFlowItemLink["handle_1"];
            $role_ids[] = $businessFlowItemLink["handle_2"];
            $role_ids[] = $businessFlowItemLink["handle_3"];
            $role_ids[] = $businessFlowItemLink["handle_4"];
            $role_ids[] = $businessFlowItemLink["handle_5"];
            $role_ids[] = $businessFlowItemLink["handle_6"];
            $role_ids = array_filter($role_ids);
            $role_ids = array_unique($role_ids);
            $role_ids = array_merge($role_ids);
            //var_exp($role_ids,'$role_ids',1);
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
            //var_exp($role_employee_index,'$role_employee_index',1);
            $this->assign('role_employee_index',$role_employee_index);

            $contract_status = [5,8];
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

            $refresh = input("refresh",0,"int");
            $this->assign('refresh',$refresh);

            $show_fine = true;
        }
        
        $this->assign('show_fine',$show_fine);
        //var_exp($this->_activityBusinessFlowItem,'$activity_business_flow_item_index',1);
        $this->assign('activity_business_flow_item_index',$this->_activityBusinessFlowItem);
    }
    public function edit_page(){
        $this->_showSaleChanceEdit();

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $con['add_man']=array('in',array('0',$uid));
        $paramModel=new ParamRemark($this->corp_id);
        $param_list = $paramModel->getAllParam($con);//标签备注列表
        $this->assign("param_list",$param_list);

        return view();
    }
    public function get_all_list(){
        $result = ['status'=>0 ,'info'=>"获取销售机会时发生错误！"];
        $last_id = input('last_id',0,'int');
        $num = input('num',10,'int');
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $SaleChancesData = $saleChanceM->getAllSaleChancesByLastId($uid,$last_id,$num);
        $result['data'] = $SaleChancesData;
        $result['status'] = 1;
        $result['info'] = "获取销售机会成功！";
        return json($result);
    }
    public function get_list(){
        $result = ['status'=>0 ,'info'=>"获取销售机会时发生错误！"];
        $customer_id = input('customer_id',0,'int');
        if(!$customer_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $last_id = input('last_id',0,'int');
        $num = input('num',10,'int');
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $SaleChancesData = $saleChanceM->getSaleChancesByLastId($customer_id,$last_id,$num);
        $result['data'] = $SaleChancesData;
        $result['status'] = 1;
        $result['info'] = "获取销售机会成功！";
        return json($result);
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取销售机会时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $saleChanceData = $saleChanceM->getSaleChance($id);
            $result['data'] = $saleChanceData;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取销售机会成功！";
        return json($result);
    }
    public function get_business_flow_name(){
        $result = ['status'=>0 ,'info'=>"获取工作流列表时发生错误！"];
        $businessFlowItemLinkModel = new BusinessFlowItemLink($this->corp_id);
        $business_flows = $businessFlowItemLinkModel->getAllBusinessFlowNameAndDefault();
        $result["status"] = 1;
        $result["info"] = "获取工作流设置成功!";
        $result["data"] = $business_flows;
        return json($result);
    }
    protected function _getSaleChanceForInput($add_mode){
        // add sale chance page
        if($add_mode){
            $saleChance['customer_id'] = input('customer_id',0,'int');
            $userinfo = get_userinfo();
            $uid = $userinfo["userid"];
            $saleChance['employee_id'] = $uid;
            $saleChance['business_id'] = input('business_id',0,'int');
            $saleChance['create_time'] = time();
        }
        $saleChance['associator_id'] = input('associator_id','','string');

        $saleChance['sale_name'] = input('sale_name',"","string");
        $saleChance['sale_status'] = input('sale_status',1,'int');

        $saleChance['guess_money'] = "".number_format(input('guess_money',0,'float'),2,".","");
        $saleChance['need_money'] = "".number_format(input('need_money',0,'float'),2,".","");
        $saleChance['payed_money'] = "".number_format(input('payed_money',0,'float'),2,".","");
        $saleChance['final_money'] = "".number_format(input('final_money',0,'float'),2,".","");

        $saleChance['update_time'] = time();
        $saleChance['prepay_time'] = input('prepay_time',0,'strtotime');
        $saleChance['remark'] = input('remark','','string');
        return $saleChance;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建销售机会时发生错误！"];
        $saleChance = $this->_getSaleChanceForInput(1);
        if(empty($saleChance["sale_name"])){
            $result['info'] = "销售机会名称不能为空!";
            return json($result);
        }
        if(!$saleChance['business_id']){
            $result['info'] = "销售机会业务不能为空!";
            return json($result);
        }
        if($saleChance["guess_money"]<=0){
            $result['info'] = "预计成单金额必须大于0!";
            return json($result);
        }
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
        $businessFlowItemLinks = $businessFlowItemLinkM->getItemLinkById($saleChance["business_id"]);
        //var_exp($businessFlowItemLinks,'$businessFlowItemLinks');
        $this->assign('business_flow_item_links',$businessFlowItemLinks);
        $businessFlowItemLinkIndex = array_column($businessFlowItemLinks,"id");
        $this->assign('business_flow_item_link_index',$businessFlowItemLinkIndex);
        $now_item = $saleChance["sale_status"];
        $next_item = 0;
        for($i=0;$i<count($businessFlowItemLinks);$i++){
            if($businessFlowItemLinks[$i]["item_id"] == $now_item){
                if($i+1<count($businessFlowItemLinks)){
                    $next_item = $businessFlowItemLinks[$i+1]["item_id"];
                    break;
                }
            }
        }
        $need_sign_num = 0;
        if($now_item==3 && $next_item==3){
            $need_sign_num = 1;
        }

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();
        $table = 'sale_chance';

        $customersTrace["add_type"] = 0;
        $customersTrace["operator_type"] = 0;
        $customersTrace["operator_id"] = $uid;
        $customersTrace["create_time"] = $now_time;
        $customersTrace["customer_id"] = $saleChance['customer_id'];
        $customersTrace["db_table_name"] = $table;
        $customersTrace["db_field_name"] = "id";
        $customersTrace["old_value"] = 0;
        $customersTrace["new_value"] = 0;
        $customersTrace["value_type"] = "";
        $customersTrace["option_name"] = "添加了";
        $customersTrace["sub_name"] = "";
        $customersTrace["item_name"] = "新商机";
        $customersTrace["from_name"] = "";
        $customersTrace["link_name"] = "";
        $customersTrace["to_name"] = $saleChance["sale_name"];
        $customersTrace["status_name"] = '';
        $customersTrace["remark"] = '';

        try{
            $saleChanceM->link->startTrans();
            //var_exp($saleChance,'$saleChance',1);
            $saleChanceId = $saleChanceM->addSaleChance($saleChance);

            if($saleChance["sale_status"]==2){
                $visit_save_flg = $this->_add_visit($saleChanceId);
                if(!$visit_save_flg){
                    //$result['info'] = "保存预约拜访信息失败！";
                    //return json($result);
                    exception("保存预约拜访信息失败!");
                }
            }
            if($need_sign_num>0){
                $sign_in_save_flg = $this->_add_sign_in($saleChanceId);
                if(!$sign_in_save_flg){
                    exception("保存下一环节上门拜访信息失败!");
                }
            }
            if($saleChance["sale_status"]==4){
                $fine_save_flg = $this->_add_fine($saleChanceId,$saleChance);
                //var_exp($fine_save_flg,'$fine_save_flg',1);
                if($fine_save_flg["status"]!=1){
                    $result['info'] = $fine_save_flg['info'];
                    return json($result);
                    //exception("保存成单申请信息失败!");
                }
                $saleChance["need_money"] = $fine_save_flg["data"]["all_contract_money"];
                $saleChance["payed_money"] = $fine_save_flg["data"]["all_pay_money"];
                $saleChance["final_money"] = $fine_save_flg["data"]["all_contract_money"];
                if(isset($fine_save_flg["data"]["sale_name"])){
                    $saleChance["sale_name"] = $fine_save_flg["data"]["sale_name"];
                    $saleChanceflg = $saleChanceM->setSaleChance($saleChanceId,$saleChance);
                }
            }

            $customerM = new CustomerTraceModel($this->corp_id);
            $customersTrace["new_value"] = $saleChanceId;
            //var_exp($customersTrace,'$customersTrace',1);
            $customerTraceflg = $customerM->addSingleCustomerMessage($customersTrace);
            if(!$customerTraceflg){
                exception('提交客户跟踪数据失败!');
            }

            //任务统计
            $taskTargetM = new TaskTarget($this->corp_id);
            $updateFlg = $taskTargetM->updateTaskTarget($uid,2,$now_time);
            //var_exp($updateFlg,'$updateFlg',1);

            $saleChanceM->link->commit();
            $result['data'] = $saleChanceId;
        }catch (\Exception $ex){
            $saleChanceM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "新建销售机会成功！";
        return json($result);
    }
    public function getUpdateItemNameAndType(){
        $itemName["sale_status"] = ["阶段","getSaleStatusName"];
        $itemName["sale_name"] = ["销售机会名称"];
        $itemName["guess_money"] = ["预期金额"];
        $itemName["prepay_time"] = ["预计成单日期"];
        $itemName["need_money"] = ["应支付金额"];
        $itemName["payed_money"] = ["已支付金额"];
        $itemName["final_money"] = ["成单金额"];
        return $itemName;
    }
    public function update(){
        $result = ['status'=>0 ,'info'=>"保存销售机会时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $saleChance = $this->_getSaleChanceForInput(0);
        $saleChanceM = new SaleChanceModel($this->corp_id);

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();
        $saleChanceOldData = $saleChanceM->getSaleChance($id);
        if(empty($saleChanceOldData)){
            $result['info'] = "未找到销售机会！";
            return json($result);
        }

        $saleChanceM = new SaleChanceModel($this->corp_id);
        $signInInfo = $saleChanceM->getNextStatusIsSignInWait($id,2);
        //var_exp($signInInfo,'$signInInfo',1);
        $need_sign_num = 0;
        if($signInInfo){
            $need_sign_num = 1;
        }

        $table = 'sale_chance';
        $customersTraces = [];
        
        try{
            $saleChanceM->link->startTrans();
            if($saleChance["sale_status"]==2){
                $visit_save_flg = $this->_update_visit($id);
                if(!$visit_save_flg){
                    //$result['info'] = "保存预约拜访信息失败！";
                    //return json($result);
                    exception("保存预约拜访信息失败!");
                }
            }
            if($need_sign_num>0){
                $sign_in_save_flg = $this->_update_sign_in($id);
                if(!$sign_in_save_flg){
                    exception("保存下一环节上门拜访信息失败!");
                }
            }
            if($saleChance["sale_status"]==4){
                $fine_save_flg = $this->_update_fine($id);
                //var_exp($fine_save_flg,'$fine_save_flg',1);
                if($fine_save_flg["status"]!=1){
                    $result['info'] = $fine_save_flg['info'];
                    return json($result);
                    //exception("保存成单申请信息失败!");
                }
                $saleChance["need_money"] = $fine_save_flg["data"]["all_contract_money"];
                $saleChance["payed_money"] = $fine_save_flg["data"]["all_pay_money"];
                $saleChance["final_money"] = $fine_save_flg["data"]["all_contract_money"];
                if(isset($fine_save_flg["data"]["sale_name"])){
                    $saleChance["sale_name"] = $fine_save_flg["data"]["sale_name"];
                }
            }
            //var_exp($saleChanceOldData,'$saleChanceOldData');
            //var_exp($saleChance,'$saleChance');
            $updateItemName = $this->getUpdateItemNameAndType();
            //var_exp($updateItemName,'$updateItemName');
            $saleChanceIntersertData = array_intersect_key($saleChanceOldData,$saleChance);
            $saleChanceIntersertData = array_intersect_key($saleChanceIntersertData,$updateItemName);
            //unset($saleChanceIntersertData["update_time"]);
            //var_exp($saleChanceIntersertData,'$saleChanceIntersertData');
            $saleChanceDiffData = array_diff_assoc($saleChanceIntersertData,$saleChance);
            //var_exp($saleChanceDiffData,'$saleChanceDiffData',1);

            foreach ($saleChanceDiffData as $key=>$saleChanceDiff){
                $customersTrace = createCustomersTraceItem(
                    $uid,
                    $now_time,
                    $table,
                    $saleChanceOldData["customer_id"],
                    $key,
                    $saleChanceOldData,
                    $saleChance,
                    $updateItemName,
                    $saleChanceOldData["sale_name"]
                );
                $customersTraces[] = $customersTrace;
            }
            //var_exp($saleChance,'$saleChance',1);
            $saleChanceflg = $saleChanceM->setSaleChance($id,$saleChance);
            
            if(!empty($customersTraces)){
                $customerTraceM = new CustomerTraceModel($this->corp_id);
                //var_exp($customersTraces,'$customersTraces');
                $customerTraceflg = $customerTraceM->addMultipleCustomerMessage($customersTraces);
                //var_exp($customerTraceflg,'$customerTraceflg',1);
                if(!$customerTraceflg){
                    exception('提交客户跟踪数据失败!');
                }
            }
            
            $saleChanceM->link->commit();
            $result['data'] = $saleChanceflg;
        }catch (\Exception $ex){
            $saleChanceM->link->rollback();
            $result['info'] = $ex->getMessage();
            print_r($ex->getTrace());
            //$result['info'] = "保存销售机会失败！";
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存销售机会成功！";
        return json($result);
    }
    protected function _getSaleChanceVisitForInput($sale_id,$add_flg){
        // add sale chance visit page
        $saleChanceVisit = [];
        if($add_flg){
            $saleChanceVisit['sale_id'] = $sale_id;
            $saleChanceVisit['create_time'] = time();
            $saleChanceVisit['visit_ok'] = 0;
        }

        $saleChanceVisit['visit_time'] = input('visit_time',0,'strtotime');
        $saleChanceVisit['visit_place'] = input('visit_place');
        $saleChanceVisit['location'] = '';
        $lat = input('lat','','string');
        $lng = input('lng','','string');
        if($lat&&$lng){
            $saleChanceVisit['location'] = $lat.','.$lng;
        }

        $saleChanceVisit['partner_notice'] = input('partner_notice',0,'int');
        $saleChanceVisit['add_note'] = input('add_note',0,'int');
        return $saleChanceVisit;
    }
    public function getUpdateVisitItemNameAndType(){
        $itemName["visit_time"] = ["拜访时间","time_format"];
        $itemName["visit_place"] = ["拜访地点"];
        $itemName["location"] = ["拜访位置坐标"];
        $itemName["partner_notice"] = ["结伴提醒","getYesNoName"];
        $itemName["add_note"] = ["添加到备忘录","getYesNoName"];
        return $itemName;
    }
    protected function _add_visit($sale_id){
        $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
        $add_flg = true;
        $SaleChancesVisitData = $this->_getSaleChanceVisitForInput($sale_id,$add_flg);
        $save_flg = $saleChanceVisitM->addSaleChanceVisit($SaleChancesVisitData);
        //var_exp($save_flg,'$save_flg',1);
        return $save_flg;
    }
    protected function _update_visit($sale_id){
        $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
        $SaleChancesVisitOldData = $saleChanceVisitM->getSaleChanceVisitBySaleId($sale_id);
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $saleChanceData = $saleChanceM->getSaleChance($SaleChancesVisitOldData["sale_id"]);
        $add_flg = false;
        if(empty($SaleChancesVisitOldData)){
            $add_flg = true;
        }
        $SaleChancesVisitData = $this->_getSaleChanceVisitForInput($sale_id,$add_flg);
        //var_exp($sale_id,'$sale_id');
        //var_exp($SaleChancesVisitOldData,'$SaleChancesVisitOldData');
        //var_exp($SaleChancesVisitData,'$SaleChancesVisitData');
        //var_exp($add_flg,'$add_flg');
        $save_flg = false;
        if($add_flg){
            $save_flg = $saleChanceVisitM->addSaleChanceVisit($SaleChancesVisitData);
        }else{
            //var_exp(1,'up');
            $save_flg = $saleChanceVisitM->setSaleChanceVisitBySaleId($sale_id,$SaleChancesVisitData);

            $userinfo = get_userinfo();
            $uid = $userinfo["userid"];
            $now_time = time();
            //var_exp($SaleChancesVisitOldData,'$SaleChancesVisitOldData');
            //var_exp($SaleChancesVisitData,'$SaleChancesVisitData');
            $updateItemName = $this->getUpdateVisitItemNameAndType();
            //var_exp($updateItemName,'$updateItemName');
            $SaleChancesVisitDataIntersertData = array_intersect_key($SaleChancesVisitOldData,$SaleChancesVisitData);
            $SaleChancesVisitDataIntersertData = array_intersect_key($SaleChancesVisitDataIntersertData,$updateItemName);
            //unset($SaleChancesVisitDataIntersertData["update_time"]);
            //var_exp($SaleChancesVisitDataIntersertData,'$SaleChancesVisitDataIntersertData');
            $SaleChancesVisitDataDiffData = array_diff_assoc($SaleChancesVisitDataIntersertData,$SaleChancesVisitData);
            //var_exp($SaleChancesVisitDataDiffData,'$SaleChancesVisitDataDiffData',1);
            $table = 'sale_chance';
            $customersTraces = [];
            foreach ($SaleChancesVisitDataDiffData as $key=>$SaleChancesVisitDataDiff){
                $customersTrace = createCustomersTraceItem(
                    $uid,
                    $now_time,
                    $table,
                    $saleChanceData["customer_id"],
                    $key,
                    $SaleChancesVisitOldData,
                    $SaleChancesVisitData,
                    $updateItemName,
                    $saleChanceData["sale_name"]
                );
                $customersTraces[] = $customersTrace;
            }
            //var_exp($customersTraces,'$customersTraces',1);

            if(!empty($customersTraces)){
                $customerTraceM = new CustomerTraceModel($this->corp_id);
                //var_exp($customersTraces,'$customersTraces');
                $customerTraceflg = $customerTraceM->addMultipleCustomerMessage($customersTraces);
                //var_exp($customerTraceflg,'$customerTraceflg',1);
                if(!$customerTraceflg){
                    exception('提交客户跟踪数据失败!');
                }
            }

            $save_flg = true;
        }
        //var_exp($save_flg,'$save_flg',1);
        return $save_flg;
    }
    protected function _add_sign_in($sale_id){
        $save_flg = false;
        $saleChanceSignInM = new SaleChanceSignInModel($this->corp_id);
        $data["sale_id"] = $sale_id;
        $data["sign_in_ok"] = 0;
        $save_flg = $saleChanceSignInM->addSaleChanceSignIn($data);
        return $save_flg;
    }
    protected function _update_sign_in($sale_id){
        $save_flg = false;
        $saleChanceSignInM = new SaleChanceSignInModel($this->corp_id);
        $signInInfo = $saleChanceSignInM->getSaleChanceSignInBySaleId($sale_id);
        //var_exp($signInInfo,'$signInInfo',1);
        if(!$signInInfo){
            $save_flg = $this->_add_sign_in($sale_id);
        }else{
            $save_flg = true;
        }
        return $save_flg;
    }
    protected function _getSaleChanceFineForInput($sale_id,$add_flg,$update_flg){
        // add sale chance page
        $saleOrderFine = [];
        if($add_flg){
            $saleOrderFine["sale_id"] = $sale_id;
        }
        if($add_flg||$update_flg){
            $saleOrderFine["create_time"] = time();
            $saleOrderFine["status"] = 0;
            $saleOrderFine["handle_status"] = 1;
            $saleOrderFine['handle_now'] = input('handle_1','','string');
        }

        $saleOrderFine['prod_desc'] = input('prod_desc');

        $saleOrderFine['handle_1'] = input('handle_1','','string');
        $saleOrderFine['handle_2'] = input('handle_2','','string');
        $saleOrderFine['handle_3'] = input('handle_3','','string');
        $saleOrderFine['handle_4'] = input('handle_4','','string');
        $saleOrderFine['handle_5'] = input('handle_5','','string');
        $saleOrderFine['handle_6'] = input('handle_6','','string');

        $saleOrderFine["update_time"] = time();
        return $saleOrderFine;
    }
    protected function _getSaleContractsForInput($sale_id,$sale_order_id){
        $result = ['status'=>0 ,'info'=>"获取合同信息时发生错误！"];
        $contracts = [];
        $contract_str = input('contracts');
        $contract_arr = json_decode($contract_str,true);
        //var_exp($contract_arr,'$contract_arr');
        $contract_id_arr = [];
        foreach ($contract_arr as $contract_item){
            $contract_id = intval($contract_item["contract_id"]);
            if($contract_id<=0){
                $result["info"] = "合同未选择!";
                return $result;
            }
            if(in_array($contract_id,$contract_id_arr)){
                $result["info"] = "合同重复选择!";
                return $result;
            }
            $contract["sale_id"] = $sale_id;
            $contract["sale_order_id"] = $sale_order_id;
            $contract["contract_id"] = $contract_id;
            $contract["contract_money"] = "".number_format($contract_item["contract_money"],2,".","");
            $contract["pay_money"] = "".number_format($contract_item["pay_money"],2,".","");
            $contract["pay_name"] = filter_var($contract_item["pay_name"], filter_id("string"));
            $contract["pay_type"] = intval($contract_item["pay_type"]);
            $contract["due_time"] = strtotime($contract_item["due_time"]);
            $contract["need_bill"] = intval($contract_item["need_bill"]);
            $contract["pay_bank"] = isset($contract_item["pay_bank"])?filter_var($contract_item["pay_bank"], filter_id("string")):"";
            $contracts[] = $contract;
            $contract_id_arr[] = $contract_item["contract_id"];
        }
        $result["status"] = 1;
        $result["info"] = "获取合同信息成功!";
        $result["data"] = $contracts;
        return $result;
    }
    public function getUpdateFineItemNameAndType(){
        $itemName["prod_desc"] = ["产品描述"];
        return $itemName;
    }
    public function getUpdateContractItemNameAndType(){
        $itemName["contract_money"] = ["成单金额"];
        $itemName["pay_money"] = ["打款金额"];
        $itemName["pay_type"] = ["付款方式","getPayTypeName"];
        $itemName["pay_name"] = ["打款名称"];
        $itemName["due_time"] = ["预计合同到期时间","day_format"];
        $itemName["need_bill"] = ["需要发票","getYesNoName"];
        return $itemName;
    }
    protected function _add_fine($sale_id,$saleChanceData){
        $result = ['status'=>0 ,'info'=>"保存成单申请时发生错误！"];
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContractItemM = new SaleOrderContractItem($this->corp_id);
        $add_flg = true;
        $update_flg = false;
        $sale_order_id = 0;
        $sale_order_contract_idx = [];
        $saleOrderContractData = $this->_getSaleChanceFineForInput($sale_id,$add_flg,$update_flg);
        //var_exp($saleOrderContractData,'$saleOrderContractData',1);
        $saleContracts = $this->_getSaleContractsForInput($sale_id,$sale_order_id);
        if($saleContracts["status"]!=1){
            $result["info"] = $saleContracts["info"];
            return $result;
        }
        //var_exp($saleContracts,'$saleContracts',1);
        $result_data = [];
        if(
            empty($saleOrderContractData["handle_1"])
        ){
            exception("审核人必须选择!");
        }
        for($i=2;$i<=6;$i++){
            if(
                !empty($saleOrderContractData["handle_".$i])
                && empty($saleOrderContractData["handle_".($i-1)])
            ){
                exception("审核人".($i-1)."不能为空!");
            }
        }

        $saleOrderContractData["order_num"] = 1;
        $save_flg = $saleOrderContractM->addSaleOrderContract($saleOrderContractData);
        if(!$save_flg){
            $result["info"] = "成单申请保存失败!";
            return $result;
        }
        $sale_order_contract_idx[0] = [$sale_id,$save_flg];

        if(count($saleContracts["data"])>1){
            //申请时多个合同拆分,复制销售机会
            $saleContractIds = array_column($saleContracts["data"],"contract_id");
            //var_exp($saleContractIds,'$saleContractIds');
            $contractAppliedModel = new ContractAppliedModel($this->corp_id);
            $saleContractNames = $contractAppliedModel->getContractNoAndTypeInfos($saleContractIds);
            //var_exp($saleContractNames,'$saleContractNames',1);
            $saleContractNameIdx = [];
            foreach ($saleContractNames as $saleContractName){
                $saleContractNameIdx[$saleContractName["id"]] = $saleContractName["contract_name"];
            }
            //var_exp($saleContractNameIdx,'$saleContractNameIdx');

            $sale_name = input('sale_name',"","string");
            $contract_name = $saleContractNameIdx[$saleContracts["data"][0]["contract_id"]];
            $result_data["sale_name"] = $sale_name." - 1:".$contract_name;
//                $saleChanceNameUpdate["sale_name"] = $result_data["sale_name"];
//                $saleChanceflg = $saleChanceM->setSaleChance($sale_id,$saleChanceNameUpdate);
//                if(!$saleChanceflg){
//                    $result["info"] = "成单申请拆分更新销售机会名称失败!";
//                    return $result;
//                }

            //$saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
            //$SaleChancesVisitData = $saleChanceVisitM->getSaleChanceVisitBySaleId($sale_id);

            for ($i=1;$i<count($saleContracts["data"]);$i++){
                $saleChanceTmp = $saleChanceData;
                unset($saleChanceTmp["id"]);
                //unset($saleChanceTmp["employee_name"]);
                //var_exp($saleContracts["data"][$i],'$saleContracts["data"][$i]');
                $contract_name = $saleContractNameIdx[$saleContracts["data"][$i]["contract_id"]];
                $saleChanceTmp["sale_name"] = $sale_name." - ".($i+1).":".$contract_name;
                $saleChanceTmp["pid"] = $sale_id;
                $saleChanceTmp["is_copy"] = 1;
                $saleChanceTmp["sale_status"] = 4;
                $saleChanceTmp["need_money"] = $saleContracts["data"][$i]["contract_money"];
                $saleChanceTmp["payed_money"] = $saleContracts["data"][$i]["pay_money"];
                $saleChanceTmp["final_money"] = $saleContracts["data"][$i]["contract_money"];
                $saleChanceAddFlg = $saleChanceM->addSaleChance($saleChanceTmp);
                if(!$saleChanceAddFlg){
                    $result["info"] = "成单申请拆分销售机会失败!";
                    return $result;
                }

                $saleOrderContractData["sale_id"] = $saleChanceAddFlg;
                $saleOrderContractData["order_num"] = 1;
                $save_flg = $saleOrderContractM->addSaleOrderContract($saleOrderContractData);
                if(!$save_flg){
                    $result["info"] = "成单申请拆分数据保存失败!";
                    return $result;
                }
                $sale_order_contract_idx[$i] = [$saleChanceAddFlg,$save_flg];

//                    if(empty($SaleChancesVisitData)){
//                        continue;
//                    }
//                    $SaleChancesVisitTmp = $SaleChancesVisitData;
//                    unset($saleChanceTmp["id"]);
//                    $SaleChancesVisitTmp["sale_id"] = $saleChanceAddFlg;
//                    $SaleChancesVisitFlg = $saleChanceVisitM->addSaleChanceVisit($SaleChancesVisitTmp);
//                    if(!$SaleChancesVisitFlg){
//                        $result["info"] = "成单申请拆分销售机会拜访数据保存失败!";
//                        return $result;
//                    }
            }
        }

        $saleContractsIdx = [];
        $all_contract_money = 0;
        $all_pay_money = 0;
        for($key=0;$key<count($saleContracts["data"]);$key++){
            $saleContractItem = $saleContracts["data"][$key];
            $saleContractsIdx[$saleContractItem["contract_id"]]=$key;
            $all_contract_money += $saleContractItem["contract_money"];
            $all_pay_money += $saleContractItem["pay_money"];
        }
        //var_exp($saleContractsIdx,'$saleContractsIdx');
        $result_data["all_contract_money"] = "".number_format($all_contract_money,2,".","");
        $result_data["all_pay_money"] = "".number_format($all_pay_money,2,".","");

        $item_infos = [];
        foreach ($saleContractsIdx as $contract_id=>$idx){
            $new_item_info = $saleContracts["data"][$saleContractsIdx[$contract_id]];
            $new_item_info["sale_id"] = $sale_order_contract_idx[$idx][0];
            $new_item_info["sale_order_id"] = $sale_order_contract_idx[$idx][1];
            $item_infos[] = $new_item_info;
        }
        $saleOrderContractItemAddFlg = $saleOrderContractItemM->addMultipleContractItem($item_infos);
        if(!$saleOrderContractItemAddFlg){
            $result["info"] = "成单申请合同关联添加失败!";
            return $result;
        }

        $result["status"] = 1;
        $result["info"] = "成单申请保存成功!";
        $result["data"] = $result_data;
        return $result;
    }
    protected function _update_fine($sale_id){
        $result = ['status'=>0 ,'info'=>"保存成单申请时发生错误！"];
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $saleChanceData = $saleChanceM->getSaleChanceOrigin($sale_id);
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContractOldData = $saleOrderContractM->getSaleOrderContractBySaleId($sale_id);
        //var_exp($saleOrderContractOldData,'$saleOrderContractOldData',1);
        $saleOrderContractItemM = new SaleOrderContractItem($this->corp_id);
        $add_flg = false;
        $update_flg = false;
        if(empty($saleOrderContractOldData)){
            return $this->_add_fine($sale_id,$saleChanceData);
        }
            $sale_order_id = $saleOrderContractOldData["id"];
            $saleOrderContractItemOld = $saleOrderContractItemM->getContractItemBySaleId($sale_id);

        //var_exp($saleOrderContractItemOld,'$saleOrderContractItemOld');
        $refresh = input("refresh",0,"int");
        $reply = $saleOrderContractOldData["status"]==3;
        if($refresh||$reply){
            $update_flg = true;
        }
        $saleOrderContractData = $this->_getSaleChanceFineForInput($sale_id,$add_flg,$update_flg);
        //var_exp($saleOrderContractData,'$saleOrderContractData',1);
        $saleContracts = $this->_getSaleContractsForInput($sale_id,$sale_order_id);
        if($saleContracts["status"]!=1){
            $result["info"] = $saleContracts["info"];
            return $result;
        }
        $order_num = count($saleContracts["data"]);
        $can_update_contract = count($saleOrderContractOldData)?:1;
        if($order_num>$can_update_contract){
            $result["info"] = "只有第一次提交成单申请时能提交多个合同!";
            return $result;
        }
        //var_exp($saleContracts,'$saleContracts',1);
        $result_data = [];
        if(!in_array($saleOrderContractOldData["status"],[2,3])){
            $result["info"] = "成单申请审核中,不能编辑!";
            return $result;
        }
        if(
            empty($saleOrderContractData["handle_1"])
        ){
            exception("审核人必须选择!");
        }
        for($i=2;$i<=6;$i++){
            if(
                !empty($saleOrderContractData["handle_".$i])
                && empty($saleOrderContractData["handle_".($i-1)])
            ){
                exception("审核人".($i-1)."不能为空!");
            }
        }

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();
        $customersTraces = [];
        $table = 'sale_chance';

        $save_flg = $saleOrderContractM->setSaleOrderContractBySaleId($sale_id,$saleOrderContractData);

        //var_exp($saleOrderContractOldData,'$saleOrderContractOldData');
        //var_exp($saleOrderContractData,'$saleOrderContractData');
        $updateItemName = $this->getUpdateFineItemNameAndType();
        //var_exp($updateItemName,'$updateItemName');
        $saleOrderContractDataIntersertData = array_intersect_key($saleOrderContractOldData,$saleOrderContractData);
        $saleOrderContractDataIntersertData = array_intersect_key($saleOrderContractDataIntersertData,$updateItemName);
        //unset($saleOrderContractDataIntersertData["update_time"]);
        //var_exp($saleOrderContractDataIntersertData,'$saleOrderContractDataIntersertData');
        $saleOrderContractDataDiffData = array_diff_assoc($saleOrderContractDataIntersertData,$saleOrderContractData);
        //var_exp($saleOrderContractDataDiffData,'$saleOrderContractDataDiffData',1);


        foreach ($saleOrderContractDataDiffData as $key=>$saleOrderContractDataDiff){
            $customersTrace = createCustomersTraceItem(
                $uid,
                $now_time,
                $table,
                $saleChanceData["customer_id"],
                $key,
                $saleOrderContractOldData,
                $saleOrderContractData,
                $updateItemName,
                $saleChanceData["sale_name"]
            );
            $customersTraces[] = $customersTrace;
        }

        //empty($saleOrderContractData["contract_id"])||
        $saleOrderContractItemIdx = [];
        for($key=0;$key<count($saleOrderContractItemOld);$key++){
            $saleOrderContractItem = $saleOrderContractItemOld[$key];
            $saleOrderContractItemIdx[$saleOrderContractItem["contract_id"]]=$key;
        }
        //var_exp($saleOrderContractItemIdx,'$saleOrderContractItemIdx');
        $saleContractsIdx = [];
        $all_contract_money = 0;
        $all_pay_money = 0;
        for($key=0;$key<count($saleContracts["data"]);$key++){
            $saleContractItem = $saleContracts["data"][$key];
            $saleContractsIdx[$saleContractItem["contract_id"]]=$key;
            $all_contract_money += $saleContractItem["contract_money"];
            $all_pay_money += $saleContractItem["pay_money"];
        }
        //var_exp($saleContractsIdx,'$saleContractsIdx');
        $result_data["all_contract_money"] = "".number_format($all_contract_money,2,".","");
        $result_data["all_pay_money"] = "".number_format($all_pay_money,2,".","");
        $updateContractsIdx = array_intersect_key($saleOrderContractItemIdx,$saleContractsIdx);
        $deleteContractsIdx = array_diff_key($saleOrderContractItemIdx,$updateContractsIdx);
        $insaertContractsIdx = array_diff_key($saleContractsIdx,$updateContractsIdx);
        //var_exp($updateContractsIdx,'$updateContractsIdx');
        //var_exp($deleteContractsIdx,'$deleteContractsIdx');
        //var_exp($insaertContractsIdx,'$insaertContractsIdx');
        if(!empty($deleteContractsIdx)){
            $item_ids = [];
            foreach ($deleteContractsIdx as $contract_id=>$idx){
                $id = $saleOrderContractItemOld[$saleOrderContractItemIdx[$contract_id]]["id"];
                $item_ids[] = $id;
            }
            //var_exp($item_ids,'$item_ids');
            $saleOrderContractItemDelFlg = $saleOrderContractItemM->delContractItem($item_ids);
            if(!$saleOrderContractItemDelFlg){
                $result["info"] = "成单申请合同关联删除失败!";
                return $result;
            }
        }
        if(!empty($updateContractsIdx)){
            foreach ($updateContractsIdx as $contract_id=>$idx){
                //var_exp($saleOrderContractItemIdx[$contract_id],'$saleOrderContractItemIdx[$contract_id]');
                $old_item_info = $saleOrderContractItemOld[$saleOrderContractItemIdx[$contract_id]];
                $id = $old_item_info["id"];
                $new_item_info = $saleContracts["data"][$saleContractsIdx[$contract_id]];
                $new_item_info["sale_order_id"] = $sale_order_id;
                //var_exp($id,'$id');
                //var_exp($new_item_info,'$new_item_info');
                $saleOrderContractItemUpdateFlg = $saleOrderContractItemM->setContractItem($id,$new_item_info);

                //var_exp($old_item_info,'$old_item_info');
                //var_exp($new_item_info,'$new_item_info');
                $updateItemName = $this->getUpdateContractItemNameAndType();
                //var_exp($updateItemName,'$updateItemName');
                $saleContractIntersertData = array_intersect_key($old_item_info,$new_item_info);
                $saleContractIntersertData = array_intersect_key($saleContractIntersertData,$updateItemName);
                //unset($saleContractIntersertData["update_time"]);
                //var_exp($saleContractIntersertData,'$saleContractIntersertData');
                $saleContractDiffData = array_diff_assoc($saleContractIntersertData,$new_item_info);
                //var_exp($saleContractDiffData,'$saleContractDiffData',1);


                foreach ($saleContractDiffData as $key=>$saleContractDiff){
                    $customersTrace = createCustomersTraceItem(
                        $uid,
                        $now_time,
                        $table,
                        $saleChanceData["customer_id"],
                        $key,
                        $old_item_info,
                        $new_item_info,
                        $updateItemName,
                        $saleChanceData["sale_name"]
                    );
                    $customersTraces[] = $customersTrace;
                }
            }
        }
        if(!empty($insaertContractsIdx)){
            $item_infos = [];
            foreach ($insaertContractsIdx as $contract_id=>$idx){
                $new_item_info = $saleContracts["data"][$saleContractsIdx[$contract_id]];
                $new_item_info["sale_order_id"] = $sale_order_id;
                $item_infos[] = $new_item_info;
            }
            $saleOrderContractItemAddFlg = $saleOrderContractItemM->addMultipleContractItem($item_infos);
            if(!$saleOrderContractItemAddFlg){
                $result["info"] = "成单申请合同关联添加失败!";
                return $result;
            }
        }

        //var_exp($customersTraces,'$customersTraces',1);
        if(!empty($customersTraces)){
            $customerTraceM = new CustomerTraceModel($this->corp_id);
            //var_exp($customersTraces,'$customersTraces');
            $customerTraceflg = $customerTraceM->addMultipleCustomerMessage($customersTraces);
            //var_exp($customerTraceflg,'$customerTraceflg',1);
            if(!$customerTraceflg){
                exception('提交客户跟踪数据失败!');
            }
        }
        //var_exp($sale_order_id,'$sale_order_id');
        $result["status"] = 1;
        $result["info"] = "成单申请保存成功!";
        $result["data"] = $result_data;
        return $result;
    }
    public function sign_in_page(){
        $saleChanceSignInM = new SaleChanceSignInModel($this->corp_id);
        $saleChanseSignInWaitList = $saleChanceSignInM->getAllSignInWait();
        //var_exp($saleChanseSignInWaitList,'$saleChanseSignInWaitList',1);
        $this->assign('wait_list',$saleChanseSignInWaitList);
        return view();
    }
    public function sign_in(){
        $result = ['status'=>0 ,'info'=>"签到时发生错误！"];
        $customer_id = input('customer_id',0,'int');
        if(!$customer_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $sale_id = input('sale_id',0,'int');
        $lat = "".number_format(input('lat',0,'float'),6,".","");
        $lng = "".number_format(input('lng',0,'float'),6,".","");
        $location_str = input('location_str',"",'string');
        $saleChanceSignInM = new SaleChanceSignInModel($this->corp_id);
        $time = time();
        $customerM = new CustomerModel($this->corp_id);
        $customerData = $customerM->getCustomerAndHaveVisit($customer_id,$sale_id);
        $need_sign_num = 0;
        if($customerData){
            $need_sign_num = $customerData["need_sign_num"];
        }
        if($need_sign_num==0){
            $result['info'] = "没有需要签到的销售机会！";
            return json($result);
        }
        try{
            $saleChanceSignInM->link->startTrans();
            $signInflg = $saleChanceSignInM->sign_in($customer_id,$time,$lat,$lng,$location_str,$sale_id);
            //var_exp($signInflg,'$signInflg',1);
            if(!$signInflg){
                exception("签到失败!");
            }

            //签到后更新到下一步
            $sale_ids = $sale_id?:explode(",",$customerData["sale_id"]);
            $saleChanceflg = $saleChanceSignInM->changeToSignInStatus($customer_id,$sale_ids);
            //var_exp($saleChanceflg,'$saleChanceflg',1);
            if(!$saleChanceflg){
                exception("更新签到状态失败!");
            }
            
            $saleChanceSignInM->link->commit();
            $result['data'] = $saleChanceflg;
        }catch (\Exception $ex){
            $saleChanceSignInM->link->rollback();
            $result['info'] = $ex->getMessage();
            //$result['info'] = "保存销售机会失败！";
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "签到成功！";
        return json($result);
    }
    public function retract(){
        $result = ['status'=>0 ,'info'=>"撤回成单申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $saleChanceM = new SaleOrderContractModel($this->corp_id);
        $update_flg = $saleChanceM->retractSaleOrderContract($id,$uid);
        if(!$update_flg){
            $result['info'] = "撤回成单申请失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='撤回成单申请成功!';
        return $result;
    }
    public function invalid(){
        $result = ['status'=>0 ,'info'=>"作废销售机会时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        try{
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $saleChanceflg = $saleChanceM->invalidSaleChance($id,$uid);
            $result['data'] = $saleChanceflg;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "作废销售机会成功！";
        return json($result);
    }
    public function abandoned(){
        $result = ['status'=>0 ,'info'=>"输单销售机会时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        try{
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $saleChanceflg = $saleChanceM->abandonedSaleOrderContract($id,$uid);
            $result['data'] = $saleChanceflg;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "输单销售机会成功！";
        return json($result);
    }
}