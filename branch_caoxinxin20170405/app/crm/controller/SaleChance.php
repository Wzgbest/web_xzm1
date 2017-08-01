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
use app\crm\model\CustomerTrace;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\systemsetting\model\BusinessFlowItemLink;

class SaleChance extends Initialize{
    protected $_activityBusinessFlowItem = [1,2,4];
    public function __construct(){
        parent::__construct();
    }
    public function index(){
        return view();
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
            $SaleChancesVisitData = $saleChanceVisitM->getSaleChancesBySaleId($id);
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
            $saleChance['create_time'] = input('create_time',0,'int');
        }
        $saleChance['associator_id'] = input('associator_id',0,'int');

        $saleChance['sale_name'] = input('sale_name');
        $saleChance['sale_status'] = input('sale_status',1,'int');;

        $saleChance['guess_money'] = input('guess_money',0,'double');
        $saleChance['need_money'] = input('need_money',0,'double');//必填?
        $saleChance['payed_money'] = input('payed_money',0,'double');
        $saleChance['final_money'] = input('final_money',0,'double');

        $saleChance['update_time'] = time();
        $saleChance['prepay_time'] = input('prepay_time',0,'int');
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
    public function update(){
        $result = ['status'=>0 ,'info'=>"保存销售机会时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $saleChance = $this->_getSaleChanceForInput(0);
        $saleChanceM = new SaleChanceModel($this->corp_id);
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
    protected function _getSaleChanceVisitForInput($sale_id,$add_mode){
        // add sale chance visit page
        if($add_mode){
            $saleChance['create_time'] = time();
            $saleChance['visit_ok'] = 0;
        }

        $saleChance['visit_time'] = input('visit_time',0,'strtotime');
        $saleChance['visit_place'] = input('visit_place');
        $saleChance['location'] = input('location','','string');

        $saleChance['partner_notice'] = input('partner_notice',0,'int');
        $saleChance['add_note'] = input('add_note',0,'int');
        return $saleChance;
    }
    protected function _update_visit($sale_id){
        $saleChanceVisitM = new SaleChanceVisitModel($this->corp_id);
        $SaleChancesVisitOldData = $saleChanceVisitM->getSaleChancesBySaleId($sale_id);
        $add_flg = false;
        if(empty($SaleChancesVisitOldData)){
            $add_flg = true;
        }
        $SaleChancesVisitData = $this->_getSaleChanceVisitForInput($sale_id,$add_flg);
        $save_flg = false;
        if($add_flg){
            $SaleChancesVisitData["sale_id"] = $sale_id;
            $save_flg = $saleChanceVisitM->addSaleChanceVisit($SaleChancesVisitData);
        }else{
            $save_flg = $saleChanceVisitM->setSaleChanceVisit($sale_id,$SaleChancesVisitData);
        }
        return $save_flg;
    }
    protected function _getSaleChanceFineForInput($sale_id,$add_flg){
        // add sale chance page
        $saleChance['customer_id'] = input('customer_id',0,'int');
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $saleChance['employee_id'] = $uid;
        $saleChance['associator_id'] = input('associator_id',0,'int');
        $saleChance['business_id'] = input('business_id',0,'int');

        $saleChance['sale_name'] = input('sale_name');
        $saleChance['sale_status'] = input('sale_status',1,'int');;

        $saleChance['guess_money'] = input('guess_money',0,'double');
        $saleChance['need_money'] = input('need_money',0,'double');//必填?
        $saleChance['payed_money'] = input('payed_money',0,'double');
        $saleChance['final_money'] = input('final_money',0,'double');

        $saleChance['create_time'] = input('create_time',0,'int');
        $saleChance['update_time'] = input('update_time',0,'int');
        $saleChance['prepay_time'] = input('prepay_time',0,'int');
        return $saleChance;
    }
    protected function _update_fine($sale_id){
        return $sale_id;
    }
    public function invalid(){
        $result = ['status'=>0 ,'info'=>"作废销售机会时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $saleChanceflg = $saleChanceM->invalidSaleChance($id);
            $result['data'] = $saleChanceflg;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "作废销售机会成功！";
        return json($result);
    }
}