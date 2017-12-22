<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

namespace app\task\controller;

use app\common\controller\Initialize;
use app\task\model\EmployeeTask as EmployeeTaskModel;
use app\task\model\TaskTarget as TaskTargetModel;
use app\task\model\TaskTake as TaskTakeModel;
use app\task\model\DayTask as DayTaskModel;

class DayTask extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    protected function _check_take($uid){
        if(!is_array($uid)){
            $uid = explode(",",$uid);
        }
        $dayTaskM = new DayTaskModel($this->corp_id);
        $taskTakedInfos = $dayTaskM->getTaskNameByTaskTypeAndEmployee(4,$uid);
        if(!empty($taskTakedInfos)){
            $taked_str = "";
            foreach ($taskTakedInfos as $taskTakedInfo){
                $taked_str .= "员工 ".$taskTakedInfo["truename"]." 已在 ".$taskTakedInfo["task_name"]." 任务中;";
            }
            return $taked_str;
        }
        return true;
    }
    protected function _getTaskForInput($uid){
//        var_exp($task_time,'$task_time',1);
        $task_info['task_name'] = input("task_name","","string");
        $task_info['task_start_time'] = 0;
        $task_info['task_end_time'] = 0;
        $task_info['task_take_start_time'] = 0;
        $task_info['task_take_end_time'] = 0;
        $task_info['task_type'] = 6;
        $task_info['task_method'] = 8;
        $task_info['content'] = input("content","","string");

        $task_info['public_to_take'] = "";
        $task_info['public_to_view'] = "";

        $task_info['create_employee'] = $uid;
        $task_info['create_time'] = time();
        $task_info['status'] = 2;
        return $task_info;
    }
    protected function _getTaskTargetForInput(){
        $task_target_infos = [];
        $task_target_str = input("target");
        $task_target_arr = json_decode($task_target_str,true);
        //var_exp($task_target_arr,'$task_target_arr',1);

        foreach ($task_target_arr as $task_target){
            if(!isset($task_target['target_type'])||!isset($task_target['target_num'])){
                return [];
            }
            $task_target_info['target_type'] = $task_target['target_type'];
            $task_target_info['target_num'] = $task_target['target_num'];
            $task_target_info['target_method'] = 0;

            //var_exp($task_target_info,'$task_target_info',1);
            $task_target_infos[] = $task_target_info;
        }
        return $task_target_infos;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建每日任务时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        //TODO 设置任务权限校验
        $time = time();
        $taskInfo = $this->_getTaskForInput($uid);
        $taskInfo["reward_max_num"] = 0;
        $taskTargetInfos = $this->_getTaskTargetForInput();
//        var_exp($taskTargetInfos,'$taskTargetInfos',1);
        if(empty($taskTargetInfos)){
            $result['info'] = '任务目标参数错误';
            return json($result);
        }

        $taskTakeInfos = [];
        $public_uids = explode(",",$taskInfo["public_to_take"]);
        $public_uids = array_filter($public_uids);
        $public_uids = array_unique($public_uids);
        foreach ($public_uids as $employee_id){
            $taskTakeInfos[] = [
                "take_employee"=>$employee_id,
                "take_time"=>$time
            ];
        }

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTargetM = new TaskTargetModel($this->corp_id);
        try {
            $employeeTaskM->link->startTrans();

            $taskId = $employeeTaskM->addTask($taskInfo);
            if (!$taskId) {
                exception('提交任务失败!');
            }

            foreach ($taskTargetInfos as &$taskTargetInfo) {
                $taskTargetInfo['task_id'] = $taskId;
            }
            $taskTargetId = $taskTargetM->addMutipleTaskTarget($taskTargetInfos);
            if (!$taskTargetId) {
                exception('提交任务目标失败!');
            }

            $employeeTaskM->link->commit();
            $result['data'] = $taskId;
        }catch (\Exception $ex){
            $employeeTaskM->link->rollback();
            //print_r($ex->getTrace());die();
            $result['info'] = $ex->getMessage();
            return json($result);
        }

        $result['status'] = 1;
        $result['info'] = "新建任务成功！";
        return json($result);
    }
    public function check_take(){
        if(empty($public_to_take)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $public_to_take = input("public_to_take","","string");
        $check_flg = $this->_check_take($public_to_take);
        if($check_flg!==true){
            $result['info'] = $check_flg;
            return json($result);
        }
    }
    public function update(){
        $id = input('id',0,'int');
        $public_to_take = input("public_to_take","","string");
        if(!$id||empty($public_to_take)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        //检测员工任务是否重复
        $check_flg = $this->_check_take($public_to_take);
        if($check_flg!==true){
            $result['info'] = $check_flg;
            return json($result);
        }
        $take_flg = $this->_update_employee_take($id,$public_to_take);
        if(!$take_flg){
            $this->error("");
            $result['info'] = "更新员工每日任务时发生错误！";
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "更新员工每日任务成功！";
        return json($result);
    }
    public function _update_employee_take($task_id,$public_to_take,$task_target_list=null){
        $flg = false;
        if(!is_array($public_to_take)){
            $public_to_take = explode(",",$public_to_take);
        }
        if(empty($task_target_list)){
            $task_target_list = [];//TODO $task_id
        }
        //TODO add || update

        return $flg;
    }
    public function del(){
        $result = ['status'=>0 ,'info'=>"删除每日任务失败!"];
        $task_id = input('task_id',0,"int");
        if(!$task_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $task_info["status"] = 0;
        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $flg = $employeeTaskM->setTaskInfo($task_id,$task_info);
        if(!$flg){
            $result['info'] = '删除每日任务时发生错误!';
            return json($result);
        }
        $result['info'] = '删除每日任务成功!';
        $result['status'] = 1;
        return json($result);
    }
}