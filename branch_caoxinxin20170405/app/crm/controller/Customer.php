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
use app\crm\model\CustomerDelete as CustomerDelete;
use app\crm\model\CustomerNegotiate;

class Customer extends Initialize{
    public function index(){
        echo "crm/customer/index";
    }
    
    public function manage(){
        //TODO 管理员权限验证?
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $num = input('num',0,'int');
        $num = $num?:20;
        $p = input("p",0,"int");
        $p = $p?:1;
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
    public function public_pool(){
        //TODO 权限验证?
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $scale = input('scale',0,'int');
        if(!$scale || $scale>4){
            $result['info'] = '参数错误!';
            return json($result);
        }
        $num = input('num',0,'int');
        $num = $num?:20;
        $p = input("p",0,"int");
        $p = $p?:1;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $filter = $this->_getCustomerFilter(["resource_from","grade","customer_name"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getPublicPoolCustomer($num,$p,$filter,$field,$order,$direction);
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
        $scale = input('scale',0,'int');
        if(!$scale || $scale>4){
            $result['info'] = '参数错误!';
            return json($result);
        }
        $num = input('num',0,'int');
        $num = $num?:20;
        $p = input("p",0,"int");
        $p = $p?:1;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $filter = $this->_getCustomerFilter(["resource_from","is_public","customer_name"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getPoolCustomer($num,$p,$filter,$field,$order,$direction);
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
        $num = input('num',0,'int');
        $num = $num?:20;
        $p = input("p",0,"int");
        $p = $p?:1;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $uid = session('userinfo.userid');
        $filter = $this->_getCustomerFilter(["take_type","grade","sale_chance","comm_status","customer_name","tracer","contact_name","in_column"]);
        $field = $this->_getCustomerField(["take_type","grade"]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getSelfCustomer($num,$p,$uid,$filter,$field,$order,$direction);
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
        $num = input('num',0,'int');
        $num = $num?:20;
        $p = input("p",0,"int");
        $p = $p?:1;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $uid = session('userinfo.userid');
        $filter = $this->_getCustomerFilter(["take_type","grade","sale_chance","belongs_to","comm_status","customer_name","tracer","contact_name","in_column"]);
        $field = $this->_getCustomerField([]);
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getSubordinateCustomer($num,$p,$uid,$filter,$field,$order,$direction);
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
        $num = input('num',0,'int');
        $num = $num?:20;
        $p = input("p",0,"int");
        $p = $p?:1;
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
            if($belongs_to && in_array($belongs_to,[2,3])){//TODO 维护状态??
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
        if(in_array("take_type", $field_column) && in_array("take_type", $fields_arr)){//获取途径
            $field[] = "take_type";
        }
        if(in_array("grade", $field_column) && in_array("grade", $fields_arr)){//客户级别
            $field[] = "grade";
        }
        return $field;
    }
    public function get_column_num(){
        $result = ['status'=>0 ,'info'=>"查询客户列信息时发生错误！"];
        $uid = input('uid',0,'int');
        if(!$uid){
            $result['info'] = "参数错误！";
            return json($result);
        }
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
    public function release_customer(){
        $result = ['status'=>0 ,'info'=>"批量释放客户时发生错误！"];
        $ids = input('ids/a');
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $uid = session('userinfo.userid');
        try{
            $customerM = new CustomerModel($this->corp_id);
            $releaseFlg = $customerM->releaseCustomers($uid,$ids);
            if($releaseFlg){
                $result['info'] = "批量释放客户成功！";
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
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
        $result['info'] = "群发短信功能开发中！";
        return json($result);
    }
    public function get_customer_general(){
        $result = ['status'=>0 ,'info'=>"获取客户信息时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customerData = $customerM->getCustomer($id);
            //TODO 获取其他表内容
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
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customerData = $customerM->getCustomer($id);
            $result['data'] = $customerData;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取客户信息成功！";
        return json($result);
    }

    protected function _getCustomerForInput(){
        // add customer page
        $customer['customer_name'] = input('customer_name');
        $customer['telephone'] = input('telephone');
        $customer['comm_status'] = input('comm_status',0,'int');

        $customer['take_type'] = input('take_type',0,'int');
        $customer['grade'] = input('grade');

        $customer['field'] = input('field',0,'int');
        $customer['prov'] = input('prov');
        $customer['city'] = input('city');
        $customer['dist'] = input('dist');
        $customer['address'] = input('address');
        $customer['location'] = input('location');
        $customer['lat'] = input('lat',0,'double');
        $customer['lng'] = input('lng',0,'double');
        $customer['website'] = input('website');
        $customer['remark'] = input('remark');
        $customer['belongs_to'] = input('belongs_to',0,'int');

        return $customer;
    }
    protected function _getCustomerNegotiateForInput(){
        $comm_status = input('comm_status',0,'int');
        $customerNegotiate = getCommStatusArr($comm_status);
        return $customerNegotiate;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建客户时发生错误！"];
        $customer = $this->_getCustomerForInput();
        if(!in_array($customer['belongs_to'],[3,4])){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        $customerM = new CustomerModel($this->corp_id);
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
    public function update(){
        $result = ['status'=>0 ,'info'=>"保存客户时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $customer = $this->_getCustomerForInput();
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        $customerM = new CustomerModel($this->corp_id);
        try{
            //$customerM->link->startTrans();
            $customersFlg = $customerM->setCustomer($id,$customer);
            /*if(!$customersFlg){
                exception('添加客户失败!');
            }*/
            $customerNegotiateM = new CustomerNegotiate($this->corp_id);
            $customersNegotiateFlg = $customerNegotiateM->updateCustomerNegotiate($id,$customerNegotiate);
            /*if(!$customersNegotiateFlg){
                exception('更新客户沟通状态失败!');
            }*/
            //$customerM->link->commit();
        }catch (\Exception $ex){
            //$customerM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存客户信息成功！";
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
        try{
            $customerDeleteM = new CustomerDelete($this->corp_id);
            $customersDeleteFlg = $customerDeleteM->moveInDelMultipleCustomer($ids);
            if(!$customersDeleteFlg){
                exception('删除客户失败!');
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "删除客户信息成功！";
        return json($result);
    }
}