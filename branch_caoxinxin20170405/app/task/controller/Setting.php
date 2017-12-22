<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\Task\controller;

use app\common\controller\Initialize;
use app\task\model\DayTask as DayTaskModel;
use app\common\model\StructureEmployee;
use app\common\model\Employee;
use app\datacount\service\DataCount as DataCountService;

class Setting extends Initialize{
    protected $dataCountSrv;
    public function __construct(){
        parent::__construct();
        $this->dataCountSrv = new DataCountService($this->corp_id,$this->uid);
    }
    public  function index(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $dayTaskM = new DayTaskModel($this->corp_id);
        $dayTaskInfos = $dayTaskM->getDayTaskByCreateEmployee($uid);
        $dayTaskNameList = [];
        foreach ($dayTaskInfos as $dayTaskInfo){
            $dayTaskNameList[] = ["id"=>$dayTaskInfo["id"],"task_name"=>$dayTaskInfo["task_name"]];
        }
        $task_ids = array_column($dayTaskInfos,"id");
        $dayTaskTargets = $dayTaskM->getTaskTargetByTaskIds(6,$task_ids);
        $dayTaskInfoList = [];
        foreach ($dayTaskTargets as $dayTaskTarget){
            $dayTaskInfoList[$dayTaskTarget["id"]][$dayTaskTarget["target_type"]] = $dayTaskTarget["target_num"];
        }
        $this->assign('day_task_name_list',$dayTaskNameList);
        $this->assign('day_task_info_list',json_encode($dayTaskInfoList,true));

        $structureEmployeeModel = new StructureEmployee($this->corp_id);
        $structures = $structureEmployeeModel->getAllStructureAndEmployee();
        $structure_employee = [];
        $structure_list = [];
        foreach ($structures as &$structure){
            $structure["employee_ids_arr"] = explode(",",$structure["employee_ids"]);
            $structure_employee[$structure["id"]] = explode(",",$structure["employee_ids"]);
            $structure_list[$structure["id"]] = ["pid"=>$structure["struct_pid"],"name"=>$structure["struct_name"]];
        }
        $employM = new Employee($this->corp_id);
        $friendsInfos = $employM->getAllUsers();
        $employee_name = [];
        foreach ($friendsInfos as $friendsInfo){
            $employee_name[$friendsInfo["id"]] = $friendsInfo["nickname"];
        }
        $this->assign("structure_employee",json_encode($structure_employee,true));
        $this->assign("structure_list",json_encode($structure_list,true));
        $this->assign("employee_name",json_encode($employee_name,true));
        return view();
    }
    public  function template(){
        return view();
    }
    public function task_list(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $dayTaskM = new DayTaskModel($this->corp_id);
        $employee_ids = [1,2,3,4,5,6,7,8,9];//TODO 获取员工
        $time = 1;
        $start_time = 0;
        $end_time = 0;
        list($start_time,$end_time) = $this->dataCountSrv->get_times($time,$start_time,$end_time);
        $data_count = $this->dataCountSrv->get_employee_data_count($employee_ids,$start_time,$end_time);
//        var_exp($data_count,'$data_count');
        $dayTaskInfos = $dayTaskM->getDayTaskEmployeeByCreateEmployee(4,$employee_ids);
//        var_exp($dayTaskInfos,'$dayTaskInfos');
        $employee_day_task_list = [];
        foreach ($dayTaskInfos as $dayTaskInfo){
            if(!isset($employee_day_task_list[$dayTaskInfo["take_employee"]])){
                $employee_day_task_list[$dayTaskInfo["take_employee"]]["id"]=$dayTaskInfo["id"];
                $employee_day_task_list[$dayTaskInfo["take_employee"]]["employee_id"]=$dayTaskInfo["take_employee"];
                $employee_day_task_list[$dayTaskInfo["take_employee"]]["truename"]=$dayTaskInfo["truename"];
            }
            $num=0;
            $field_name = "";
            if(isset($this->dataCountSrv->task_type_idx[$dayTaskInfo["target_type"]])){
                $field_name = $this->dataCountSrv->task_type_idx[$dayTaskInfo["target_type"]];
            }
            if(!empty($field_name)){
                if(isset($data_count["data"][$dayTaskInfo["take_employee"]][$field_name])){
                    $num = $data_count["data"][$dayTaskInfo["take_employee"]][$field_name];
                }
            }
            $employee_day_task_list[$dayTaskInfo["take_employee"]][$dayTaskInfo["target_type"]]["num"]=$num;
            $employee_day_task_list[$dayTaskInfo["take_employee"]][$dayTaskInfo["target_type"]]["target"]=$dayTaskInfo["target_num"];
        }
//        var_exp($employee_day_task_list,'$employee_day_task_list');
        $this->assign("employee_day_task_list",$employee_day_task_list);
        return view();
    }
}
