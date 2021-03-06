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
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\common\model\Corporation;
use app\task\model\EmployeeTask as EmployeeTaskModel;
use app\task\model\TaskTarget as TaskTargetModel;
use app\task\model\TaskReward as TaskRewardModel;
use app\task\model\TaskTake as TaskTakeModel;
use app\task\model\TaskGuess as TaskGuessModel;
use app\task\model\TaskComment as TaskCommentModel;
use app\task\model\TaskTip as TaskTipModel;
use app\task\service\EmployeeTask as EmployeeTaskService;
use app\common\model\Structure;
use app\huanxin\service\RedEnvelope as RedEnvelopeService;
use app\huanxin\model\RedEnvelope as RedEnvelopeModel;
use app\crm\model\Customer as CustomerModel;
use think\View;

class MonthTask extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
    }
    protected function _getTaskForInput($uid){
        $task_time = input("task_time",0,"int");
//        var_exp($task_time,'$task_time',1);
        $task_info['task_name'] = input("task_name","","string");
        $task_info['task_start_time'] = mktime(0, 0, 0, date('m',$task_time), 1, date('Y',$task_time));
        $task_info['task_end_time'] = mktime(23, 59, 59, date('m',$task_time), date('t',$task_time), date('Y',$task_time));
        $task_info['task_take_start_time'] = $task_info['task_start_time'];
        $task_info['task_take_end_time'] = $task_info['task_end_time'];
        $task_info['task_type'] = 4;
        $task_info['task_method'] = 6;
        $task_info['content'] = input("content","","string");

        $task_info['public_to_take'] = input("public_to_take","","string");
        $public_uids = explode(",",$task_info['public_to_take']);
        $public_uids = array_filter($public_uids);
        $public_uids = array_unique($public_uids);
        $task_info['public_to_take'] = implode(",",$public_uids);
        $task_info['public_to_view'] = $task_info['public_to_take'];

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
        $result = ['status'=>0 ,'info'=>"新建月度任务时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
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
        if(empty($taskTakeInfos)){
            $result['info'] = '任务对象参数错误';
            return json($result);
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

            foreach ($taskTakeInfos as &$taskTakeInfo) {
                $taskTakeInfo['task_id'] = $taskId;
            }
            $taskTakeM = new TaskTakeModel($this->corp_id);
            $taskTakeId = $taskTakeM->addMutipleTaskTake($taskTakeInfos);
            if(!$taskTakeId){
                exception('提交任务参与信息失败!');
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
}