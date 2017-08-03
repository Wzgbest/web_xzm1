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
        $customerTraceList = $customerM->getAllCustomerTraceByCustomerId($customer_id);
        $customerTraceData = [];
        foreach($customerTraceList as $customerTrace){
            $customerTraceData[$customerTrace["create_time"]][] = $customerTrace;
        }
        $customerTraceData = array_filter($customerTraceData);
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
    public function get_list(){
        $result = ['status'=>0 ,'info'=>"获取跟踪记录时发生错误！"];
        $customer_id = input('customer_id',0,'int');
        if(empty($customer_id)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $last_id = input('last_id',0,'int');
        $num = input('num',10,'int');
        $customerM = new CustomerTraceModel($this->corp_id);
        $customerTraceList = $customerM->getCustomerTraceByLastId($customer_id,$last_id,$num);
        //var_exp($customerTraceList,'$customerTraceList',1);
        $customerTraceData = [];
        foreach($customerTraceList as $customerTrace){
            $customerTraceData[$customerTrace["create_time"]][] = $customerTrace;
        }
        $customerTraceData = array_filter($customerTraceData);
        $result['data'] = $customerTraceData;
        $result['status'] = 1;
        $result['info'] = "获取跟踪记录成功！";
        return json($result);
    }
    public function add(){
        $customer_id = input('customer_id',0,'int');
        $remark = input('remark','','string');
        if(empty($customer_id)||empty($remark)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $customersTrace["add_type"] = 1;
        $customersTrace["operator_type"] = 0;
        $customersTrace["operator_id"] = $uid;
        $customersTrace["create_time"] = time();
        $customersTrace["customer_id"] = $customer_id;
        $customersTrace["db_table_name"] = '';
        $customersTrace["db_field_name"] = "";
        $customersTrace["old_value"] = "";
        $customersTrace["new_value"] = "";
        $customersTrace["value_type"] = "";
        $customersTrace["option_name"] = '';
        $customersTrace["item_name"] = "";
        $customersTrace["from_name"] = '';
        $customersTrace["link_name"] = '';
        $customersTrace["to_name"] = '';
        $customersTrace["status_name"] = '';
        $customersTrace["remark"] = $remark;
        $customerM = new CustomerTraceModel($this->corp_id);
        $customerTraceflg = $customerM->addSingleCustomerMessage($customersTrace);
        if(!$customerTraceflg){
            $result['info'] = "提交跟踪记录失败！";
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "提交跟踪记录成功！";
        return json($result);
    }
}