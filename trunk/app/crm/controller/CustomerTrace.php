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
use app\crm\model\SaleChance;
use app\crm\model\CustomerTrace as CustomerTraceModel;

class CustomerTrace extends Initialize{
    public function index(){
        echo "crm/customer_trace/index";
    }
    protected function _showCustomerTrace(){
        $customer_id = input('customer_id',0,'int');
        if(!$customer_id){
            $this->error("参数错误！");
        }
        $this->assign("customer_id",$customer_id);
        $this->assign("fr",input('fr'));
        $customerM = new CustomerTraceModel($this->corp_id);
        $customerTraceData = $customerM->getAllCustomerTraceByCustomerId($customer_id);
        $this->assign("customer_trace",$customerTraceData);
        $customerM = new CustomerContact($this->corp_id);
        $customerData = $customerM->getCustomerContactCount($customer_id);
        $this->assign("customer_contact_num",$customerData);
        $customerM = new SaleChance($this->corp_id);
        $customerData = $customerM->getSaleChanceCount($customer_id);
        $this->assign("sale_chance_num",$customerData);
        $customerM = new CustomerTraceModel($this->corp_id);
        $customerData = $customerM->getCustomerTraceCount($customer_id);
        $this->assign("customer_trace_num",$customerData);
    }
    public function show(){
        $this->_showCustomerTrace();
        return view();
    }
}