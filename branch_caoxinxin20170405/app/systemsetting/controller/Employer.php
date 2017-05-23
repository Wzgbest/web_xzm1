<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Corporation;
use app\common\model\Employer as EmployerModel;
use app\huanxin\service\Api as HuanxinApi;
use app\common\model\StructureEmployer;
use think\Request;
use app\systemsetting\model\EmployerImportRecord as EmployerImport;
use app\systemsetting\model\EmployerImportFail;
use think\Db;
use app\crm\model\Customer as CustomerModel;
use app\common\model\EmployerDelete;
use app\common\model\UserCorporation;

class Employer extends Initialize{
    public function index(){}

    /**
     * 查看员工详情
     * @param $user_id 员工id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function showSingleEmployerInfo($user_id)
    {
        $employerM = new EmployerModel($this->corp_id);
        $info = $employerM->getEmployerByUserid($user_id);
        return $info;
    }

    /**
     * 分页展示员工列表
     * @param int $page_now_num 当前页
     * @param int $page_rows 行数
     * @param int $struct_id 部门id
     * @param int $role 角色id
     * @param int $on_duty 状态
     * @return array
     */
    public function showEmployerList($page_now_num = 0, $page_rows = 10,$map = null)
    {
        $input = input('param.');
        $employerM = new EmployerModel($this->corp_id);
        $map = [];
        if (isset($input['struct_id'])) {
            $map['struct_id'] = $input['struct_id'];
        }
        if (isset($input['role'])) {
            $map['role'] = $input['role'];
        }
        if (isset($input['on_duty'])) {
            $map['on_duty'] = $input['on_duty'];
        }
        $res = $employerM->getPageEmployerList($page_now_num,$page_rows,$map);
        $count = $employerM->countPageEmployerList($map);
        $count = empty($count)? 0:$count[0]['num'];
        return [
            'data'=>$res,
            'page_now_num'=>$page_now_num,
            'page_row'=>$page_rows,
            'total_num'=>$count,
        ];
    }

