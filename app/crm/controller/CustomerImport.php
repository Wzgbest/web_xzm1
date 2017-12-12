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
use app\crm\model\CustomerImportRecord as CustomerImportRecordModel;
use app\crm\model\CustomerImportFail;
use app\common\model\Business;

class CustomerImport extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $type = input("type",0,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $filter = $this->_getCustomerFilter(["start_time","end_time","batch","operator"]);
        try{
            $customerImport = new CustomerImportRecordModel($this->corp_id);
            $customerImportRecord = $customerImport->getImportCustomerRecord($type,$num,$p,$filter);
            $this->assign("listdata",$customerImportRecord);
            $customers_count = $customerImport->getImportCustomerRecordCount($type,$filter);
            $this->assign("count",$customers_count);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($customers_count/$num);
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("start_num",$customers_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$customers_count?$end_num:$customers_count);
        return view();
    }
    protected function _getCustomerFilter($filter_column){
        $filter = [];
        if(in_array("start_time", $filter_column)){//开始时间
            $structure = input("start_time");
            if($structure){
                $filter["start_time"] = $structure;
            }
        }
        if(in_array("end_time", $filter_column)){//结束时间
            $role = input("end_time");
            if($role){
                $filter["end_time"] = $role;
            }
        }
        if(in_array("batch", $filter_column)){//批次
            $batch = input("batch");
            if($batch){
                $filter["batch"] = $batch;
            }
        }
        if(in_array("operator", $filter_column)){//导入人
            $operator = input("operator");
            if($operator){
                $filter["operator"] = $operator;
            }
        }
        return $filter;
    }

    public function table(){
        $result = ['status'=>0 ,'info'=>"查询客户导入发生错误！"];
        $num = 10;
        $import_to = input("import_to",0,'int');
        $p = input("p",1,"int");
        $type = input("type",0,"int");
        try{
            $map = ['import_to'=>$import_to];
            $customerImport = new CustomerImportRecordModel($this->corp_id);
            $customerImportRecord = $customerImport->getImportCustomerRecord($type,$num,$p,$map);
            $result['data'] = $customerImportRecord;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }

    public function get(){
        $result = ['status'=>0 ,'info'=>"获取客户导入发生错误！"];
        $id = input("id");
        $type = input("type",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $map["id"] = $id;
        try{
            $customerImport = new CustomerImportRecordModel($this->corp_id);
            $customerImportRecord = $customerImport->getImportCustomerRecord($type,1,0,["id"=>$id]);
            $result['data'] = $customerImportRecord;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
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
        if(in_array($import_to,[2,3])){
            $customer_default['belongs_to'] = $import_to;
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
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $customerImport = new CustomerImportRecordModel($this->corp_id);
        $record = $customerImport->getNewImportCustomerRecord($uid);
        if(!$record){
            $result['info'] = '添加导入记录失败!';
            return json($result);
        }
        //var_exp($record,'$record',1);
        $batch = $record['batch'];


        $businessModel = new Business();
        $business_index = $businessModel->getBusinessIdx();
        //var_exp($business_index,'$business_index',1);

        //校验数据
        $customerM = new CustomerModel($this->corp_id);
        $customerIdAndName = $customerM->getAllCustomerIdAndName();
        //var_exp($customerIdAndName,'$customerIdAndName');
        $customerName = array_values($customerIdAndName);
        //var_exp($customerName,'$customerName',1);

        $success_num = 0;
        $fail_array = [];
        $customerImport->link->startTrans();
        foreach ($res ['data'] as $item) {
            $item['batch'] = $batch;
            try {
                if(in_array($item['customer_name'],$customerName)){
                    exception("客户名称已存在");
                }
                $customerName[] = $item['customer_name'];
                $customer = $customer_default;
                $location = explode(",",$item['location']);
                $customer['customer_name'] = $item['customer_name'];
                $customer['resource_from'] = 1;
                $customer['telephone'] = $item['telephone'];
                $customer['add_man'] = $uid;
                $customer['add_batch'] = $item['batch'];
                $customer['add_time'] = $time;
                $customer['handle_man'] = $import_to==3?$uid:0;
                $customer['belongs_to'] = $import_to;
                $customer['field1'] = isset($business_index[$item['field']])?$business_index[$item['field']]:0;
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
        $data['import_to'] = $import_to;
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
        if(!($this->checkRule('crm/customer/customer_manage/delete'))){
            $this->noRole(2);
        }
        $self = input('self',0,'int');
        if($self){
            //TODO 权限验证
        }
        $ids = input("ids");
        if(!$ids){
            $this->error("参数错误!");
        }
        $ids_arr = [];
        if(!is_array($ids)){
            $ids_arr = explode(",",$ids);
        }else{
            $ids_arr = $ids;
        }
        $ids_arr = array_map("intval",$ids_arr);
//        var_exp($ids_arr,'$ids_arr',1);
        $scale = input('scale',0,'int');
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $customerM = new CustomerModel($this->corp_id);
        $customers_data = $customerM->getExportCustomers($uid,$scale,$self,$ids_arr);
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
        $type = input("type",0,"int");
        $customerImport = new CustomerImportRecordModel($this->corp_id);
        $record = $customerImport->getImportCustomerRecord($type,1,0,["id"=>$record_id]);
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