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
use app\crm\model\CustomerTrace;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\systemsetting\model\BusinessFlowItem;
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
        $business_flows = $businessFlowModel->getAllBusinessFlowByuserId($uid);
        //var_exp($business_flows,'$business_flows',1);
        $this->assign('business_flows',$business_flows);
        $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
        $businessFlowItemLinks = $businessFlowItemLinkM->getItemLinkById($SaleChancesData["business_id"]);
        //var_exp($businessFlowItemLinks,'$businessFlowItemLinks');
        $this->assign('business_flow_item_links',$businessFlowItemLinks);
        $businessFlowItemLinkIndex = array_column($businessFlowItemLinks,"id");
        $this->assign('business_flow_item_link_index',$businessFlowItemLinkIndex);
        $now_and_next_item = [];
        $now_and_next_item[]=$SaleChancesData["sale_status"];
        for($i=0;$i<count($businessFlowItemLinks);$i++){
            if($businessFlowItemLinks[$i]["item_id"] == $SaleChancesData["sale_status"]){
                if($i+1<count($businessFlowItemLinks)){
                    $now_and_next_item[]=$businessFlowItemLinks[$i+1]["item_id"];
                }
                break;
            }
        }
        //var_exp($now_and_next_item,'$now_and_next_item');
        $this->assign('now_and_next_item',$now_and_next_item);
        //var_exp($this->_activityBusinessFlowItem,'$activity_business_flow_item_index');
        $this->assign('activity_business_flow_item_index',$this->_activityBusinessFlowItem);
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
        $business_flows = $businessFlowModel->getAllBusinessFlowByuserId($uid);
        //var_exp($business_flows,'$business_flows',1);
        $this->assign('business_flows',$business_flows);
        $sale_chance["prepay_time"]=time();
        $this->assign('sale_chance',$sale_chance);
        return view();
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
    protected function _getSaleChanceForInput(){
        // add ale chance page
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
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建销售机会时发生错误！"];
        $saleChance = $this->_getSaleChanceForInput();
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
        $saleChance = $this->_getSaleChanceForInput();
        try{
            $saleChanceM = new SaleChanceModel($this->corp_id);
            $saleChanceflg = $saleChanceM->setSaleChance($id,$saleChance);
            $result['data'] = $saleChanceflg;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存销售机会成功！";
        return json($result);
    }
    protected function _update(){
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