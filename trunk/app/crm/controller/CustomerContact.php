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

class CustomerContact extends Initialize{
    public function index(){
        echo "crm/customer_contact/index";
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取客户信息时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactData = $customerContactM->getCustomerContact($id);
            //TODO 获取其他表内容
            $result['data'] = $customerContactData;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取客户信息成功！";
        return json($result);
    }
    protected function _getCustomerContactForInput(){
        // add customer contact page
        $customerContact['customer_id'] = input('customer_id',0,'int');
        $customerContact['contact_name'] = input('contact_name ');
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
        $result = ['status'=>0 ,'info'=>"新建客户联系人时发生错误！"];
        $customerContact = $this->_getCustomerContactForInput();
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactId = $customerContactM->addCustomerContact($customerContact);
            $result['data'] = $customerContactId;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "新建客户联系人成功！";
        return json($result);
    }
    public function update(){
        $result = ['status'=>0 ,'info'=>"保存客户时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $customerContact = $this->_getCustomerContactForInput();
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactFlg = $customerContactM->setCustomerContact($id,$customerContact);
            //TODO 保存其他表内容,需开启事务
            $result['data'] = $customerContactFlg;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存客户信息成功！";
        return json($result);
    }
}