    /**
     * 添加员工
     * @param $input 增加员工页面提交信息
     * @return array
     */
    public function addEmployer(Request $request)
    {
        if ($request->isGet()) {
            return view();
        } elseif ($request->isPost()) {
            $input = $request->param();
            $result = $this->validate($input,'Employer');
            $info['status'] = false;
            //验证字段
            if(true !== $result){
                $info['message'] = $result;
                return $info;
            }
            if ($input['is_leader'] == 0) {
                if (count($input['struct_id'])>1) {
                    return [
                        'status' =>false,
                        'message' =>'非领导不可选择多部门',
                    ];
                }
            }
            $struct_ids = $input['struct_id'];
            unset($input['struct_id']);
            $employerM = new EmployerModel($this->corp_id);
            $struct_empM = new StructureEmployer($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;
            $employerM->link->startTrans();
            try{
                //员工表增加信息
                $id = $employerM->addSingleEmployer($input);

                //部门表增加信息
                if ($input['is_leader'] == 1) {
                    foreach ($struct_ids as $k=>$v) {
                        $struct_data[$k]['user_id'] =$id;
                        $struct_data[$k]['struct_id'] = $v;
                    }
                    $f = $struct_empM->addMultipleStructureEmployer($struct_data);
                } else {
                    $struct_data['user_id'] = $id;
                    $struct_data['struct_id'] = $struct_ids;
                    $f = $struct_empM->addStructureEmployer($struct_data);
                }
                if ($id > 0 && $f > 0) {
                    //环信增加好友
                    $d = $huanxin->addFriend($input['telephone']);//TODO 测试注释掉
//                $d['status'] = true;//TODO 测试开启
                    if ($d['status']) {
                        $tel = [];
                        array_push($tel,$input['telephone']);
                        $im = $employerM->saveIm($tel);
                    } else {
                        $employerM->link->rollback();
                        $info['message'] = '添加环信好友有失败，联系管理员';
                        $info['error'] = $d['error'];
                        return $info;
                    }
                } else {
                    $employerM->link->rollback();
                    $info['message'] = '添加员工失败，联系管理员';
                    return $info;
                }
            }catch (\Exception $e){
                $employerM->link->rollback();

            }
            if ($id > 0 && $f >0 && $d['status'] && $im > 0) {
                $employerM->link->commit();
                return [
                    'status' => true,
                    'message' => '新增员工成功，添加环信好友成功',
                ];
            } else {
                $employerM->link->rollback();
                $info['message'] = '新增员工失败，或添加环信好友失败';
                return $info;
            }
        }
    }

    /**
     * 编辑员工信息
     * @param Request $request
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function editEmployer(Request $request, $user_id)
    {
        if ($request->isGet()) {
            $employerM = new EmployerModel($this->corp_id);
            $structM = new StructureEmployer($this->corp_id);
            $employer_info = $employerM->getEmployerByUserid($user_id);
            $struct_info = $structM->getEmployerStructure($user_id);
            $employer_info['struct_info'] = $struct_info;
            return $employer_info;
        } elseif ($request->isPost()) {
            $input = $request->param();
            $result = $this->validate($input,'Employer');
            $info['status'] = false;
            //验证字段
            if(true !== $result){
                $info['message'] = $result;
                return $info;
            }
            if ($input['is_leader'] == 0) {
                if (count($input['struct_id'])>1) {
                    return [
                        'status' =>false,
                        'message' =>'非领导不可选择多部门',
                    ];
                }
            }
            $struct_ids = $input['struct_id'];
            $user_id = $input['user_id'];
            unset($input['struct_id']);
            unset($input['user_id']);
            $employerM = new EmployerModel($this->corp_id);
            $struct_empM = new StructureEmployer($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;
            //取出旧设置的部门ids
            $struct_old = $struct_empM->getStructIdsByEmployer($user_id);
            $struct_ = [];
            foreach ($struct_old as $val) {
                $struct_[] .=$val['struct_id'];
            }

            $employerM->link->startTrans();
            try{
                //员工表修改信息
                $em_res = $employerM->setSingleEmployerInfobyId($user_id,$input);

                //部门表修改信息，1,2,3 --->  1,2,3,4,5   1,2,3,4,5--->1,2,3
                if ($input['is_leader'] == 1) {
                    $insert = array_diff($struct_ids,$struct_);//新添加的
                    $delete = array_diff($struct_,$struct_ids);//需要删除的
                    //有需要添加的
                    if (!empty($insert)) {
                        $insert_data = [];
                        foreach ($insert as $k=>$v) {
                            array_push($insert_data,['user_id'=>$user_id,'struct_id'=>$v]);
                        }
                        if (count($insert_data) >1) {
                            $res = $struct_empM->addMultipleStructureEmployer($insert_data);
                        } else {
                            $res = $struct_empM->addStructureEmployer($insert_data);
                        }
                    } else {
                        $res = 1;
                    }

                    //有需要删除的
                    if (!empty($delete)) {
                        $delete_data = [];
                        foreach ($delete as $k=>$v) {
                            array_push($delete_data,$v);
                        }
                        $del_res = $struct_empM->deleteMultipleStructureEmployer($user_id,$delete_data);
                    } else {
                        $del_res = 1;
                    }
                } else {
                    //非领导
                    $struct_data['user_id'] = $user_id;
                    $struct_data['struct_id'] = $struct_ids[0];
                    $res = $struct_empM->setStructureEmployerById($user_id,$struct_old[0]['struct_id'],$struct_data);
                    if ($res ===0) {
                        $res =1;
                    }
                    $del_res = 1;
                }
            }catch (\Exception $e){
                $employerM->link->rollback();
            }
            if ($em_res >= 0 && $res>0 && $del_res>0) {
                $employerM->link->commit();
                return [
                    'status' => true,
                    'message' => '修改员工信息成功',
                ];
            } else {
                $employerM->link->rollback();
                $info['message'] = '修改员工信息失败';
                return $info;
            }
        }
    }

    /**
     * 删除单个员工、多个员工
     * @param $user_ids 用户ids，逗号分隔
     * @return array
     */
    public function deleteMultipleEmployer($user_ids)
    {
        $customerM = new CustomerModel();
//        检测有无保护客户
        $res = $customerM->getCustomersByUserIds($user_ids); $res =null;
        if (!empty($res)) {
            $arr=[];
            foreach ($res as $k=>$v) {
                array_push($arr,$v['truename']);
            }
            $arr = array_unique($arr);
            if (count($arr) < 4) {
                $str = implode(',',$arr);
            } else {
                $str = $arr[0].','.$arr[1].','.$arr[2];
            }
            return [
                'status'=>false,
                'message'=>$str.'等用户有未释放的客户，删除失败',
            ];
        } else {
//        查询员工状态是否为离职
            $employerM = new EmployerModel();
            $dat = $employerM->getEmployerByUserids($user_ids);
            if (!empty($dat)) {
                $arr=[];
                $users=[];
                $tel_arr=[];
                $names = [];
                foreach ($dat as $k=>$v) {
                    if ($v['status'] ==1) {
                        array_push($arr,$v['truename']);
                    }
                    unset($v['status']);
                    unset($v['on_duty']);
                    array_push($users,$v);
                    array_push($tel_arr,$v['telephone']);
                    array_push($names,$v['truename']);
                }
                if (!empty($arr)) {
                    $str = count($arr) < 4 ? implode(',',$arr) : $arr[0].','.$arr[1].','.$arr[2];
                    return [
                        'status'=>false,
                        'message'=>$str.'等员工未改为离职状态，无法删除'
                    ];
                }
                $tel_arr = implode(',',$tel_arr);
                $names = implode(',',$names);
                $emp_delM = new EmployerDelete();
                $stru_empM = new StructureEmployer();
                $emp_delM->link->startTrans();
                Corporation::startTrans();
                try{
//                删除员工
                    $b = $employerM->deleteMultipleEmployer($user_ids);
//                转移到employer_delete表
                    $d =$emp_delM->addMultipleBackupInfo($users);
//                    删除用户公司对照表信息
                    $f = UserCorporation::deleteUserCorp($tel_arr);
//                    删除部门员工表信息
                    $g = $stru_empM->deleteMultipleStructureEmployer($user_ids);
                }catch(\Exception $e){
                    $emp_delM->link->rollback();
                    UserCorporation::rollback();
                }
                if ($b > 0 && $d > 0 && $f > 0 && $g > 0) {
//                    $emp_delM->link->commit();
//                    UserCorporation::commit();
                    $emp_delM->link->rollback();
                    UserCorporation::rollback();
//                    write_log(session('userinfo')['userid'],6,'删除员工'.$names.'成功',$this->corp_id);
                    return [
                        'status'=>true,
                        'message'=>'删除员工成功',
                    ];
                } else {
                    $emp_delM->link->rollback();
                    UserCorporation::rollback();
                    return [
                        'status'=>false,
                        'message' =>'删除员工失败',
                    ];
                }
            } else {
                return [
                    'status'=>false,
                    'message'=>'员工不存在'
                ];
            }
        }
    }

    public function importEmployer(){
        $result =  ['status'=>0 ,'info'=>"导入失败！"];
        $file_id = input("file_id",0,"int");
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
            $this->error ( $column_res ['data'] );
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
        $employerImport = new EmployerImport($this->corp_id);
        $record = $employerImport->getNewImportEmployerRecord(session('userinfo.id'));
        if(!$record){
            $result['info'] = '添加导入记录失败!';
            return json_encode($result);
        }
        //var_exp($record,'$record',1);
        $batch = $record['batch'];

        //校验数据
        $success_num = 0;
        $fail_array = [];
        $employerImport->link->startTrans();
        Corporation::startTrans();
        foreach ($res ['data'] as $item) {
            try {
                $employer['corpid'] = $this->corp_id;
                $employer['telephone'] = $item['telephone'];
                $employer['username'] = $item['telephone'];
                $employer['truename'] = $item['username'];
                $employer['struct_id'] = 0;
                $is_leader = (trim($item['is_leader']) == "是") ? 1 : 0;
                $employer['is_leader'] = $is_leader;
                $employer['worknum'] = $item['worknum'];
                $employer['role'] = $item['role'];
                $sex = trim($item['sex']);
                $gender = ($sex == "男") ? 1 : (($sex == "女") ? 0 : 2);
                $employer['gender'] = $gender;
                $employer['qqnum'] = $item['qqnum'];
                $employer['wechat'] = $item['wechat'];
                $employer['wired_phone'] = $item['wired_phone'];
                $employer['part_phone'] = $item['part_phone'];
                $validate_result = $this->validate($employer, 'Employer');
                //验证字段
                if (true !== $validate_result) {
                    exception($validate_result);
                }
                unset($employer['struct_id']);
                $employerImport->link->startTrans();
                Corporation::startTrans();

                $employerM = new EmployerModel($this->corp_id);
                $add_flg = $employerM->addSingleEmployer($employer);
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
            }catch(\Exception $e){
                $employerImport->link->rollback();
                UserCorporation::rollback();
                $item['batch'] = $batch;
                $item['remark'] = $e->getMessage();
                $fail_array[] = $item;
                continue;
            }
            $employerImport->link->commit();
            UserCorporation::commit();
            $success_num++;
            //var_exp($success_num, '$success_num');
        }
        $employerImport->link->commit();
        UserCorporation::commit();
        $fail_num = count($fail_array);
        //var_exp($fail_num,'$fail_num');

        //判断执行情况,写入失败记录
        if($fail_num == 0){
            $data['import_result'] = 2;
        }else{
            $employerImportFail = new EmployerImportFail($this->corp_id);
            $fail_save_flg = $employerImportFail->addMutipleImportEmployerFail($fail_array);
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
        //var_exp($record,'$record');
        //var_exp($data,'$data');
        $save_flg = $employerImport->setImportEmployerRecord($record['id'],$data);
        if(!$save_flg){
            $result['info'] = '写入导入记录失败!';
            return json_encode($result);
        }

        //返回信息
        $result['status'] = 1;
        $result['info'] = '成功导入'.$success_num.'条,失败'.$fail_num.'条!';
        return json_encode($result);
    }

    public function exportFailEmployer(){
        $result =  ['status'=>0 ,'info'=>"导出失败！"];
        $record_id = input("record_id",0,"int");
        $employerImport = new EmployerImport($this->corp_id);
        $record = $employerImport->getImportEmployerRecord($record_id);
        if(!$record){
            $result['info'] = '未找到导入记录!';
            return json_encode($result);
        }
        if($record['import_result']==2){
            $result['info'] = '该批次导入全部成功,无法导出!';
            return json_encode($result);
        }
        $batch = $record['batch'];
        $employerImportFail = new EmployerImportFail($this->corp_id);
        $importFailEmployers = $employerImportFail->getEmployerBybatch($batch);
        if(!$importFailEmployers){
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
        foreach ($importFailEmployers as $importFailEmployer){
            unset($importFailEmployer['id']);
            $excel_data[] = $importFailEmployer;
        }
        outExcel($excel_data,'import-Fail-Employers-'.$batch.'-'.time().'.xlsx');
    }
}