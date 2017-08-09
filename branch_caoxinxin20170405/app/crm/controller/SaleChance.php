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
        $filter = $this->_getCustomerFilter(["in_column"]);
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
        $this->assign("fr",input('fr'));
        $this->assign("customer_id",input('customer_id',0,"int"));
        $businessFlowModel = new BusinessFlowModel($this->corp_id);
        $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
        //var_exp($business_flows,'$business_flows',1);
        $this->assign('business_flows',$business_flows);
        $sale_chance["prepay_time"]=time();
        $this->assign('sale_chance',$sale_chance);
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
        $this->assign("sale_chance",$SaleChancesData);

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
        //var_exp($now_and_next_item,'$now_and_next_item',1);
        $this->assign('now_item',$now_item);
        $this->assign('next_item',$next_item);
        $this->assign('now_and_next_item',[$now_item,$next_item]);

        $show_visit = false;
        if(
            in_array($now_item,[2,3])
            ||in_array($next_item,[2,3])
        ){
            $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
            $SaleChancesVisitData = $saleChanceVisitM->getSaleChanceVisitBySaleId($id);
            if(empty($SaleChancesVisitData)){
                $SaleChancesVisitData["visit_time"] = time();
                $SaleChancesVisitData["visit_place"] = "";
                $SaleChancesVisitData["partner_notice"] = 0;
                $SaleChancesVisitData["add_note"] = 0;
            }
            $this->assign('saleChancesVisitData',$SaleChancesVisitData);
            $show_visit = true;
        }
        $this->assign('show_visit',$show_visit);

        $show_fine = false;
        if(
            in_array($now_item,[4,5,6,8])
            ||in_array($next_item,[4,5,6,8])
        ){
            $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
            $saleOrderContractData = $saleOrderContractM->getSaleOrderContractBySaleId($id);
            if(empty($saleOrderContractData)){
                $saleOrderContractData["contract_id"] = 0;
                $saleOrderContractData["order_money"] = 0.00;
                $saleOrderContractData["pay_money"] = 0.00;
                $saleOrderContractData["pay_type"] = 0;
                $saleOrderContractData["pay_name"] = '';
                $saleOrderContractData["due_time"] = time();
                $saleOrderContractData["need_bill"] = 0;
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
            }
            $this->assign('saleOrderContractData',$saleOrderContractData);
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

            $status = [5,7,8];
            $contractAppliedModel = new ContractAppliedModel($this->corp_id);
            $contracts = $contractAppliedModel->getAllContractNoAndType($uid,null,$status);
            //var_exp($contracts,'$contracts',1);
            $this->assign('contract_list',$contracts);
            $contract_type_index = [];
            foreach($contracts as $contract){
                $contract_type_index[$contract["id"]] = $contract["contract_type_name"];
            }
            $this->assign('contract_type_name_json',json_encode($contract_type_index,true));

            $refresh = input("refresh",0,"int");
            $this->assign('refresh',$refresh);

            $show_fine = true;
        }
        
        $this->assign('show_fine',$show_fine);
        //var_exp($this->_activityBusinessFlowItem,'$activity_business_flow_item_index');
        $this->assign('activity_business_flow_item_index',$this->_activityBusinessFlowItem);
    }
    public function edit_page(){
        $this->_showSaleChanceEdit();
        return view();
    }
    public function get_all_list(){
        $result = ['status'=>0 ,'info'=>"获取销售机会时发生错误！"];
        $last_id = input('last_id',0,'int');
        $num = input('num',10,'int');
        $saleChanceM = new SaleChanceModel($this->corp_id);
        $SaleChancesData = $saleChanceM->getAllSaleChancesByLastId($last_id,$num);
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
        $saleChance['associator_id'] = input('associator_id',0,'int');

        $saleChance['sale_name'] = input('sale_name');
        $saleChance['sale_status'] = input('sale_status',1,'int');;

        $saleChance['guess_money'] = "".number_format(input('guess_money',0,'float'),2);
        $saleChance['need_money'] = "".number_format(input('need_money',0,'float'),2);
        $saleChance['payed_money'] = "".number_format(input('payed_money',0,'float'),2);
        $saleChance['final_money'] = "".number_format(input('final_money',0,'float'),2);

        $saleChance['update_time'] = time();
        $saleChance['prepay_time'] = input('prepay_time',0,'strtotime');
        $saleChance['remark'] = input('remark','','string');
        return $saleChance;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建销售机会时发生错误！"];
        $saleChance = $this->_getSaleChanceForInput(1);
        try{
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $saleChanceId = $saleChanceM->addSaleChance($saleChance);
            $result['data'] = $saleChanceId;
        }catch (\Exception $ex){
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
        $table = 'sale_chance';
        $customersTraces = [];
        foreach ($saleChanceDiffData as $key=>$saleChanceDiff){
            $customersTrace = createCustomersTraceItem($uid,$now_time,$table,$saleChanceOldData["customer_id"],$key,$saleChanceOldData,$saleChance,$updateItemName);
            $customersTraces[] = $customersTrace;
        }
        //var_exp($customersTraces,'$customersTraces',1);
        
        try{
            $saleChanceM->link->startTrans();
            $saleChanceflg = $saleChanceM->setSaleChance($id,$saleChance);
            if($saleChance["sale_status"]==2){
                $visit_save_flg = $this->_update_visit($id);
                if(!$visit_save_flg){
                    //$result['info'] = "保存预约拜访信息失败！";
                    //return json($result);
                    exception("保存预约拜访信息失败!");
                }
            }
            if($saleChance["sale_status"]==4){
                $fine_save_flg = $this->_update_fine($id);
                if(!$fine_save_flg){
                    //$result['info'] = "保存成单拜访信息失败！";
                    //return json($result);
                    exception("保存成单申请信息失败!");
                }
            }
            
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
        $saleChanceVisit['location'] = input('location','','string');

        $saleChanceVisit['partner_notice'] = input('partner_notice',0,'int');
        $saleChanceVisit['add_note'] = input('add_note',0,'int');
        return $saleChanceVisit;
    }
    protected function _update_visit($sale_id){
        $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
        $SaleChancesVisitOldData = $saleChanceVisitM->getSaleChanceVisitBySaleId($sale_id);
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
            $save_flg = true;
        }
        //var_exp($save_flg,'$save_flg',1);
        return $save_flg;
    }
    protected function _getSaleChanceFineForInput($sale_id,$add_flg){
        // add sale chance page
        $saleOrderFine = [];
        if($add_flg){
            $saleOrderFine["sale_id"] = $sale_id;
            $saleOrderFine["create_time"] = time();
            $saleOrderFine["status"] = 0;
            $saleOrderFine["handle_status"] = 1;
            $saleOrderFine['handle_now'] = input('handle_1',0,'int');
        }
        $saleOrderFine['contract_id'] = input('contract_id',0,'int');
        $saleOrderFine['order_money'] = input('order_money',0,'float');
        $saleOrderFine['pay_money'] = input('pay_money',0,'float');
        $saleOrderFine['pay_type'] = input('pay_type',0,'int');
        $saleOrderFine['pay_name'] = input('pay_name');
        $saleOrderFine['due_time'] = input('due_time',0,'strtotime');
        $saleOrderFine['need_bill'] = input('need_bill',0,'int');
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
    protected function _update_fine($sale_id){
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContractOldData = $saleOrderContractM->getSaleOrderContractBySaleId($sale_id);
        $add_flg = false;
        $update_all_flg = false;
        if(empty($saleOrderContractOldData)){
            $add_flg = true;
            $saleOrderContractOldData["status"] = 3;
        }
        $refresh = input("refresh",0,"int");
        $reply = $saleOrderContractOldData["status"]==3;
        if($add_flg||$refresh||$reply){
            $update_all_flg = true;
        }
        $saleOrderContractData = $this->_getSaleChanceFineForInput($sale_id,$update_all_flg);
        //var_exp($saleOrderContractData,'$saleOrderContractData',1);
        $save_flg = false;
        if(!in_array($saleOrderContractOldData["status"],[2,3])){
            return $save_flg;
        }
        if(
            empty($saleOrderContractData["contract_id"])||
            empty($saleOrderContractData["handle_1"])
        ){
            return $save_flg;
        }
        if($add_flg){
            $save_flg = $saleOrderContractM->addSaleOrderContract($saleOrderContractData);
        }else{
            $save_flg = $saleOrderContractM->setSaleOrderContractBySaleId($sale_id,$saleOrderContractData);
        }
        return $save_flg;
    }
    public function sign_in_page(){
        $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
        $saleChanseVisitWaitList = $saleChanceVisitM->getAllSaleChanceVisitWait();
        //var_exp($saleChanseVisitWaitList,'$saleChanseVisitWaitList',1);
        $this->assign('wait_list',$saleChanseVisitWaitList);
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
        $lat = "".number_format(input('lat',0,'float'),6);
        $lng = "".number_format(input('lng',0,'float'),6);
        $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
        try{
            $saleChanceVisitM->link->startTrans();
            $saleChanceflg = $saleChanceVisitM->sign_in($customer_id,$lat,$lng,$sale_id);
            if(!$saleChanceflg){
                exception("签到失败!");
            }
            $saleChanceVisitM->link->commit();
            $result['data'] = $saleChanceflg;
        }catch (\Exception $ex){
            $saleChanceVisitM->link->rollback();
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