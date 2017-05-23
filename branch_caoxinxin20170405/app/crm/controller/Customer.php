<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\crm\model\Customer as CustomerModel;
use app\crm\model\CustomerContact as CustomerContactModel;
use app\crm\model\CustomerImportRecord as CustomerImport;
use app\crm\model\CustomerImportFail;

class Customer extends Initialize{


    public function importCustomer(){
        $result =  ['status'=>0 ,'info'=>"导入失败！"];
        $file_id = input("file_id",0,"int");
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
            $this->error ( $column_res ['data'] );
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
                return json_encode($result);
            }
        }
        $res = importFormExcel($file_id,$column);
        //var_exp($res['data'],'$res[\'data\']');
        if ($res ['status'] == 0) {
            $result['info'] = 'Excel文件读取失败!';
            return json_encode($result);
        }

        //获取批次
        $customerImport = new CustomerImport($this->corp_id);
        $record = $customerImport->getNewImportCustomerRecord(session('userinfo.id'));
        if(!$record){
            $result['info'] = '添加导入记录失败!';
            return json_encode($result);
        }
        //var_exp($record,'$record',1);
        $batch = $record['batch'];
        $uid = session('userinfo.id');

        //校验数据
        $success_num = 0;
        $fail_array = [];
        $customerImport->link->startTrans();
        foreach ($res ['data'] as $item) {
            $item['batch'] = $batch;
            try {
                $customer['customer_name'] = $item['customer_name'];
                $customer['resource_from'] = 1;
                $customer['telephone'] = $item['telephone'];
                $customer['add_man'] = $uid;
                $customer['add_batch'] = $item['batch'];
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
                $customerContact['customer_id'] = $add_flg;
                $customerContact['contact_name'] = $item['customer_name'];
                $customerContact['phone_first'] = $item['telephone'];
                $validate_result = $this->validate($customerContact,'CustomerContact');
                //验证字段
                if(true !== $validate_result){
                    exception($validate_result);
                }
                $customerContactM = new CustomerContactModel($this->corp_id);
                $user_corp_add_flg = $customerContactM->addCustomerContact($customerContact);//TODO 添加方法
                if(!$user_corp_add_flg){
                    exception('添加客户联系方式失败!');
                }
            }catch(\Exception $e){
                $customerImport->link->rollback();
                $item['remark'] = $e->getMessage();
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
                return json_encode($result);
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
            return json_encode($result);
        }

        //返回信息
        $result['status'] = 1;
        $result['info'] = '成功导入'.$success_num.'条,失败'.$fail_num.'条!';
        return json_encode($result);
    }

    public function exportFailCustomer(){
        $result =  ['status'=>0 ,'info'=>"导出失败！"];
        $record_id = input("record_id",0,"int");
        $customerImport = new CustomerImport($this->corp_id);
        $record = $customerImport->getImportCustomerRecord($record_id);
        if(!$record){
            $result['info'] = '未找到导入记录!';
            return json_encode($result);
        }
        if($record['import_result']==2){
            $result['info'] = '该批次导入全部成功,无法导出!';
            return json_encode($result);
        }
        $batch = $record['batch'];
        $customerImportFail = new CustomerImportFail($this->corp_id);
        $importFailCustomers = $customerImportFail->getCustomerBybatch($batch);
        if(!$importFailCustomers){
            $result['info'] = '未找到导入失败的员工!';
            return json_encode($result);
        }
        $excel_data = [[
            "导入批次",
            "员工姓名",
            "手机号",
            "座机",
            "分机",
            "性别",
            "工号",
            "是领导",
            "角色",
            "QQ号",
            "微信号",
            "备注"
        ]];
        foreach ($importFailCustomers as $importFailCustomer){
            unset($importFailCustomer['id']);
            $excel_data[] = $importFailCustomer;
        }
        outExcel($excel_data,'import-Fail-Customers-'.$batch.'-'.time().'.xlsx');
    }
}