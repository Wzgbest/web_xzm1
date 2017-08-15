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
use app\task\model\TaskReward as TaskRewardModel;

class Index extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
    }
    public function show(){
    }
    public function add_page(){
    }


    public function get(){
        $result = ['status'=>0 ,'info'=>"获取任务时发生错误！"];

        $result['status'] = 1;
        $result['info'] = "获取任务成功！";
        return json($result);
    }
    protected function _getTaskForInput(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];

        $task_info['task_name'] = input("task_name","","string");
        $task_info['task_start_time'] = input("task_start_time",0,"strtotime");
        $task_info['task_end_time'] = input("task_end_time",0,"strtotime");
        $task_info['task_take_deadline'] = input("task_take_deadline",0,"strtotime");
        $task_info['task_type'] = input("task_type",0,"int");
        $task_info['task_method'] = input("task_method",0,"int");
        $task_info['content'] = input("content","","string");
        $task_info['public_to_take'] = input("public_to_take","","string");
        $task_info['public_to_view'] = input("public_to_view","","string");
        $task_info['create_employee'] = $uid;
        $task_info['create_time'] = time();
        $task_info['status'] = 1;
        return $task_info;
    }
    protected function _getTaskTargetForInput($taskId){
        $task_target_info['task_id'] = $taskId;
        $task_target_info['target_type'] = input("target_type",0,"int");
        $task_target_info['target_num'] = input("target_num",0,"int");
        $task_target_info['target_customer'] = input("target_customer",0,"int");
        $task_target_info['target_appraiser'] = input("target_appraiser","","string");
        return $task_target_info;
    }
    protected function _getTaskRewardForInput($taskId){
        $task_reward_info['task_id'] = $taskId;
        $task_reward_info['reward_type'] = input("reward_type",0,"int");
        $task_reward_info['reward_amount'] = input("reward_amount",0,"int");
        $task_reward_info['reward_num'] = input("reward_num",0,"int");
        return $task_reward_info;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建任务时发生错误！"];
        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTargetM = new TaskTargetModel($this->corp_id);
        $taskRewardM = new TaskRewardModel($this->corp_id);
        $taskInfo = $this->_getTaskForInput();
        //TODO 检验和判断

        try{
            $employeeTaskM->link->startTrans();

            $taskId = $employeeTaskM->addTask($taskInfo);
            if(!$taskId){
                exception('提交任务失败!');
            }

            $taskTargetInfo = $this->_getTaskTargetForInput($taskId);
            $taskTargetId = $taskTargetM->addTaskTaget($taskTargetInfo);
            if(!$taskTargetId){
                exception('提交任务目标失败!');
            }

            $taskRewardInfo = $this->_getTaskRewardForInput($taskId);
            $taskRewardId = $taskRewardM->addTaskTaget($taskRewardInfo);
            if(!$taskRewardId){
                exception('提交任务目标失败!');
            }

            $employeeTaskM->link->commit();
            $result['data'] = $taskId;
        }catch (\Exception $ex){
            $employeeTaskM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "新建任务成功！";
        return json($result);
    }
}