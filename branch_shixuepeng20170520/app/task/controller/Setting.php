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

class Setting extends Initialize{
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
    public  function task_list(){
        return view();
    }
}
