<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Corporation as CorporationModel;
use app\huanxin\model\RedEnvelope;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\task\model\EmployeeTask as EmployeeTaskModel;
use app\task\model\TaskTarget as TaskTargetModel;
use app\task\model\TaskReward as TaskRewardModel;
use app\task\model\TaskTake as TaskTakeModel;
use app\task\service\EmployeeTask as EmployeeTaskService;
use app\huanxin\model\RedEnvelope as RedEnvelopeM;

class EmployeeTask extends Command{
    protected function configure(){
        $this->setName('employee_task')->setDescription('check over time task and standard employee');
    }

    protected function execute(Input $input, Output $output){
        $output_info_str = '';
        $time = time();
        $corpIds = CorporationModel::getAllCorpIds();
        $all_task_infos = [];
        $success_task_ids = [];
        $error_task_ids = [];
        foreach ($corpIds as $corpIdArr){
            $corp_id = $corpIdArr["corp_id"];
            $employeeTaskModel = new EmployeeTaskModel($corp_id);
            $standard_task_ids = $employeeTaskModel->getAllStandardTaskId($time);
            $all_task_infos["standard"][$corp_id] = $standard_task_ids;

            $over_time_task_ids = $employeeTaskModel->getAllOverTimeTaskId($time);
            $all_task_infos["over_time"][$corp_id] = $over_time_task_ids;
        }
        //var_exp($all_task_infos,'$all_task_infos');

        foreach ($all_task_infos["standard"] as $corp_id=>$standard_task_ids){
            foreach ($standard_task_ids as $standard_task_id){
                $id = $standard_task_id;
                $employeeTaskM = new EmployeeTaskModel($corp_id);
                $taskTargetM = new TaskTargetModel($corp_id);
                $taskRewardM = new TaskRewardModel($corp_id);
                $taskTakeM = new TaskTakeModel($corp_id);
                try{
                    $employeeTaskM->link->startTrans();
                    //var_exp($corp_id,'$corp_id');
                    //var_exp($standard_task_id,'$standard_task_id');
                    $taskInfo = $employeeTaskM->getTaskInfo($id);
                    //var_exp($taskInfo,'$taskInfo',1);
                    if(empty($taskInfo)){
                        continue;
                    }
                    $start_time = $taskInfo["task_start_time"];
                    $end_time = $taskInfo["task_end_time"];
                    $task_type = $taskInfo["task_type"];
                    $task_method = $taskInfo["task_method"];
    
                    $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
                    $uids = $taskTakeEmployeeIds;
                    //var_exp($uids,'$uids');
                    if($task_type>2){
                        continue;
                    }
    
                    $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
                    $target_type = $taskTarget["target_type"];
                    $standard = $taskTarget["target_num"];
    
                    $employeeTaskService = new EmployeeTaskService($corp_id);
                    $rankingdata = $employeeTaskService->getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$standard,20,1);
    
                    $haveRedEnvelopeInfo = $employeeTaskM->getStandardTaskInfoById($id);

                    $needRedEnvelopeEmployeeId = [];
                    foreach ($rankingdata as $key=>$rankingitem){
                        if($rankingitem["is_standard"] && !isset($haveRedEnvelopeInfo[$rankingitem["employee_id"]])){
                            $needRedEnvelopeEmployeeId[$key] = $rankingitem["employee_id"];
                        }
                    }
                    var_exp($needRedEnvelopeEmployeeId,'$needRedEnvelopeEmployeeId');

                    if(!empty($needRedEnvelopeEmployeeId)){
                        $taskReward = $taskRewardM->getTaskRewardListByTaskId($id);
                        //var_exp($taskReward,'$taskReward');
                        $redEnvelopeInfos = [];
                        $redEnvelopeMoneys = 0;
                        foreach ($taskReward as $reward_item){
                            foreach ($needRedEnvelopeEmployeeId as $key=>$value){
                                if(isset($redEnvelopeInfos[$value])){
                                    continue;
                                }
                                $idx = $key+1;
                                if($reward_item["reward_start"]<=$idx&&$idx<=$reward_item["reward_end"]){
                                    $redEnvelopeInfo["redid"] = md5(time().rand(1000,9999));
                                    $redEnvelopeInfo["type"] = 3;
                                    $redEnvelopeInfo["task_id"] = $id;
                                    $redEnvelopeInfo["fromuser"] = 0;
                                    $redEnvelopeInfo["money"] = $reward_item["reward_amount"];
                                    $redEnvelopeInfo["create_time"] = $time;
                                    $redEnvelopeInfo["total_money"] = $reward_item["reward_amount"];
                                    $redEnvelopeInfo["is_token"] = 0;
                                    $redEnvelopeInfo["took_user"] = $value;
                                    $redEnvelopeInfos[$value] = $redEnvelopeInfo;
                                    $redEnvelopeMoneys += $reward_item["reward_amount"];
                                }
                            }
                        }
                        $redEnvelopeM = new RedEnvelopeM($corp_id);
                        $employeeM = new Employee($corp_id);
                        $cashM = new TakeCash($corp_id);
                        $res = $redEnvelopeM->createRedId($redEnvelopeInfos);
                        if (!$res) {
                            exception("保存红包信息发生错误!");
                        }

                        $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],['left_money'=>['exp',"corp_frozen_money - $redEnvelopeMoneys"]],["corp_frozen_money"=>["egt",$redEnvelopeMoneys]]);
                        if (!$update_user) {
                            exception("更新冻结金额发生错误!");
                        }

                        $order_data = [
                            'userid'=>$taskInfo["create_employee"],
                            'take_money'=> -$redEnvelopeMoneys,
                            'status'=>1,
                            'took_time'=>$time,
                            'remark' => '打赏用户'
                        ];
                        $add_cash_rec = $cashM->addOrderNumber($order_data);
                        if (!$add_cash_rec) {
                            exception("添加打赏交易记录发生错误!");
                        }
                        var_exp($redEnvelopeInfos,'$redEnvelopeInfos');
                        $success_task_ids[] = $id;
                    }
                    $employeeTaskM->link->commit();
                }catch(\Exception $ex){
                    $employeeTaskM->link->rollback();
                    //print_r($ex->getTrace());
                    $output_info_str .= "ETSAOT->error:".$ex->getMessage().";";
                }
            }
        }

        foreach ($all_task_infos["over_time"] as $corp_id=>$over_time_task_id){
            $employeeTaskM = new EmployeeTaskModel($corp_id);
            $overTimeTaskInfo = $employeeTaskM->getAllOverTimeTask($time);
            if(!empty($overTimeTaskId)){
                $overTimeTaskIds = array_column($overTimeTaskInfo,"id");
                try{
                    $employeeTaskM->link->startTrans();

                    $updateTaskResult = $employeeTaskM->setTaskStatus($overTimeTaskIds,2,3);
                    if (!$updateTaskResult) {
                        exception("更新超时任务为结算中发生错误!");
                    }

                    $employeeTaskM->link->commit();
                }catch(\Exception $ex){
                    $employeeTaskM->link->rollback();
                }
            }
        }

        $output_info_str .= "ETSAOT->time:".$time.";success:".count($success_task_ids).";error:".count($error_task_ids).";";

        $trace_info_str = '';
        $trace_info_str .= var_exp($all_task_infos,'$all_task_infos','return',false).";";
        $trace_info_str .= var_exp($success_task_ids,'$success_task_ids','return',false).";";
        $trace_info_str .= var_exp($error_task_ids,'$error_task_ids','return',false).";";
        $trace_info_str .= "\r\n".$output_info_str;
        trace($trace_info_str);

        $output->writeln($output_info_str);
    }
}