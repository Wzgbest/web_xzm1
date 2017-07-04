<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Corporation;
use app\common\model\Employee as EmployeeModel;
use app\huanxin\service\Api as HuanxinApi;
use app\systemsetting\model\EmployeeImportRecord;
use app\systemsetting\model\EmployeeImportFail;
use think\Db;
use app\common\model\UserCorporation;

class EmployeeImport extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $filter = $this->_getCustomerFilter(["start_time","end_time","batch","operator"]);
        try{
            $employeeImport = new EmployeeImportRecord($this->corp_id);
            $employeeImportRecord = $employeeImport->getImportEmployeeRecord($num,$p,$filter);
            $this->assign("listdata",$employeeImportRecord);
            $employees_count = $employeeImport->getImportEmployeeRecordCount($filter);
            $this->assign("count",$employees_count);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($employees_count/$num);
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
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
        $result = ['status'=>0 ,'info'=>"查询员工导入发生错误！"];
        $num = input("num",10,"int");
        $p = input("p",1,"int");
        try{
            $employeeImport = new EmployeeImportRecord($this->corp_id);
            $employeeImportRecord = $employeeImport->getImportEmployeeRecord($num,$p);
            $result['data'] = $employeeImportRecord;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取员工导入发生错误！"];
        $id = input("id");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $map["id"] = $id;
        try{
            $employeeImport = new EmployeeImportRecord($this->corp_id);
            $record = $employeeImport->getImportEmployeeRecord(1,0,["id"=>$id]);
            $result['data'] = $record;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }
    /**
     * @return \think\response\Json
     * created by blu10ph
     */
    public function importEmployee(){
        $result =  ['status'=>0 ,'info'=>"导入失败！"];
        $file_id = input("file_id",0,"int");
        if(!$file_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $column = array (
            'A' => 'username',
            'B' => 'telephone',
            'C' => 'wired_phone',
            'D' => 'part_phone',
            'E' => 'sex',
            'F' => 'worknum',
            'G' => 'is_leader',
            'H' => 'role',
            'I' => 'qqnum',
            'J' => 'wechat',
        );
        $column_res = getHeadFormExcel($file_id);
        if ($column_res ['status'] == 0) {
            $result['info'] = $column_res ['data'];
            return json($result);
        }
        $column_default = [
            0 => '员工姓名',
            1 => '手机号',
            2 => '座机',
            3 => '分机',
            4 => '性别',
            5 => '工号',
            6 => '是领导',
            7 => '角色',
            8 => 'QQ号',
            9 => '微信号',];
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
        $employeeImport = new EmployeeImportRecord($this->corp_id);
        $record = $employeeImport->getNewImportEmployeeRecord($uid);
        if(!$record){
            $result['info'] = '添加导入记录失败!';
            return json($result);
        }
        //var_exp($record,'$record',1);
        $batch = $record['batch'];

        //获取已存在员工电话
        $employeeM = new EmployeeModel($this->corp_id);
        $telephones = $employeeM->getAllTels();
        $telephones = array_filter($telephones);
        $telephones = array_unique($telephones);

        //校验数据
        $success_num = 0;
        $fail_array = [];
        $employeeImport->link->startTrans();
        Corporation::startTrans();
        foreach ($res ['data'] as $item) {
            try {
                if(in_array($item["telephone"], $telephones)){
                    exception("手机号已存在!");
                }
                $employee['corpid'] = $this->corp_id;
                $employee['telephone'] = $item['telephone'];
                $employee['username'] = $item['telephone'];
                $employee['truename'] = $item['username'];
                $employee['struct_id'] = 0;
                $is_leader = (trim($item['is_leader']) == "是") ? 1 : 0;
                $employee['is_leader'] = $is_leader;
                $employee['worknum'] = $item['worknum'];
                $employee['role'] = $item['role'];
                $sex = trim($item['sex']);
                $gender = ($sex == "男") ? 1 : (($sex == "女") ? 0 : 2);
                $employee['gender'] = $gender;
                $employee['qqnum'] = $item['qqnum'];
                $employee['wechat'] = $item['wechat'];
                $employee['wired_phone'] = $item['wired_phone'];
                $employee['part_phone'] = $item['part_phone'];
                $validate_result = $this->validate($employee, 'Employee');
                //验证字段
                if (true !== $validate_result) {
                    exception($validate_result);
                }
                unset($employee['struct_id']);
                $employeeImport->link->startTrans();
                Corporation::startTrans();
                $add_flg = $employeeM->addSingleEmployee($employee);
                //var_exp($add_flg, '$add_flg');
                if (!$add_flg) {
                    exception('导入员工帐号时发生错误!');
                }
                $user_corporation = ["corp_name" => $this->corp_id, "telephone" => $item['telephone']];
                $userCorpM = new UserCorporation($this->corp_id);
                $user_corp_add_flg = $userCorpM->addMutipleUserCorp([$user_corporation]);
                //var_exp($user_corp_add_flg, '$user_corp_add_flg');
                if (!$user_corp_add_flg) {
                    exception('导入帐号时发生错误!');
                }
                //$huanxin_array = ['username'=>$item['telephone'],'password'=>'123456','nickname'=>$item['username']];
                //$huanxin_json = json_encode($huanxin_array);
                $huanxin = new HuanxinApi();
                //$reg_info = $huanxin->addFriend($item['telephone']);
                $reg_info['status'] = 1;//TODO 测试 先关了
                if (!$reg_info['status']) {
                    exception('注册环信时发生错误!');
                }
            }catch(\Exception $ex){
                $employeeImport->link->rollback();
                UserCorporation::rollback();
                $item['batch'] = $batch;
                $item['remark'] = $ex->getMessage();
                $fail_array[] = $item;
                continue;
            }
            $employeeImport->link->commit();
            UserCorporation::commit();
            $success_num++;
            //var_exp($success_num, '$success_num');
        }
        $employeeImport->link->commit();
        UserCorporation::commit();
        $fail_num = count($fail_array);
        //var_exp($fail_num,'$fail_num');

        //判断执行情况,写入失败记录
        if($fail_num == 0){
            $data['import_result'] = 2;
        }else{
            $employeeImportFail = new EmployeeImportFail($this->corp_id);
            $fail_save_flg = $employeeImportFail->addMutipleImportEmployeeFail($fail_array);
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
        //var_exp($record,'$record');
        //var_exp($data,'$data');
        $save_flg = $employeeImport->setImportEmployeeRecord($record['id'],$data);
        if(!$save_flg){
            $result['info'] = '写入导入记录失败!';
            return json($result);
        }

        //返回信息
        $result['status'] = 1;
        $result['info'] = '成功导入'.$success_num.'条,失败'.$fail_num.'条!';
        return json($result);
    }

    /**导出员工
     * @return \think\response\Json
     * created by blu10ph
     */
    public function exportEmployee(){
        $ids = input("ids");
        if(!$ids){
            $this->error("参数错误!");
        }
        $ids_arr = explode(",",$ids);
        $ids_arr = array_map("intval",$ids_arr);
        //var_exp($ids_arr,'$ids_arr',1);
        $where["id"] = ["in",$ids_arr];
        $employeeM = new EmployeeModel($this->corp_id);
        $employees_data = $employeeM->exportAllEmployees($where);
        if(!$employees_data){
            $this->error("导出员工失败!");
        }
        $excel_data = [[
            0 => "员工姓名",
            1 => "手机号",
            2 => "座机",
            3 => "分机",
            4 => "性别",
            5 => "工号",
            6 => "是领导",
            7 => "角色",
            8 => "QQ号",
            9 => "微信号",
            10 => "备注"
        ]];
        foreach ($employees_data as $employee){
            unset($employee['id']);
            $employee['gender'] = $employee['gender']==1?"男":"女";
            $employee['is_leader'] = $employee['is_leader']==1?"是":"否";
            $excel_data[] = $employee;
        }
        outExcel($excel_data,'employees-'.time().'.xlsx');
    }

    /**
     * @return \think\response\Json
     * created by blu10ph
     */
    public function exportFailEmployee(){
        $result =  ['status'=>0 ,'info'=>"导出失败！"];
        $record_id = input("record_id",0,"int");
        $employeeImport = new EmployeeImportRecord($this->corp_id);
        $record = $employeeImport->getImportEmployeeRecord(1,0,["id"=>$record_id]);
        if(!$record){
            $result['info'] = '未找到导入记录!';
            return json($result);
        }
        if($record['import_result']==2){
            $result['info'] = '该批次导入全部成功,无法导出!';
            return json($result);
        }
        $batch = $record['batch'];
        $employeeImportFail = new EmployeeImportFail($this->corp_id);
        $importFailEmployees = $employeeImportFail->getEmployeeByBatch($batch);
        if(!$importFailEmployees){
            $result['info'] = '未找到导入失败的员工!';
            return json($result);
        }
        $excel_data = [[
            0 => "导入批次",
            1 => "员工姓名",
            2 => "手机号",
            3 => "座机",
            4 => "分机",
            5 => "性别",
            6 => "工号",
            7 => "是领导",
            8 => "角色",
            9 => "QQ号",
            10 => "微信号",
            11 => "备注"
        ]];
        foreach ($importFailEmployees as $importFailEmployee){
            unset($importFailEmployee['id']);
            $excel_data[] = $importFailEmployee;
        }
        outExcel($excel_data,'import-Fail-Employees-'.$batch.'-'.time().'.xlsx');
    }
}