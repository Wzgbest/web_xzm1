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
use app\task\model\TaskGuess as TaskGuessModel;
use app\task\model\TaskTip as TaskTipModel;
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

            $over_time_task_list = $employeeTaskModel->getAllOverTimeTask($time);
            $all_task_infos["over_time"][$corp_id] = $over_time_task_list;
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

                        $user_info = ['corp_frozen_money'=>['exp',"corp_frozen_money - $redEnvelopeMoneys"]];
                        $user_map = ["corp_frozen_money"=>["egt",$redEnvelopeMoneys]];
                        $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$user_info,$user_map);
                        if (!$update_user) {
                            exception("更新冻结金额发生错误!");
                        }

                        $order_data = [
                            'money_type'=>2,
                            'userid'=>$taskInfo["create_employee"],
                            'take_money'=> -$redEnvelopeMoneys,
                            'status'=>1,
                            'took_time'=>$time,
                            'remark' => '任务奖励发放'
                        ];
                        $order_datas = [];
                        $order_data = [];
                        if($task_type==1) {
                            $order_data['money_type'] = 2;
                        }
                        //红包金额
                        foreach($redEnvelopeInfos as $redEnvelopeInfo){
                            $order_data = [
                                'userid'=>$taskInfo["create_employee"],
                                'take_money'=> -$redEnvelopeInfo["money"],
                                'status'=>1,
                                'took_time'=>$time,
                                'remark' => '任务奖励发放'
                            ];
                            $order_datas[] = $order_data;
                        }
                        $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
                        if (!$add_cash_rec) {
                            exception("添加任务奖励发放记录发生错误!");
                        }
                        var_exp($redEnvelopeInfos,'$redEnvelopeInfos');
                    }
                    $employeeTaskM->link->commit();
                    $success_task_ids[] = $corp_id."_".$id;
                }catch(\Exception $ex){
                    $employeeTaskM->link->rollback();
                    $error_task_ids[] = $corp_id."_".$id;
                    //print_r($ex->getTrace());
                    $output_info_str .= "ETSAOT->error:".$ex->getMessage().";";
                }
            }
        }

        foreach ($all_task_infos["over_time"] as $corp_id=>$over_time_task_list){
            if(empty($over_time_task_list)){
                continue;
            }
            $employeeTaskM = new EmployeeTaskModel($corp_id);
            $taskTargetM = new TaskTargetModel($corp_id);
            $taskRewardM = new TaskRewardModel($corp_id);
            $taskTakeM = new TaskTakeModel($corp_id);
            $employeeM = new Employee($corp_id);
            $cashM = new TakeCash($corp_id);
            $taskGuessM = new TaskGuessModel($corp_id);
            $taskTipM = new TaskTipModel($corp_id);
            $overTimeTaskIds = array_column($over_time_task_list,"id");

            $updateTaskResult = $employeeTaskM->setTaskStatus($overTimeTaskIds,2,3);
            if (!$updateTaskResult) {
                exception("更新超时任务为结算中发生错误!");
            }
            foreach ($over_time_task_list as $taskInfo){
                $id = $taskInfo["id"];
                $start_time = $taskInfo["task_start_time"];
                $end_time = $taskInfo["task_end_time"];
                $task_type = $taskInfo["task_type"];
                $task_method = $taskInfo["task_method"];
                try{
                    $employeeTaskM->link->startTrans();
                    $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
                    $uids = $taskTakeEmployeeIds;
                    //var_exp($uids,'$uids');
                    //PK和悬赏任务检测是否有人参与
                    if($task_type>2&&($task_type+count($uids))<4){
                        //没人参与,任务失败
                        $updateTaskResult = $employeeTaskM->setTaskStatus([$id],2,0);
                        $returnMoney = $taskInfo["reward_count"];

                        $order_datas = [];
                        $order_sub_data = [
                            'userid'=>$taskInfo["create_employee"],
                            'take_money'=> -$returnMoney,
                            'status'=>1,
                            'took_time'=>$time,
                            'remark' => '任务失败退回'
                        ];
                        $order_datas[] = $order_sub_data;

                        $order_add_data = [
                            'userid'=>$taskInfo["create_employee"],
                            'take_money'=> $returnMoney,
                            'status'=>1,
                            'took_time'=>$time,
                            'remark' => '任务失败退回'
                        ];
                        $order_datas[] = $order_add_data;
                        $taskGuessAndTipMoneyEmployeeIdx = [];

                        //返还打赏记录
                        $taskTipInfoList = $taskTipM->getTipMoneyList($id);
                        foreach($taskTipInfoList as $taskTipInfo){
                            $order_add_data = [
                                'userid'=>$taskTipInfo["tip_employee"],
                                'take_money'=> $taskTipInfo["tip_money"],
                                'status'=>1,
                                'took_time'=>$time,
                                'remark' => '猜输赢任务失败退回'
                            ];
                            $order_datas[] = $order_add_data;
                            $taskGuessAndTipMoneyEmployeeIdx[$taskTipInfo["tip_employee"]] += $taskTipInfo["tip_money"];
                        }
                        $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
                        if (!$add_cash_rec) {
                            exception("添加任务失败退回记录发生错误!");
                        }

                        //返还猜输赢记录
                        $taskGuessInfoList = $taskGuessM->getGuessInfoList($id);
                        foreach($taskGuessInfoList as $taskGuessInfo){
                            $order_add_data = [
                                'userid'=>$taskGuessInfo["guess_employee"],
                                'take_money'=> $taskGuessInfo["guess_money"],
                                'status'=>1,
                                'took_time'=>$time,
                                'remark' => '猜输赢任务失败退回'
                            ];
                            $order_datas[] = $order_add_data;
                            $taskGuessAndTipMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]] += $taskGuessInfo["guess_money"];
                        }

                        //返还任务金额
                        $employeeInfoMap = ["frozen_money"=>["egt",$returnMoney]];
                        $employeeInfo["frozen_money"] = ['exp',"frozen_money - $returnMoney"];
                        $employeeInfo["left_money"] = ['exp',"left_money + $returnMoney"];
                        $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$employeeInfo,$employeeInfoMap);
                        if (!$update_user) {
                            exception("更新冻结金额发生错误!");
                        }

                        //返还打赏猜输赢等用户额度
                        foreach($taskGuessAndTipMoneyEmployeeIdx as $employee_id=>$money){
                            $employeeInfo["left_money"] = ['exp',"left_money + $money"];
                            $update_user = $employeeM->setEmployeeSingleInfoById($employee_id,$employeeInfo,$employeeInfoMap);
                            if (!$update_user) {
                                exception("返还打赏猜输赢金额发生错误!");
                            }
                        }
                        continue;
                    }


                    $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
                    $target_type = $taskTarget["target_type"];
                    $standard = $taskTarget["target_num"];

                    $employeeTaskService = new EmployeeTaskService($corp_id);
                    $rankingdata = $employeeTaskService->getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$standard,20,1);

                    $haveRedEnvelopeInfo = $employeeTaskM->getStandardTaskInfoById($id);

                    //计算已经发送的红包和需要发送的红包
                    //任务红包
                    $sentRedEnvelopeMoney = array_column($haveRedEnvelopeInfo,"money");
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
                            $reward_amount = $reward_item["reward_amount"];
                            if($task_method==2){
                                $reward_amount = bcdiv($reward_amount,count($needRedEnvelopeEmployeeId),2);
                            }
                            $moreNum = 0;
                            $mulTaskMoney = bcmul($reward_amount,count($needRedEnvelopeEmployeeId),2);
                            $copmFlg = bccomp($mulTaskMoney,$reward_item["reward_amount"]);
                            if($copmFlg>0){
                                $moreNum = bcmul(bcsub($mulTaskMoney,$reward_item["reward_amount"],2),100,0);
                                $reward_amount = bcsub($reward_amount,"0.01");
                            }elseif($copmFlg<0){
                                $moreNum = bcmul(bcsub($reward_item["reward_amount"],$reward_amount,2),100,0);
                            }
                            foreach ($needRedEnvelopeEmployeeId as $key=>$value){
                                if(isset($redEnvelopeInfos[$value])){
                                    continue;
                                }
                                $idx = $key+1;
                                if(
                                    $reward_item["reward_num"]==0 ||
                                    ($reward_item["reward_start"]<=$idx&&$idx<=$reward_item["reward_end"])
                                ){
                                    //多的一分给第一名
                                    $tmp_reward_amount = ($key==0)?bcadd($reward_amount,bcmul($moreNum,"0.01",2),2):$reward_amount;

                                    $redEnvelopeInfo["redid"] = md5(time().rand(1000,9999));
                                    $redEnvelopeInfo["type"] = 3;
                                    $redEnvelopeInfo["task_id"] = $id;
                                    $redEnvelopeInfo["fromuser"] = 0;
                                    $redEnvelopeInfo["money"] = $tmp_reward_amount;
                                    $redEnvelopeInfo["create_time"] = $time;
                                    $redEnvelopeInfo["total_money"] = $tmp_reward_amount;
                                    $redEnvelopeInfo["is_token"] = 0;
                                    $redEnvelopeInfo["took_user"] = $value;
                                    $redEnvelopeInfos[$value] = $redEnvelopeInfo;
                                    $redEnvelopeMoneys += $reward_item["reward_amount"];
                                }
                            }
                        }
                        $needRedEnvelopeEmployeeNum = count($redEnvelopeInfos);
                        var_exp($redEnvelopeInfos,'$redEnvelopeInfos_task');

                        //打赏红包
                        $allTipMoney = $taskInfo["tip_count"];
                        $allHaveTipRedEnvelopeEmployeeNum = count($haveRedEnvelopeInfo)+$needRedEnvelopeEmployeeNum;
                        //计算平均红包额度
                        $tipRedEnvelopeMoney = 0;
                        if($allHaveTipRedEnvelopeEmployeeNum>0){
                            $tipRedEnvelopeMoney = bcdiv($allTipMoney,$allHaveTipRedEnvelopeEmployeeNum,2);
                        }
                        //计算比平均红包额度多一分的数量
                        $moreNum = 0;
                        $mulTipMoney = bcmul($tipRedEnvelopeMoney,$allHaveTipRedEnvelopeEmployeeNum,2);
                        $copmFlg = bccomp($mulTipMoney,$allTipMoney);
                        if($copmFlg>0){
                            $moreNum = bcmul(bcsub($mulTipMoney,$allTipMoney,2),100,0);
                            $tipRedEnvelopeMoney = bcsub($tipRedEnvelopeMoney,"0.01");
                        }elseif($copmFlg<0){
                            $moreNum = bcmul(bcsub($allTipMoney,$mulTipMoney,2),100,0);
                        }
                        //根据排行榜和多一分数量生成红包
                        for($i=0;$i<$allHaveTipRedEnvelopeEmployeeNum;$i++){
                            //多的一分先到先得
                            //$reward_amount = ($i-$moreNum>=0)?$tipRedEnvelopeMoney:bcadd($tipRedEnvelopeMoney,"0.01");
                            //多的一分给第一名
                            $reward_amount = $i==0?bcadd($tipRedEnvelopeMoney,bcmul($moreNum,"0.01",2),2):$tipRedEnvelopeMoney;

                            $redEnvelopeInfo["redid"] = md5(time().rand(1000,9999));
                            $redEnvelopeInfo["type"] = 3;
                            $redEnvelopeInfo["task_id"] = $id;
                            $redEnvelopeInfo["fromuser"] = 0;
                            $redEnvelopeInfo["money"] = $reward_amount;
                            $redEnvelopeInfo["create_time"] = $time;
                            $redEnvelopeInfo["total_money"] = $reward_amount;
                            $redEnvelopeInfo["is_token"] = 0;
                            $redEnvelopeInfo["took_user"] = $rankingdata[$i]["employee_id"];;
                            $redEnvelopeInfos[] = $redEnvelopeInfo;
                        }
                        var_exp($redEnvelopeInfos,'$redEnvelopeInfos_task_tip');
                        
                        //猜输赢红包
                        $order_datas = [];
                        if($task_type==2 && isset($rankingdata[0])){
                            $win_employee = $rankingdata[0]["employee_id"];
                            $allGuessMoney = 0;
                            $guessWinMoney = 0;
                            $guessWinFirstId = -1;
                            $guessWinFirstEmployeeId = 0;
                            $taskGuessInfoList = $taskGuessM->getGuessInfoList($id);
                            $taskGuessWinInfoList = [];
                            foreach ($taskGuessInfoList as $taskGuessInfo){
                                $allGuessMoney = bcadd($allGuessMoney,$taskGuessInfo["guess_money"],2);
                                if($taskGuessInfo["guess_take_employee"] == $win_employee){
                                    $guessWinMoney = bcadd($allGuessMoney,$taskGuessInfo["guess_money"],2);
                                    $taskGuessWinInfoList[] = $taskGuessInfo;
                                    if($guessWinFirstId==-1 || $taskGuessInfo["id"]<$guessWinFirstId){
                                        $guessWinFirstId = $taskGuessInfo["id"];
                                        $guessWinFirstEmployeeId = $taskGuessInfo["guess_employee"];
                                    }
                                }
                            }
                            if(bccomp($allGuessMoney,0)>0 && bccomp($guessWinMoney,0)>0){
                                $haveGuessRedEnvelopeList = [];
                                $haveGuessRedEnvelopeMoney = 0;
                                $baseMoney = bcdiv($allGuessMoney,$guessWinMoney,2);
                                foreach($taskGuessWinInfoList as $guessWinInfo){
                                    $haveGuessRedEnvelope["employee_id"] = $guessWinInfo["guess_employee"];
                                    $haveGuessRedEnvelope["red_envelope_money"] = bcmul($guessWinInfo["guess_money"],$baseMoney,2);
                                    $haveGuessRedEnvelopeList[] = $haveGuessRedEnvelope;
                                    $haveGuessRedEnvelopeMoney = bcadd($haveGuessRedEnvelopeMoney,$haveGuessRedEnvelope["red_envelope_money"]);
                                }

                                //计算比按比例分配的额度多的数量
                                $moreNum = 0;
                                $copmFlg = bccomp($haveGuessRedEnvelopeMoney,$allGuessMoney);
                                if($copmFlg>0){
                                    $baseMoney = bcsub($baseMoney,"0.01",2);
                                    $haveGuessRedEnvelopeList = [];
                                    foreach($taskGuessWinInfoList as $guessWinInfo){
                                        $haveGuessRedEnvelope["employee_id"] = $guessWinInfo["guess_employee"];
                                        $haveGuessRedEnvelope["red_envelope_money"] = bcmul($guessWinInfo["guess_money"],$baseMoney,2);
                                        $haveGuessRedEnvelopeList[] = $haveGuessRedEnvelope;
                                        $haveGuessRedEnvelopeMoney = bcadd($haveGuessRedEnvelopeMoney,$haveGuessRedEnvelope["red_envelope_money"]);
                                    }
                                    $moreNum = bcsub($allGuessMoney,$haveGuessRedEnvelopeMoney,2);
                                }elseif($copmFlg<0){
                                    $moreNum = bcsub($allGuessMoney,$haveGuessRedEnvelopeMoney,2);
                                }
                                foreach ($haveGuessRedEnvelopeList as $haveGuessRedEnvelope){
                                    $reward_amount = $haveGuessRedEnvelope["red_envelope_money"];
                                    if(($haveGuessRedEnvelope["employee_id"])==$guessWinFirstEmployeeId){
                                        $reward_amount = bcadd($reward_amount,$moreNum);
                                    }

                                    $redEnvelopeInfo["redid"] = md5(time().rand(1000,9999));
                                    $redEnvelopeInfo["type"] = 3;
                                    $redEnvelopeInfo["task_id"] = $id;
                                    $redEnvelopeInfo["fromuser"] = 0;
                                    $redEnvelopeInfo["money"] = $reward_amount;
                                    $redEnvelopeInfo["create_time"] = $time;
                                    $redEnvelopeInfo["total_money"] = $reward_amount;
                                    $redEnvelopeInfo["is_token"] = 0;
                                    $redEnvelopeInfo["took_user"] = $haveGuessRedEnvelope["employee_id"];;
                                    $redEnvelopeInfos[] = $redEnvelopeInfo;
                                }

                            }elseif(bccomp($guessWinMoney,0)==0){
                                //这帮人运气太差,一个也没猜中,退钱
                                $taskGuessMoneyEmployeeIdx = [];

                                //返还猜输赢记录
                                foreach($taskGuessInfoList as $taskGuessInfo){
                                    $order_add_data = [
                                        'userid'=>$taskGuessInfo["guess_employee"],
                                        'take_money'=> $taskGuessInfo["guess_money"],
                                        'status'=>1,
                                        'took_time'=>$time,
                                        'remark' => '猜输赢任务失败退回'
                                    ];
                                    $order_datas[] = $order_add_data;
                                    $taskGuessMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]] += $taskGuessInfo["guess_money"];
                                }

                                //返还打赏猜输赢等用户额度
                                foreach($taskGuessMoneyEmployeeIdx as $employee_id=>$money){
                                    $employeeInfo["left_money"] = ['exp',"left_money + $money"];
                                    $update_user = $employeeM->setEmployeeSingleInfoById($employee_id,$employeeInfo,$employeeInfoMap);
                                    if (!$update_user) {
                                        exception("返还打赏猜输赢金额发生错误!");
                                    }
                                }
                            }
                            var_exp($redEnvelopeInfos,'$redEnvelopeInfos_task_tip_guess');
                        }

                        $redEnvelopeM = new RedEnvelopeM($corp_id);
                        $res = $redEnvelopeM->createRedId($redEnvelopeInfos);
                        if (!$res) {
                            exception("保存红包信息发生错误!");
                        }

                        //红包和剩余金额对应的额度记录
                        $order_data = [];
                        if($task_type==1) {
                            $order_data['money_type'] = 2;
                        }
                        //红包金额
                        foreach($redEnvelopeInfos as $redEnvelopeInfo){
                            $order_data = [
                                'userid'=>$taskInfo["create_employee"],
                                'take_money'=> -$redEnvelopeInfo["money"],
                                'status'=>1,
                                'took_time'=>$time,
                                'remark' => '任务奖励发放'
                            ];
                            $order_datas[] = $order_data;
                        }
                        //剩余金额
                        $balances = $taskInfo["reward_count"]-$redEnvelopeMoneys-$sentRedEnvelopeMoney;
                        if($balances>0){
                            //减除冻结
                            $order_data = [
                                'userid'=>$taskInfo["create_employee"],
                                'take_money'=> -$balances,
                                'status'=>1,
                                'took_time'=>$time,
                                'remark' => '任务发放结余'
                            ];
                            $order_datas[] = $order_data;
                            //增加非冻结
                            $order_data = [
                                'userid'=>$taskInfo["create_employee"],
                                'take_money'=> $balances,
                                'status'=>1,
                                'took_time'=>$time,
                                'remark' => '任务发放结余解冻'
                            ];
                            $order_datas[] = $order_data;
                        }

                        $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
                        if (!$add_cash_rec) {
                            exception("添加任务奖励发放记录发生错误!");
                        }

                        //更新员工额度
                        $employeeMonyField = "frozen_money";
                        if($task_type==1) {
                            $employeeMonyField = "corp_".$employeeMonyField;
                        }
                        $taskMoney = $taskInfo["reward_count"]-$sentRedEnvelopeMoney;
                        $employeeInfo = [$employeeMonyField=>['exp',$employeeMonyField." - ".$taskMoney]];
                        $employeeInfoMap = [$employeeMonyField=>["egt",$taskMoney]];
                        $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$employeeInfo,$employeeInfoMap);
                        if (!$update_user) {
                            exception("更新冻结金额发生错误!");
                        }

                        var_exp($redEnvelopeInfos,'$redEnvelopeInfos');
                    }

                    $updateTaskStatus = $employeeTaskM->setTaskStatus([$id],3,5);
                    if (!$updateTaskStatus) {
                        exception("更新任务状态发生错误!");
                    }

                    $employeeTaskM->link->commit();
                    $success_task_ids[] = $corp_id."_".$id;
                }catch(\Exception $ex){
                    $employeeTaskM->link->rollback();
                    $error_task_ids[] = $corp_id."_".$id;
                    //print_r($ex->getTrace());
                    $output_info_str .= "ETSAOT->error:".$ex->getMessage().";";
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