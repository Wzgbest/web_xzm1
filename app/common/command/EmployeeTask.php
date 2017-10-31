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
        var_exp($all_task_infos,'$all_task_infos');

        echo "standard\r\n";
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
                    $pay_type = $taskInfo["pay_type"];
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
                    $rankingdata = $employeeTaskService->getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$id,$standard,20,1);

                    $haveRedEnvelopeInfo = $employeeTaskM->getStandardTaskInfoById($id);

                    $needRedEnvelopeEmployeeId = [];
                    foreach ($rankingdata as $key=>$rankingitem){
                        if($rankingitem["is_standard"] && !isset($haveRedEnvelopeInfo[$rankingitem["employee_id"]])){
                            $needRedEnvelopeEmployeeId[$key] = $rankingitem["employee_id"];
                        }
                    }
                    var_exp($needRedEnvelopeEmployeeId,'$needRedEnvelopeEmployeeId_standard');

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

                        $redEnvelopeMoneys = bcmul($redEnvelopeMoneys,100,0);
                        $user_info = ['corp_frozen_money'=>['exp',"corp_frozen_money - ".$redEnvelopeMoneys]];
                        $user_map = ["corp_frozen_money"=>["egt",$redEnvelopeMoneys]];
                        $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$user_info,$user_map);
                        if (!$update_user) {
                            exception("更新发放后冻结金额发生错误!");
                        }

                        $order_datas = [];
                        //红包金额
                        foreach($redEnvelopeInfos as $redEnvelopeInfo){
                            $order_data = [
                                'userid'=>$taskInfo["create_employee"],
                                'take_money'=> -$redEnvelopeInfo["money"],
                                'take_status'=>1,
                                'took_time'=>$time,
                                'remark' => '任务奖励发放',
                                'status'=>1
                            ];
                            if($pay_type==1) {
                                $order_data['money_type'] = 2;
                            }
                            $order_datas[] = $order_data;
                        }
                        $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
                        if (!$add_cash_rec) {
                            exception("添加任务奖励发放记录发生错误!");
                        }
                        var_exp($redEnvelopeInfos,'$redEnvelopeInfos_standard');
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

        echo "over_time\r\n";
        foreach ($all_task_infos["over_time"] as $corp_id=>$over_time_task_list){
            if(empty($over_time_task_list)){
                continue;
            }
            var_exp($corp_id,'$corp_id');
            $employeeTaskM = new EmployeeTaskModel($corp_id);
            $taskTargetM = new TaskTargetModel($corp_id);
            $taskRewardM = new TaskRewardModel($corp_id);
            $taskTakeM = new TaskTakeModel($corp_id);
            $employeeM = new Employee($corp_id);
            $cashM = new TakeCash($corp_id);
            $taskGuessM = new TaskGuessModel($corp_id);
            $taskTipM = new TaskTipModel($corp_id);
            //$overTimeTaskIds = array_column($over_time_task_list,"id");

            foreach ($over_time_task_list as $taskInfo){
                var_exp($taskInfo,'$taskInfo_over_time');
                $id = $taskInfo["id"];
                $start_time = $taskInfo["task_start_time"];
                $end_time = $taskInfo["task_end_time"];
                $task_type = $taskInfo["task_type"];
                $pay_type = $taskInfo["pay_type"];
                $task_method = $taskInfo["task_method"];
                try{
                    $employeeTaskM->link->startTrans();
                    $updateTaskResult = $employeeTaskM->setTaskStatus([$id],2,3);
                    if (!$updateTaskResult) {
                        exception("更新超时任务为结算中发生错误!");
                    }
                    $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
                    $uids = $taskTakeEmployeeIds;
                    //var_exp($uids,'$uids');
                    //PK和悬赏任务检测是否有人参与
                    if($task_type>2&&($task_type+count($uids))<4){
                        //没人参与,任务失败
                        $updateTaskResult = $employeeTaskM->setTaskStatus([$id],2,0);
                        $returnMoney = $taskInfo["reward_count"];

                        $order_datas = [];

                        $order_add_data = [
                            'userid'=>$taskInfo["create_employee"],
                            'take_money'=> $returnMoney,
                            'take_status'=>1,
                            'took_time'=>$time,
                            'remark' => '任务失败退回',
                            'status'=>1,
                            "money_type"=>1
                        ];
                        $order_datas[] = $order_add_data;
                        $taskGuessAndTipMoneyEmployeeIdx = [];

                        //返还打赏记录
                        $taskTipInfoList = $taskTipM->getTipMoneyList($id);
                        foreach($taskTipInfoList as $taskTipInfo){
                            $order_add_data = [
                                'userid'=>$taskTipInfo["tip_employee"],
                                'take_money'=> $taskTipInfo["tip_money"],
                                'take_status'=>1,
                                'took_time'=>$time,
                                'remark' => '猜输赢任务失败退回',
                                'status'=>1,
                                "money_type"=>1
                            ];
                            $order_datas[] = $order_add_data;
                            if(isset($taskGuessAndTipMoneyEmployeeIdx[$taskTipInfo["tip_employee"]])){
                                $taskGuessAndTipMoneyEmployeeIdx[$taskTipInfo["tip_employee"]] += $taskTipInfo["tip_money"];
                            }else{
                                $taskGuessAndTipMoneyEmployeeIdx[$taskTipInfo["tip_employee"]] = $taskTipInfo["tip_money"];
                            }
                        }

                        //返还猜输赢记录
                        $taskGuessInfoList = $taskGuessM->getGuessInfoList($id);
                        foreach($taskGuessInfoList as $taskGuessInfo){
                            $order_add_data = [
                                'userid'=>$taskGuessInfo["guess_employee"],
                                'take_money'=> $taskGuessInfo["guess_money"],
                                'take_status'=>1,
                                'took_time'=>$time,
                                'remark' => '猜输赢任务失败退回',
                                'status'=>1,
                                "money_type"=>1
                            ];
                            $order_datas[] = $order_add_data;
                            $taskGuessAndTipMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]] += $taskGuessInfo["guess_money"];
                        }

                        var_exp($order_datas,'$order_datas_task_fail');
                        $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
                        if (!$add_cash_rec) {
                            exception("添加任务失败退回记录发生错误!");
                        }

                        //返还任务金额
                        $returnMoney = bcmul($returnMoney,100,0);
                        $employeeInfoMap = ["frozen_money"=>["egt",$returnMoney]];
                        $employeeInfo=[];
                        $employeeInfo["frozen_money"] = ['exp',"frozen_money - $returnMoney"];
                        $employeeInfo["left_money"] = ['exp',"left_money + ".$returnMoney];
                        $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$employeeInfo,$employeeInfoMap);
                        if (!$update_user) {
                            exception("更新返还任务冻结金额发生错误!");
                        }

                        //返还打赏猜输赢等用户额度
                        foreach($taskGuessAndTipMoneyEmployeeIdx as $employee_id=>$money){
                            $employeeInfo=[];
                            $employeeInfo["left_money"] = ['exp',"left_money + ".bcmul($money,100,0)];
                            $update_user = $employeeM->setEmployeeSingleInfoById($employee_id,$employeeInfo,$employeeInfoMap);
                            if (!$update_user) {
                                exception("返还打赏猜输赢金额发生错误!");
                            }
                        }
                        $employeeTaskM->link->commit();
                        continue;
                    }


                    $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
                    $target_type = $taskTarget["target_type"];
                    $standard = $taskTarget["target_num"];

                    //有任务红包的员工
                    $rankingdata = [];
                    $needRedEnvelopeEmployeeId = [];
                    $haveRedEnvelopeNum = 0;
                    $sentRedEnvelopeMoney = 0;

                    if($task_type<3) {
                        $employeeTaskService = new EmployeeTaskService($corp_id);
                        $rankingdata = $employeeTaskService->getRankingList($target_type, $task_method, $start_time, $end_time, $uids,$id, $standard, 20, 1);
                        var_exp($rankingdata, '$rankingdata');

                        if($task_type==1) {
                            $haveRedEnvelopeInfo = $employeeTaskM->getStandardTaskInfoById($id);
                            var_exp($haveRedEnvelopeInfo, '$haveRedEnvelopeInfo');

                            foreach ($haveRedEnvelopeInfo as $haveRedEnvelopeEmployee) {
                                if (!empty($haveRedEnvelopeEmployee["redid"])) {
                                    $haveRedEnvelopeNum++;
                                    $sentRedEnvelopeMoney = $sentRedEnvelopeMoney + $haveRedEnvelopeEmployee["money"];
                                }
                            }
                            var_exp($haveRedEnvelopeNum, '$haveRedEnvelopeNum');
                            var_exp($sentRedEnvelopeMoney, '$sentRedEnvelopeMoney');

                            //计算已经发送的红包和需要发送的红包
                            foreach ($rankingdata as $key => $rankingitem) {
                                if (!isset($haveRedEnvelopeInfo[$rankingitem["employee_id"]])) {
                                    continue;
                                }
                                if ($rankingitem["is_standard"] && empty($haveRedEnvelopeInfo[$rankingitem["employee_id"]]["redid"])) {
                                    $needRedEnvelopeEmployeeId[$key] = $rankingitem["employee_id"];
                                }
                            }
                        }elseif ($task_type==2){
                            if(isset($rankingdata[0])){
                                $needRedEnvelopeEmployeeId[0] = $rankingdata[0]["employee_id"];
                            }
                        }
                    }elseif ($task_type==3){
                        $takeList = $taskTakeM->getTaskTakeListByTaskId($id);
                        $needRedEnvelopeEmployeeId = [];
                        $deadline = strtotime("-3 days");
                        var_exp($deadline, '$deadline');
                        if($taskInfo['task_end_time']<$deadline){
                            for($i=0;$i<count($takeList);$i++) {
                                if ($takeList[$i]["whether_help"] >= 0) {
                                    if($takeList[$i]["whether_help"]==0){
                                        var_exp($takeList[$i], '$takeList[$i]');
                                        $toHelpFlg = $taskTakeM->toHelp(["id"=>$takeList[$i]["id"]]);
                                        if(!$toHelpFlg){
                                            exception("系统自动判定已帮发生错误!");
                                        }
                                    }
                                    $needRedEnvelopeEmployeeId[] = $takeList[$i]["take_employee"];
                                    $rankingdata[] = ["employee_id" => $takeList[$i]["take_employee"]];
                                }
                            }
                        }else{
                            $select = array_column($takeList,"whether_help");
                            $hav_not_select = in_array(0,$select);
                            if($hav_not_select){
                                var_exp($select, '$select');
                                break;
                            }else{
                                for($i=0;$i<count($takeList);$i++) {
                                    if ($takeList[$i]["whether_help"] == 1) {
                                        $needRedEnvelopeEmployeeId[] = $takeList[$i]["take_employee"];
                                        $rankingdata[] = ["employee_id" => $takeList[$i]["take_employee"]];
                                    }
                                }
                            }
                        }
                    }
                    var_exp($needRedEnvelopeEmployeeId, '$needRedEnvelopeEmployeeId_over_time');

                    $order_datas = [];
                    $redEnvelopeInfos = [];
                    $redEnvelopeMoneys = 0;
                    $needRedEnvelopeEmployeeNum = 0;
                    $taskReward = $taskRewardM->getTaskRewardListByTaskId($id);
                    //var_exp($taskReward,'$taskReward');
                    if(!empty($needRedEnvelopeEmployeeId)){
                        if ($task_type==2){
                            $redEnvelopeInfo["redid"] = md5(time().rand(1000,9999));
                            $redEnvelopeInfo["type"] = 3;
                            $redEnvelopeInfo["task_id"] = $id;
                            $redEnvelopeInfo["fromuser"] = 0;
                            $redEnvelopeInfo["money"] = $taskInfo["reward_count"];
                            $redEnvelopeInfo["create_time"] = $time;
                            $redEnvelopeInfo["total_money"] = $taskInfo["reward_count"];
                            $redEnvelopeInfo["is_token"] = 0;
                            $redEnvelopeInfo["took_user"] = $needRedEnvelopeEmployeeId[0];
                            $redEnvelopeInfos[] = $redEnvelopeInfo;
                            $redEnvelopeMoneys += $taskInfo["reward_count"];
                        }else{
                            foreach ($taskReward as $reward_item){
                                $reward_amount = $reward_item["reward_amount"];
                                $moreNum = 0;
                                if($reward_item["reward_type"]==1){
                                    $reward_amount = bcdiv($reward_amount,count($needRedEnvelopeEmployeeId),2);
                                    var_exp($reward_amount,'$reward_amount');
                                    $mulTaskMoney = bcmul($reward_amount,count($needRedEnvelopeEmployeeId),2);
                                    var_exp($mulTaskMoney,'$mulTaskMoney');
                                    $copmFlg = bccomp($mulTaskMoney,$reward_item["reward_amount"]);
                                    var_exp($copmFlg,'$copmFlg');
                                    if($copmFlg>0){
                                        $moreNum = bcmul(bcsub($mulTaskMoney,$reward_item["reward_amount"],2),100,0);
                                        $reward_amount = bcsub($reward_amount,"0.01");
                                    }elseif($copmFlg<0){
                                        $moreNum = bcmul(bcsub($reward_item["reward_amount"],$mulTaskMoney,2),100,0);
                                    }
                                }
                                var_exp($reward_amount,'$reward_amount');
                                var_exp($moreNum,'$moreNum');
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
                                        $tmp_reward_amount = 0;
                                        if($reward_item["reward_type"]==1) {
                                            $tmp_reward_amount = ($key == 0) ? bcadd($reward_amount, bcmul($moreNum, "0.01", 2), 2) : $reward_amount;
                                        }else{
                                            $tmp_reward_amount = $reward_amount;
                                        }
                                        $redEnvelopeInfo["redid"] = md5(time().rand(1000,9999));
                                        $redEnvelopeInfo["type"] = 3;
                                        $redEnvelopeInfo["task_id"] = $id;
                                        $redEnvelopeInfo["fromuser"] = 0;
                                        $redEnvelopeInfo["money"] = $tmp_reward_amount;
                                        $redEnvelopeInfo["create_time"] = $time;
                                        $redEnvelopeInfo["total_money"] = $tmp_reward_amount;
                                        $redEnvelopeInfo["is_token"] = 0;
                                        $redEnvelopeInfo["took_user"] = $value;
                                        $redEnvelopeInfos[] = $redEnvelopeInfo;
                                        $redEnvelopeMoneys += $tmp_reward_amount;
                                    }
                                }
                            }
                        }
                        $needRedEnvelopeEmployeeNum = count($redEnvelopeInfos);
                        var_exp($needRedEnvelopeEmployeeNum,'$needRedEnvelopeEmployeeNum');
                        var_exp($redEnvelopeInfos,'$redEnvelopeInfos_task');
                    }

                    //打赏奖励
                    $allHaveTipRewardEmployeeNum = $haveRedEnvelopeNum+$needRedEnvelopeEmployeeNum;
                    $allTipMoney = $taskInfo["tip_count"];
                    if($allTipMoney>0){
                        if($allHaveTipRewardEmployeeNum>0){
                            //计算打赏平均奖励额度
                            $tipRewardMoney = bcdiv($allTipMoney,$allHaveTipRewardEmployeeNum,2);
                            //计算比平均奖励额度多一分的数量
                            $moreNum = 0;
                            $mulTipMoney = bcmul($tipRewardMoney,$allHaveTipRewardEmployeeNum,2);
                            $copmFlg = bccomp($mulTipMoney,$allTipMoney);
                            if($copmFlg>0){
                                $moreNum = bcmul(bcsub($mulTipMoney,$allTipMoney,2),100,0);
                                $tipRewardMoney = bcsub($tipRewardMoney,"0.01");
                            }elseif($copmFlg<0){
                                $moreNum = bcmul(bcsub($allTipMoney,$mulTipMoney,2),100,0);
                            }
                            $tipRewardInfos = [];
                            //根据排行榜和多一分数量生成奖励
                            for($i=0;$i<$allHaveTipRewardEmployeeNum;$i++){
                                //多一分的先到先得
                                //$reward_amount = ($i-$moreNum>=0)?$tipRewardMoney:bcadd($tipRewardMoney,"0.01");
                                //多一分的全给第一名
                                $reward_amount = $i==0?bcadd($tipRewardMoney,bcmul($moreNum,"0.01",2),2):$tipRewardMoney;
                                $have_employee = $rankingdata[$i]["employee_id"];

                                //调试信息数组
                                $tipRewardInfo["money"] = $reward_amount;
                                $tipRewardInfo["have_user"] = $have_employee;
                                $tipRewardInfos[] = $tipRewardInfo;

                                $order_add_data = [
                                    'userid'=>$have_employee,
                                    'take_money'=> bcmul($reward_amount,100,0),
                                    'take_status'=>1,
                                    'took_time'=>$time,
                                    'remark' => '参与任务获得打赏奖励',
                                    'status'=>1,
                                    'money_type'=>1
                                ];
                                $order_datas[] = $order_add_data;

                                $employeeInfo=[];
                                $employeeInfo["left_money"] = ['exp',"left_money + ".bcmul($reward_amount,100,0)];
                                $update_user = $employeeM->setEmployeeSingleInfoById($have_employee,$employeeInfo);
                                if (!$update_user) {
                                    exception("发放打赏奖励发生错误!");
                                }
                            }
                            var_exp($tipRewardInfos,'$tipRewardInfos');
                        }else{
                            //没人获得打赏奖励,退钱
                            $taskTipMoneyEmployeeIdx = [];
                            $TipModel = new TaskTipModel($corp_id);
                            $tipEmployeeList = $TipModel->getTipList($id);
                            foreach ($tipEmployeeList as $tipInfo){
                                $order_add_data = [
                                    'userid' => $tipInfo["tip_employee"],
                                    'take_money' => bcmul($tipInfo["tip_money"],100,0),
                                    'take_status' => 1,
                                    'took_time' => $time,
                                    'remark' => '打赏任务失败退回',
                                    'status'=>1,
                                    'money_type' => 1
                                ];
                                $order_datas[] = $order_add_data;

                                if (isset($taskTipMoneyEmployeeIdx[$tipInfo["tip_employee"]])) {
                                    $taskTipMoneyEmployeeIdx[$tipInfo["tip_employee"]] += $tipInfo["tip_money"];
                                } else {
                                    $taskTipMoneyEmployeeIdx[$tipInfo["tip_employee"]] = $tipInfo["tip_money"];
                                }
                            }
                            //var_exp($taskTipMoneyEmployeeIdx,'$taskTipMoneyEmployeeIdx',1);
                            //返还打赏等用户额度
                            foreach ($taskTipMoneyEmployeeIdx as $employee_id => $money) {
                                $employeeInfo=[];
                                $employeeInfo["left_money"] = ['exp', "left_money + ".bcmul($money,100,0)];
                                $update_user = $employeeM->setEmployeeSingleInfoById($employee_id, $employeeInfo);
                                if (!$update_user) {
                                    exception("返还打赏金额发生错误!");
                                }
                            }
                        }
                    }

                    //猜输赢红包
                    if($task_type==2) {
                        $taskGuessInfoList = $taskGuessM->getGuessInfoList($id);
                        if(!empty($taskGuessInfoList)){
                            $guess_success = false;
                            if (isset($rankingdata[0])){
                                $win_employee = $rankingdata[0]["employee_id"];
                                $allGuessMoney = 0;
                                $guessWinMoney = 0;
                                $guessWinFirstId = -1;
                                $guessWinFirstEmployeeId = 0;
                                $taskGuessWinInfoList = [];
                                foreach ($taskGuessInfoList as $taskGuessInfo) {
                                    $allGuessMoney = bcadd($allGuessMoney, $taskGuessInfo["guess_money"], 2);
                                    if ($taskGuessInfo["guess_take_employee"] == $win_employee) {
                                        $guessWinMoney = bcadd($allGuessMoney, $taskGuessInfo["guess_money"], 2);
                                        $taskGuessWinInfoList[] = $taskGuessInfo;
                                        if ($guessWinFirstId == -1 || $taskGuessInfo["id"] < $guessWinFirstId) {
                                            $guessWinFirstId = $taskGuessInfo["id"];
                                            $guessWinFirstEmployeeId = $taskGuessInfo["guess_employee"];
                                        }
                                    }
                                }
                                if (bccomp($allGuessMoney, 0) > 0 && bccomp($guessWinMoney, 0) > 0) {
                                    $guess_success = true;
                                    $haveGuessRedEnvelopeList = [];
                                    $haveGuessRedEnvelopeMoney = 0;
                                    $baseMoney = bcdiv($allGuessMoney, $guessWinMoney, 2);
                                    foreach ($taskGuessWinInfoList as $guessWinInfo) {
                                        $haveGuessRedEnvelope["employee_id"] = $guessWinInfo["guess_employee"];
                                        $haveGuessRedEnvelope["red_envelope_money"] = bcmul($guessWinInfo["guess_money"], $baseMoney, 2);
                                        $haveGuessRedEnvelopeList[] = $haveGuessRedEnvelope;
                                        $haveGuessRedEnvelopeMoney = bcadd($haveGuessRedEnvelopeMoney, $haveGuessRedEnvelope["red_envelope_money"]);
                                    }

                                    //计算比按比例分配的额度多的数量
                                    $moreNum = 0;
                                    $copmFlg = bccomp($haveGuessRedEnvelopeMoney, $allGuessMoney);
                                    if ($copmFlg > 0) {
                                        $baseMoney = bcsub($baseMoney, "0.01", 2);
                                        $haveGuessRedEnvelopeList = [];
                                        foreach ($taskGuessWinInfoList as $guessWinInfo) {
                                            $haveGuessRedEnvelope["employee_id"] = $guessWinInfo["guess_employee"];
                                            $haveGuessRedEnvelope["red_envelope_money"] = bcmul($guessWinInfo["guess_money"], $baseMoney, 2);
                                            $haveGuessRedEnvelopeList[] = $haveGuessRedEnvelope;
                                            $haveGuessRedEnvelopeMoney = bcadd($haveGuessRedEnvelopeMoney, $haveGuessRedEnvelope["red_envelope_money"]);
                                        }
                                        $moreNum = bcsub($allGuessMoney, $haveGuessRedEnvelopeMoney, 2);
                                    } elseif ($copmFlg < 0) {
                                        $moreNum = bcsub($allGuessMoney, $haveGuessRedEnvelopeMoney, 2);
                                    }
                                    foreach ($haveGuessRedEnvelopeList as $haveGuessRedEnvelope) {
                                        $reward_amount = $haveGuessRedEnvelope["red_envelope_money"];
                                        if (($haveGuessRedEnvelope["employee_id"]) == $guessWinFirstEmployeeId) {
                                            $reward_amount = bcadd($reward_amount, $moreNum);
                                        }

                                        $redEnvelopeInfo["redid"] = md5(time() . rand(1000, 9999));
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
                                }
                                var_exp($redEnvelopeInfos, '$redEnvelopeInfos_task_tip_guess');
                            }
                            if(!$guess_success){
                                //这帮人运气太差,一个也没猜中,退钱
                                $taskGuessMoneyEmployeeIdx = [];

                                //返还猜输赢记录
                                foreach ($taskGuessInfoList as $taskGuessInfo) {
                                    $order_add_data = [
                                        'userid' => $taskGuessInfo["guess_employee"],
                                        'take_money' => bcmul($taskGuessInfo["guess_money"],100,0),
                                        'take_status' => 1,
                                        'took_time' => $time,
                                        'remark' => '猜输赢任务失败退回',
                                        'status'=>1,
                                        'money_type' => 1
                                    ];
                                    $order_datas[] = $order_add_data;
                                    if (isset($taskGuessMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]])) {
                                        $taskGuessMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]] += $taskGuessInfo["guess_money"];
                                    } else {
                                        $taskGuessMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]] = $taskGuessInfo["guess_money"];
                                    }
                                }
                                //var_exp($taskGuessMoneyEmployeeIdx,'$taskGuessMoneyEmployeeIdx',1);
                                //返还猜输赢等用户额度
                                foreach ($taskGuessMoneyEmployeeIdx as $employee_id => $money) {
                                    $employeeInfo=[];
                                    $employeeInfo["left_money"] = ['exp', "left_money + ".bcmul($money,100,0)];
                                    $update_user = $employeeM->setEmployeeSingleInfoById($employee_id, $employeeInfo);
                                    if (!$update_user) {
                                        exception("返还猜输赢金额发生错误!");
                                    }
                                }
                            }
                        }
                    }

                    var_exp($redEnvelopeInfos,'$redEnvelopeInfos_task_save');
                    if(!empty($redEnvelopeInfos)){
                        $redEnvelopeM = new RedEnvelopeM($corp_id);
                        $res = $redEnvelopeM->createRedId($redEnvelopeInfos);
                        if (!$res) {
                            exception("保存红包信息发生错误!");
                        }
                    }

                    //红包和剩余金额对应的额度记录
                    //红包添加发放交易记录
                    foreach($redEnvelopeInfos as $redEnvelopeInfo){
                        $order_data = [
                            'userid'=>$taskInfo["create_employee"],
                            'take_money'=> "-".bcmul($redEnvelopeInfo["money"],100,0),
                            'take_status'=>1,
                            'took_time'=>$time,
                            'remark' => '任务奖励发放',
                            'status'=>1
                        ];
                        if($pay_type==1) {
                            $order_data['money_type'] = 2;
                        }else{
                            $order_data["money_type"] = 1;
                        }
                        $order_datas[] = $order_data;
                    }

                    //剩余金额处理
                    if($allHaveTipRewardEmployeeNum>0) {
                        echo '有发放,计算结余金额';
                        $balances = $taskInfo["reward_count"]-$redEnvelopeMoneys-$sentRedEnvelopeMoney;
                        var_exp($redEnvelopeMoneys,'$redEnvelopeMoneys');
                        var_exp($sentRedEnvelopeMoney,'$sentRedEnvelopeMoney');
                        var_exp($balances,'$balances');
                        if($balances>0){
                            //增加非冻结
                            $balances = bcmul($balances,100,0);
                            $order_data = [
                                'userid' => $taskInfo["create_employee"],
                                'take_money' => $balances,
                                'take_status' => 1,
                                'took_time' => $time,
                                'remark' => '任务发放结余',
                                'status'=>1
                            ];
                            if ($task_type == 1) {
                                $order_data["money_type"] = 2;
                            } else {
                                $order_data["money_type"] = 1;
                            }
                            $order_datas[] = $order_data;

                            $employeeMonyField = "left_money";
                            if($pay_type==1) {
                                $employeeMonyField = "corp_".$employeeMonyField;
                            }
                            $employeeInfo=[];
                            $employeeInfo = [$employeeMonyField=>['exp',$employeeMonyField." + ".$balances]];
                            $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$employeeInfo);
                            if (!$update_user) {
                                exception("任务返还金额更新发生错误!");
                            }
                            echo '退回任务结余金额';
                        }
                    }else{
                        //任务没人获得奖励,退回
                        if($task_type==2) {
                            var_exp($taskReward,'$taskReward');
                            if(isset($taskReward[0])){
                                $money = $taskReward[0]["reward_amount"];
                                foreach($taskTakeEmployeeIds as $taskTakeEmployeeId){
                                    //增加非冻结
                                    $order_data = [
                                        'userid' => $taskTakeEmployeeId,
                                        'take_money' => bcmul($money,100,0),
                                        'take_status' => 1,
                                        'took_time' => $time,
                                        'remark' => '任务失败退回',
                                        'status'=>1,
                                        "money_type"=>1
                                    ];
                                    $order_datas[] = $order_data;

                                    $employeeMonyField = "left_money";
                                    $employeeInfo=[];
                                    $employeeInfo = [$employeeMonyField=>['exp',$employeeMonyField." + ".bcmul($money,100,0)]];
                                    $update_user = $employeeM->setEmployeeSingleInfoById($taskTakeEmployeeId,$employeeInfo);
                                    if (!$update_user) {
                                        exception("任务返还金额更新发生错误!");
                                    }
                                }
                                echo '更新员工退回PK任务金额';
                            }

                            echo '退回PK任务金额';
                        }else{
                            //增加非冻结
                            $order_data = [
                                'userid' => $taskInfo["create_employee"],
                                'take_money' => bcmul($taskInfo["reward_count"],100,0),
                                'take_status' => 1,
                                'took_time' => $time,
                                'remark' => '任务失败退回',
                                'status'=>1
                            ];
                            if ($task_type == 1) {
                                $order_data["money_type"] = 2;
                            } else {
                                $order_data["money_type"] = 1;
                            }
                            $order_datas[] = $order_data;

                            $employeeMonyField = "left_money";
                            if($pay_type==1) {
                                $employeeMonyField = "corp_".$employeeMonyField;
                            }
                            $employeeInfo=[];
                            $employeeInfo = [$employeeMonyField=>['exp',$employeeMonyField." + ".bcmul($taskInfo["reward_count"],100,0)]];
                            $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$employeeInfo);
                            if (!$update_user) {
                                exception("任务返还金额更新发生错误!");
                            }

                            echo '退回非PK任务金额';
                        }
                    }

                    var_exp($order_datas,'$order_datas_task_save');
                    $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
                    if (!$add_cash_rec) {
                        exception("添加任务奖励发放记录发生错误!");
                    }

                    //更新员工额度
                    $employeeMonyField = "frozen_money";
                    if($pay_type==1) {
                        $employeeMonyField = "corp_".$employeeMonyField;
                    }
                    $taskMoney = bcmul(bcsub($taskInfo["reward_count"],$sentRedEnvelopeMoney,2),100,0);
                    $employeeInfo=[];
                    $employeeInfo = [$employeeMonyField=>['exp',$employeeMonyField." - ".$taskMoney]];
                    $employeeInfoMap = [$employeeMonyField=>["egt",$taskMoney]];
                    var_exp($employeeInfo,'$employeeInfo');
                    var_exp($employeeInfoMap,'$employeeInfoMap');
                    var_exp($taskInfo["create_employee"],'$taskInfo["create_employee"]');
                    $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$employeeInfo,$employeeInfoMap);
                    if (!$update_user) {
                        var_exp($update_user,'$update_user');
                        exception("更新任务冻结金额发生错误!");
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
                    print_r($ex->getTrace());
                    $output_info_str .= "ETSAOT->error:".$ex->getMessage().";";
                    break;
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