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

class SaleChance extends Initialize{
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
        $this->assign("customer_id",$customer_id);
        $this->assign("fr",input('fr'));
        $customerM = new SaleChanceModel($this->corp_id);
        $SaleChancesData = $customerM->getAllSaleChancesByCustomerId($customer_id);
        $this->assign("sale_chance",$SaleChancesData);
        $customerM = new CustomerContact($this->corp_id);
        $customerData = $customerM->getCustomerContactCount($customer_id);
        $this->assign("customer_contact_num",$customerData);
        $customerM = new SaleChanceModel($this->corp_id);
        $customerData = $customerM->getSaleChanceCount($customer_id);
        $this->assign("sale_chance_num",$customerData);
        $customerM = new CustomerTrace($this->corp_id);
        $customerData = $customerM->getCustomerTraceCount($customer_id);
        $this->assign("customer_trace_num",$customerData);
    }
    public function show(){
        $this->_showSaleChance();
        return view();
    }
    public function add_page(){
        $this->assign("fr",input('fr'));
        $this->assign("customer_id",input('customer_id',0,"int"));
        return view();
    }
    public function edit_page(){
        $this->_showCustomer();
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
        $saleChance['sale_status'] = input('sale_status',0,'int');;

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
}