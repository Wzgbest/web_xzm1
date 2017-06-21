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
use app\crm\model\CustomerImportRecord as CustomerImport;
use app\crm\model\CustomerImportFail;

class Customer extends Initialize{
    public function index(){
        echo "crm/customer/index";
    }
    public function manager(){
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
        $filter = [];//TODO 客户状态 	客户来源 resource_from	 沟通结果 tend_to 获取途径 跟踪人 维护人 添加人
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getManagerCustomer($num,$p,$filter);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    public function pool(){//TODO
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
        $filter = [];
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getPoolCustomer($num,$p,$filter);
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
        $filter = $this->_getCustomerFilter(["take_type","grade","customer_name","contact_name","comm_status","sale_chance"]);
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
    public function subordinate(){//TODO
        $result = ['status'=>0 ,'info'=>"查询客户信息时发生错误！"];
        $num = input('num',0,'int');
        $num = $num?:20;
        $p = input("p",0,"int");
        $p = $p?:1;
        $uid = session('userinfo.userid');
        $filter = [];
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getSubordinateCustomer($num,$p,$uid,$filter);
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
        $filter = [];
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->getPendingCustomer($num,$p,$filter);
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
        }$filter = $this->_getCustomerFilter(["take_type","grade","customer_name","contact_name","comm_status","sale_chance"]);
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
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建客户时发生错误！"];
        $customer = $this->_getCustomerForInput();
        if(!in_array($customer['belongs_to'],[3,4])){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $customerNegotiate = $this->_getCustomerNegotiateForInput();
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customerId = $customerM->addCustomer($customer);
            $customerNegotiate["customer_id"] = $customerId;
            $result['data'] = $customerId;
        }catch (\Exception $ex){
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
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->setCustomer($id,$customer);
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
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
        var_exp($ids,'$ids',1);
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerM = new CustomerModel($this->corp_id);
            $customers_data = $customerM->delCustomer($ids);
            //TODO 删除其他表内容,需开启事务
            $result['data'] = $customers_data;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "删除客户信息成功！";
        return json($result);
    }

    public function importCustomer(){
        $result =  ['status'=>0 ,'info'=>"导入失败！"];
        $file_id = input("file_id",0,"int");
        $import_to = input("import_to",0,"int");//导入到1客户管理，2公海池
        if(!$file_id || !$import_to){
            $result['info'] = "参数错误！";
            return json($result);
        }

        //客户信息默认参数
        $customer_default = [];
        if($import_to==1){
            $customer_default['belongs_to'] = 1;
        }elseif($import_to==2){
            $customer_default['belongs_to'] = 2;
        }else{
            $result['info'] = '参数错误!';
            return json($result);
        }
        $customer_default['handle_man'] = 0;

        $column = array (
            'A' => 'customer_name',
            'B' => 'telephone',
            'C' => 'address',
            'D' => 'location',
            'E' => 'field',
            'F' => 'website',
        );
        $column_res = getHeadFormExcel($file_id);
        if ($column_res ['status'] == 0) {
            $result['info'] = $column_res ['data'];
            return json($result);
        }
        $column_default = [
            0 => '公司名称',
            1 => '电话号码',
            2 => '地址',
            3 => '定位',
            4 => '行业',
            5 => '官网',];
        $length=count($column_default);
        for($i=0;$i<$length;$i++){
            if($column_res['data'][$i]!=$column_default[$i]){
                $result['info'] = 'Excel文件表头读取失败,请勿更改模板列!';
                return json($result);
            }
        }
        $res = importFormExcel($file_id,$column);
        //var_exp($res['data'],'$res[\'data\']');
        if ($res ['status'] == 0) {
            $result['info'] = 'Excel文件读取失败!';
            return json($result);
        }

        //获取批次
        $uid = session('userinfo.userid');
        $customerImport = new CustomerImport($this->corp_id);
        $record = $customerImport->getNewImportCustomerRecord($uid);
        if(!$record){
            $result['info'] = '添加导入记录失败!';
            return json($result);
        }
        //var_exp($record,'$record',1);
        $batch = $record['batch'];

        //校验数据
        $success_num = 0;
        $fail_array = [];
        $customerImport->link->startTrans();
        foreach ($res ['data'] as $item) {
            $item['batch'] = $batch;
            try {
                $customer = $customer_default;
                $location = explode(",",$item['location']);
                $customer['customer_name'] = $item['customer_name'];
                $customer['resource_from'] = 1;
                $customer['telephone'] = $item['telephone'];
                $customer['add_man'] = $uid;
                $customer['add_batch'] = $item['batch'];
                $customer['field'] = $item['field'];
                $customer['lat'] = isset($location[0])?:0;
                $customer['lng'] = isset($location[1])?:0;
                $customer['address'] = $item['address'];
                $customer['website'] = $item['website'];
                $validate_result = $this->validate($customer,'Customer');
                //验证字段
                if(true !== $validate_result){
                    exception($validate_result);
                }
                $customerImport->link->startTrans();
                $customerM = new CustomerModel($this->corp_id);
                $add_flg = $customerM->addCustomer($customer);
                if(!$add_flg){
                    exception('添加客户失败!');
                }
//                $customerContact['customer_id'] = $add_flg;
//                $customerContact['contact_name'] = $item['customer_name'];
//                $customerContact['phone_first'] = $item['telephone'];
//                $validate_result = $this->validate($customerContact,'CustomerContact');
//                //验证字段
//                if(true !== $validate_result){
//                    exception($validate_result);
//                }
//                $customerContactM = new CustomerContactModel($this->corp_id);
//                $user_corp_add_flg = $customerContactM->addCustomerContact($customerContact);
//                if(!$user_corp_add_flg){
//                    exception('添加客户联系方式失败!');
//                }
            }catch(\Exception $ex){
                $customerImport->link->rollback();
                $item['remark'] = $ex->getMessage();
                $fail_array[] = $item;
                continue;
            }
            $customerImport->link->commit();
            $success_num++;
        }
        $customerImport->link->commit();
        $fail_num = count($fail_array);

        //判断执行情况,写入失败记录
        if($fail_num == 0){
            $data['import_result'] = 2;
        }else{
            $customerImportFail = new CustomerImportFail($this->corp_id);
            $fail_save_flg = $customerImportFail->addMutipleImportCustomerFail($fail_array);
            if(!$fail_save_flg){
                $result['info'] = '写入导入失败记录时发生错误!';
                return json($result);
            }
            if($success_num == 0){
                $data['import_result'] = 0;
            }else{
                $data['import_result'] = 1;
            }
        }

        //更新记录数
        $data['success_num'] = $success_num;
        $data['fail_num'] = $fail_num;
        $save_flg = $customerImport->setImportCustomerRecord($record['id'],$data);
        if(!$save_flg){
            $result['info'] = '写入导入记录失败!';
            return json($result);
        }

        //返回信息
        $result['status'] = 1;
        $result['info'] = '成功导入'.$success_num.'条,失败'.$fail_num.'条!';
        return json($result);
    }

    public function exportCustomer(){
        $scale = input('scale',0,'int');
        if(!$scale){
            $this->error("参数错误!");
        }
        $self = input('self',0,'int');
        $uid = session('userinfo.userid');
        $customerM = new CustomerModel($this->corp_id);
        $customers_data = $customerM->getExportCustomers($uid,$scale,$self);
        //var_exp($customers_data,'$customers_data',1);
        if(!$customers_data){
            $this->error("导出员工失败!");
        }
        $excel_data = [[
            0 => '公司名称',
            1 => '电话号码',
            2 => '地址',
            3 => '定位',
            4 => '行业',
            5 => '官网',
        ]];
        foreach ($customers_data as $customer){
            unset($customer['id']);
            $excel_data[] = $customer;
        }
        outExcel($excel_data,'customers-'.time().'.xlsx');
    }

    public function exportFailCustomer(){
        $result =  ['status'=>0 ,'info'=>"导出失败！"];
        $record_id = input("record_id",0,"int");
        $customerImport = new CustomerImport($this->corp_id);
        $record = $customerImport->getImportCustomerRecord($record_id);
        if(!$record){
            $result['info'] = '未找到导入记录!';
            return json($result);
        }
        if($record['import_result']==2){
            $result['info'] = '该批次导入全部成功,无法导出!';
            return json($result);
        }
        $batch = $record['batch'];
        $customerImportFail = new CustomerImportFail($this->corp_id);
        $importFailCustomers = $customerImportFail->getCustomerByBatch($batch);
        if(!$importFailCustomers){
            $result['info'] = '未找到导入失败的员工!';
            return json($result);
        }
        $excel_data = [[
            0 => "导入批次",
            1 => '公司名称',
            2 => '电话号码',
            3 => '地址',
            4 => '定位',
            5 => '行业',
            6 => '官网',
            7 => "备注"
        ]];
        foreach ($importFailCustomers as $importFailCustomer){
            unset($importFailCustomer['id']);
            $excel_data[] = $importFailCustomer;
        }
        outExcel($excel_data,'import-Fail-Customers-'.$batch.'-'.time().'.xlsx');
    }
}