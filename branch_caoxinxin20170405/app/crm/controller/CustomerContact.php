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
use app\crm\model\CustomerContact as CustomerContactModel;
use app\crm\model\SaleChance;
use app\crm\model\CustomerTrace;

class CustomerContact extends Initialize{
    public function index(){
        echo "crm/customer_contact/index";
    }
    protected function _showCustomerContact(){
        $customer_id = input('customer_id',0,'int');
        if(!$customer_id){
            $this->error("参数错误！");
        }
        $this->assign("customer_id",$customer_id);
        $this->assign("fr",input('fr'));
        $customerM = new CustomerContactModel($this->corp_id);
        $customerContactData = $customerM->getAllCustomerContactsByCustomerId($customer_id);
        $this->assign("customer_contact",$customerContactData);
        $customerM = new CustomerContactModel($this->corp_id);
        $customerData = $customerM->getCustomerContactCount($customer_id);
        $this->assign("customer_contact_num",$customerData);
        $customerM = new SaleChance($this->corp_id);
        $customerData = $customerM->getSaleChanceCount($customer_id);
        $this->assign("sale_chance_num",$customerData);
        $customerM = new CustomerTrace($this->corp_id);
        $customerData = $customerM->getCustomerTraceCount($customer_id);
        $this->assign("customer_trace_num",$customerData);
    }
    public function show(){
        $this->_showCustomerContact();
        return view();
    }
    protected function _showCustomer(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }
        $this->assign("id",$id);
        $this->assign("fr",input('fr'));
        $customerM = new CustomerContactModel($this->corp_id);
        $customer_contactData = $customerM->getCustomerContact($id);
        $this->assign("customer_contact",$customer_contactData);
    }
    public function edit_page(){
        $this->_showCustomer();
        return view();
    }
    public function add_page(){
        $this->assign("fr",input('fr'));
        $this->assign("customer_id",input('customer_id',0,"int"));
        return view();
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取联系人时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactData = $customerContactM->getCustomerContact($id);
            $result['data'] = $customerContactData;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取联系人成功！";
        return json($result);
    }
    protected function _getCustomerContactForInput($mode){
        if($mode){
            $uid = session('userinfo.userid');
            $customerContact['create_time'] = time();
            $customerContact['create_user'] = $uid;
            $customerContact['customer_id'] = input('customer_id',0,'int');
        }
        // add customer contact page
        $customerContact['contact_name'] = input('contact_name');
        $customerContact['phone_first'] = input('phone_first');

        $customerContact['phone_second'] = input('phone_second');
        $customerContact['phone_third'] = input('phone_third');

        $customerContact['email'] = input('email');
        $customerContact['qqnum'] = input('qqnum');
        $customerContact['wechat'] = input('wechat');

        $customerContact['structure'] = input('structure');
        $customerContact['occupation'] = input('occupation');
        $customerContact['key_decide'] = input('key_decide/b');
        $customerContact['deal_capability'] = input('deal_capability');
        $customerContact['introducer'] = input('introducer');
        $customerContact['close_degree'] = input('close_degree');
        $customerContact['birthday'] = input('birthday');
        $customerContact['hobby'] = input('hobby');
        $customerContact['remark'] = input('remark');

        return $customerContact;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建联系人时发生错误！"];
        $customerContact = $this->_getCustomerContactForInput("all");
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactId = $customerContactM->addCustomerContact($customerContact);
            $result['data'] = $customerContactId;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "新建联系人成功！";
        return json($result);
    }
    public function update(){
        $result = ['status'=>0 ,'info'=>"保存联系人时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $customerContact = $this->_getCustomerContactForInput(0);
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactFlg = $customerContactM->setCustomerContact($id,$customerContact);
            $result['data'] = $customerContactFlg;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存联系人成功！";
        return json($result);
    }
}