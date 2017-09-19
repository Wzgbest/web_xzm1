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
use app\task\service\EmployeeTask as EmployeeTaskService;

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

    public function getTaskTakeAndStandardInfo(){
        $result = ['status'=>0 ,'info'=>"获取任务时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskInfo($id);
        $result['data'] = $taskInfo;
        $taskGuessM = new TaskGuessModel($this->corp_id);
        $guessTakeInfoList = $taskGuessM->getEmployeeGuessMoneyList($id);
        $result['data'] = $guessTakeInfoList;
        $guessTakeInfoList = $taskGuessM->getGuessEmployeeMoneyList($id);
        $result['data'] = $guessTakeInfoList;
        $result['status'] = 1;
        $result['info'] = "获取任务成功！";
        return json($result);
    }

    public function _getEmployeeGuessMoneyList($id,$uids){
        $taskGuessM = new TaskGuessModel($this->corp_id);
        $guessTakeInfoList = $taskGuessM->getGuessEmployeeMoneyList($id);
        return $guessTakeInfoList;
    }

    public function get(){
        $result = ['status'=>0 ,'info'=>"获取任务时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTargetM = new TaskTargetModel($this->corp_id);
        $taskRewardM = new TaskRewardModel($this->corp_id);
        $taskTakeM = new TaskTakeModel($this->corp_id);

        $taskInfo = $employeeTaskM->getTaskInfo($id);
        if(empty($taskInfo)){
            $result['info'] = "未找到任务！";
            return json($result);
        }

        $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
        $taskInfo["target"] = $taskTarget;

        $taskReward = $taskRewardM->getTaskRewardListByTaskId($id);
        $taskInfo["reward"] = $taskReward;
        $taskInfo["all_reward_amount"] = 0;
        foreach ($taskReward as $reward_item){
            $taskInfo["all_reward_amount"] += $reward_item['reward_num']*$reward_item['reward_amount'];
        }

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
        $taskInfo["take"] = $taskTakeEmployeeIds;

        $result['data'] = $taskInfo;
        $result['status'] = 1;
        $result['info'] = "获取任务成功！";
        return json($result);
    }
    public function get_ranking(){
        $result = ['status'=>0 ,'info'=>"获取任务排行时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $num = input("num",20,"int");
        $page = input("page",1,"int");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        //$time = time();

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTargetM = new TaskTargetModel($this->corp_id);
        $taskRewardM = new TaskRewardModel($this->corp_id);
        $taskTakeM = new TaskTakeModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskInfo($id);
        if(empty($taskInfo)){
            $result['info'] = "未找到任务！";
            return json($result);
        }
        $start_time = $taskInfo["task_start_time"];
        $end_time = $taskInfo["task_end_time"];
        $task_type = $taskInfo["task_type"];
        $task_method = $taskInfo["task_method"];

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
        $uids = $taskTakeEmployeeIds;
        if(!in_array($uid,$uids)){
            $result['info'] = "未参与任务！";
            return json($result);
        }
        if($task_type>2){
            $result['info'] = "任务类型不符！";
            return json($result);
        }

        $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
        $target_type = $taskTarget["target_type"];
        $standard = $taskTarget["target_num"];
        if($task_method>4){
            $result['info'] = "任务类型不相符！";
            return json($result);
        }

        /*
        $getRankingListParams = [
            '$target_type'=>$target_type,
            '$task_method'=>$task_method,
            '$start_time'=>$start_time,
            '$end_time'=>$end_time,
            '$uids'=>$uids,
            '$standard'=>$standard,
            '$num'=>$num,
            '$page'=>$page,
        ];
        var_exp($getRankingListParams,'$getRankingListParams');
        */
        $employeeTaskService = new EmployeeTaskService($this->corp_id);
        $rankingdata = $employeeTaskService->getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$standard,$num,$page);

        if($task_type=2){
            $guessdata = $this->_getEmployeeGuessMoneyList($id,$uids);
            foreach ($rankingdata as &$ranking_item){
                if(isset($guessdata[$ranking_item["employee_id"]])){
                    $ranking_item["guess_money"] = $guessdata[$ranking_item["employee_id"]]["money"];
                    $ranking_item["guess_num"] = $guessdata[$ranking_item["employee_id"]]["guess_employee_num"];
                }else{
                    $ranking_item["guess_money"] = 0;
                    $ranking_item["guess_num"] = 0;
                }
            }
        }

        $taskReward = $taskRewardM->getTaskRewardListByTaskId($id);
        $reward_idx_arr = [];
        $reward_idx_max = 0;
        foreach ($taskReward as $reward_item){
            $reward_idx_arr[$reward_item["reward_start"]] = $reward_item;
            if($reward_item["reward_end"]>$reward_idx_max){
                $reward_idx_max = $reward_item["reward_end"];
            }
        }
        $reward_idx = 0;
        $self_idx = -1;
        for($ranking_index=1;$ranking_index<=count($rankingdata);$ranking_index++){
            if(isset($reward_idx_arr[$ranking_index])){
                $reward_idx = $ranking_index;
            }
            $rankingdata[$ranking_index-1]["reward_money"] = $reward_idx_arr[$reward_idx]["reward_amount"];

            if($rankingdata[$ranking_index-1]["employee_id"]==$uid){
                $self_idx = $ranking_index-1;
            }
        }

        $result['data'] = ["self_idx"=>$self_idx,"list"=>$rankingdata];
        $result['status'] = 1;
        $result['info'] = "获取任务排行成功！";
        return json($result);
    }

    protected function _getTaskForInput($uid){
        $task_info['task_name'] = input("task_name","","string");
        $task_info['task_start_time'] = input("task_start_time",0,"strtotime");
        $task_info['task_end_time'] = input("task_end_time",0,"strtotime");
        $task_info['task_take_start_time'] = input("task_take_start_time",0,"strtotime");
        $task_info['task_take_end_time'] = input("task_take_end_time",0,"strtotime");
        $task_info['task_type'] = input("task_type",0,"int");
        if($task_info['task_type']==2){
            $task_info['task_method'] = 4;
        }elseif($task_info['task_type']==3){
            $task_info['task_method'] = 5;
        }else{
            $task_info['task_method'] = input("task_method",0,"int");
        }
        $task_info['content'] = input("content","","string");
        $task_info['public_to_take'] = input("public_to_take","","string");
        $task_info['public_to_view'] = input("public_to_view","","string");
        $task_info['create_employee'] = $uid;
        $task_info['create_time'] = time();
        $task_info['status'] = 1;
        return $task_info;
    }
    protected function _getTaskTargetForInput(){
        $task_target_info['target_type'] = input("target_type",0,"int");
        $task_target_info['target_num'] = input("target_num",0,"int");
        $task_target_info['target_customer'] = input("target_customer",0,"int");
        $task_target_info['target_appraiser'] = input("target_appraiser","","string");
        return $task_target_info;
    }
    protected function _getTaskRewardForInput($task_method){
        $task_reward_infos["all_reward_amount"] = 0;
        $task_reward_infos["reward_max_num"] = 0;
        $reward_type = 2;
        if($task_method==2) {
            $reward_type = 1;
        }
        $reward_method = 1;
        if($task_method==1) {
            $reward_method = 4;
        }
        $reward_str = input("reward");
        $reward_arr = json_decode($reward_str,true);
        $verify_arr = [];
        /* 数组校验名次序列方法,弃用
        $index_max = 1;
        */
        foreach ($reward_arr as $reward_item){
            $task_reward_info['reward_type'] = $reward_type;
            $task_reward_info['reward_method'] = $reward_method;
            $task_reward_info['reward_amount'] = $reward_item["reward_amount"];
            if($reward_item["reward_end"]<$reward_item["reward_start"]){
                return [];
            }
            /* 数组校验名次序列方法,弃用
            for($i=$reward_item["reward_start"];$i<=$reward_item["reward_end"];$i++){
                $verify_arr[$i]=1;
            }
            $index_max = ($index_max<$reward_item["reward_end"])?$reward_item["reward_end"]:$index_max;
            */
            if(isset($verify_arr[$reward_item["reward_start"]])){
                return [];
            }
            $verify_arr[$reward_item["reward_start"]] = $reward_item["reward_end"];
            if($task_method==2){
                $task_reward_info['reward_start'] = 1;
                $task_reward_info['reward_end'] = 1;
                $task_reward_info['reward_num'] = 0;
                $task_reward_infos["all_reward_amount"] += $task_reward_info['reward_amount'];
            }else{
                $task_reward_info['reward_start'] = $reward_item["reward_start"];
                $task_reward_info['reward_end'] = $reward_item["reward_end"];
                $task_reward_info['reward_num'] = $reward_item["reward_end"]-$reward_item["reward_start"]+1;

                $task_reward_infos["all_reward_amount"] += $task_reward_info['reward_num']*$task_reward_info['reward_amount'];
            }
            $task_reward_infos["list"][] = $task_reward_info;
            $task_reward_infos["reward_max_num"] = ($task_reward_infos["reward_max_num"]<$reward_item["reward_end"])?$reward_item["reward_end"]:$task_reward_infos["reward_max_num"];
        }
        /* 数组校验名次序列方法,弃用
        for($i=1;$i<=$index_max;$i++){
            if($verify_arr[$i]!=1){
                return [];
            }
        }
        */

        //var_exp($verify_arr,'$verify_arr');
        $verify_idx = 0;
        for($i=0;$i<count($verify_arr);$i++){
            $verify_idx++;
            if(!isset($verify_arr[$verify_idx])){
                return [];
            }
            $verify_idx = $verify_arr[$verify_idx];
        }
        return $task_reward_infos;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建任务时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $taskInfo = $this->_getTaskForInput($uid);
        //统计周期时间校验
        if($taskInfo["task_start_time"]>=$taskInfo["task_end_time"]){
            $result['info'] = '统计周期时间错误';
            return json($result);
        }
        //PK任务 加入时间校验
        if($taskInfo["task_type"]==2){
            $takeTimeFlg = false;
            if(
                ($taskInfo["task_start_time"]<=$taskInfo["task_take_start_time"]) &&
                ($taskInfo["task_take_start_time"]<$taskInfo["task_take_end_time"]) &&
                ($taskInfo["task_take_end_time"]<=$taskInfo["task_end_time"])
            ){
                $takeTimeFlg = true;
            }
            if(!$takeTimeFlg){
                $result['info'] = '加入时间错误';
                return json($result);
            }
        }
        $taskTargetInfo = $this->_getTaskTargetForInput();
        $taskRewardInfos = $this->_getTaskRewardForInput($taskInfo["task_method"]);
        if(empty($taskRewardInfos)){
            $result['info'] = '分配规则参数错误';
            return json($result);
        }
        //var_exp($taskRewardInfos,'$taskRewardInfos',1);
        $taskInfo["reward_max_num"] = $taskRewardInfos["reward_max_num"];
        $taskTakeInfos[] = [
            "take_employee"=>$uid,
            "take_time"=>$time
        ];
        $public_uids = explode(",",$taskInfo["public_to_take"]);
        if($taskInfo["task_type"]==1){
            foreach ($public_uids as $employee_id){
                $taskTakeInfos[] = [
                    "take_employee"=>$employee_id,
                    "take_time"=>$time
                ];
            }
        }

        //TODO 检验和判断
        $money = $taskRewardInfos["all_reward_amount"];
        $taskInfo["reward_count"] = $money;
        $paypassword = input('paypassword');
        if(empty($money)||empty($paypassword)){
            $result['info'] = '参数错误!';
            return json($result);
        }
        if (md5($paypassword) != $userinfo['userinfo']['pay_password']) {
            $result['info'] = '支付密码错误';
            $result['status'] = 6;
            return json($result);
        }
        //TODO 冻结金额计算

        $save_money = intval($money*100);
        //var_exp($userinfo,'$userinfo',1);
        if($taskInfo["task_type"]==1) {
            if ($userinfo['userinfo']['corp_left_money'] < $save_money) {
                $info['info'] = '企业余额不足';
                $info['status'] = 5;
                return json($info);
            }
        }else{
            if ($userinfo['userinfo']['left_money'] < $save_money) {
                $info['info'] = '账户余额不足';
                $info['status'] = 5;
                return json($info);
            }
        }

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTargetM = new TaskTargetModel($this->corp_id);
        $taskRewardM = new TaskRewardModel($this->corp_id);
        $employM = new Employee($this->corp_id);
        $cashM = new TakeCash($this->corp_id);

        try {
            $employeeTaskM->link->startTrans();

            $taskId = $employeeTaskM->addTask($taskInfo);
            if (!$taskId) {
                exception('提交任务失败!');
            }

            $taskTargetInfo['task_id'] = $taskId;
            $taskTargetId = $taskTargetM->addTaskTarget($taskTargetInfo);
            if (!$taskTargetId) {
                exception('提交任务目标失败!');
            }

            foreach ($taskRewardInfos["list"] as &$taskRewardInfo) {
                $taskRewardInfo['task_id'] = $taskId;
            }
            $taskRewardId = $taskRewardM->addMutipleTaskReward($taskRewardInfos["list"]);
            if (!$taskRewardId) {
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


            if($taskInfo["task_type"]==1) {
                $employee_data['corp_left_money'] = ['exp',"corp_left_money - $save_money"];
                $employee_data['corp_frozen_money'] = ['exp',"corp_frozen_money + $save_money"];
                $employee_map["corp_left_money"] = ["egt",$save_money];
                $tip_from_user = $employM->setEmployeeSingleInfo($userinfo["telephone"],$employee_data,$employee_map);
                if (!$tip_from_user) {
                    exception("更新企业余额发生错误!");
                }
            }else{
                $employee_data['left_money'] = ['exp',"left_money - $save_money"];
                $employee_data['frozen_money'] = ['exp',"frozen_money + $save_money"];
                $employee_map["left_money"] = ["egt",$save_money];
                $tip_from_user = $employM->setEmployeeSingleInfo($userinfo["telephone"],$employee_data,$employee_map);
                if (!$tip_from_user) {
                    exception("更新账户余额发生错误!");
                }
            }

            $order_data = [
                'money_type'=>($taskInfo["task_type"]==1)?2:1,
                'userid'=>$uid,
                'take_money'=> -$save_money,
                'status'=>1,
                'took_time'=>$time,
                'remark' => '发起任务'
            ];
            $tip_from_cash_rec = $cashM->addOrderNumber($order_data);
            if (!$tip_from_cash_rec) {
                exception("添加交易记录发生错误!");
            }

            if($taskInfo["task_type"]==1) {
                $de_corp_money["corp_reserved_money"] = ['exp', "corp_reserved_money - $save_money"];
                $de_corp_money["corp_reserved_frozen_money"] = ['exp', "corp_reserved_frozen_money + $save_money"];
                $de_corp_mone_map["corp_reserved_money"] = ["egt", $save_money];
                $de_corp_money_flg = Corporation::setCorporationInfo($this->corp_id, $de_corp_money, $de_corp_mone_map);
                if (!$de_corp_money_flg) {
                    exception("更新企业保留额度发生错误!");
                }
            }else{
                $de_corp_money["corp_left_money"] = ['exp', "corp_left_money - $save_money"];
                $de_corp_money["corp_frozen_money"] = ['exp', "corp_frozen_money + $save_money"];
                $de_corp_mone_map["corp_left_money"] = ["egt", $save_money];
                $de_corp_money_flg = Corporation::setCorporationInfo($this->corp_id, $de_corp_money, $de_corp_mone_map);
                if (!$de_corp_money_flg) {
                    exception("更新企业账户额度发生错误!");
                }
            }

            $employeeTaskM->link->commit();
            $result['data'] = $taskId;
        }catch (\Exception $ex){
            $employeeTaskM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }

        $telphone = $userinfo["telephone"];
        $userinfo = $employM->getEmployeeByTel($telphone);
        set_userinfo($this->corp_id,$telphone,$userinfo);
        
        $result['status'] = 1;
        $result['info'] = "新建任务成功！";
        return json($result);
    }
    public function take(){
        $result = ['status'=>0 ,'info'=>"参与任务失败!"];
        $task_id = input('task_id',0,"int");
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTakeM = new TaskTakeModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskInfo($id);
        if(empty($taskInfo)){
            $result['info'] = "未找到任务！";
            return json($result);
        }
        $start_time = $taskInfo["task_start_time"];
        if($start_time>$time){
            $result['info'] = "任务未开始！";
            return json($result);
        }
        $end_time = $taskInfo["task_end_time"];
        if($end_time<$time){
            $result['info'] = "任务已结束！";
            return json($result);
        }
        $take_start_time = $taskInfo["task_take_start_time"];
        if($take_start_time>0&&$take_start_time>$time){
            $result['info'] = "任务加入未开始！";
            return json($result);
        }
        $take_end_time = $taskInfo["task_take_end_time"];
        if($take_end_time>0&&$take_end_time<$time){
            $result['info'] = "任务加入已结束！";
            return json($result);
        }
        $task_type = $taskInfo["task_type"];
        $public_take_uids = $taskInfo["public_to_take"];
        if(!in_array($uid,$public_take_uids)){
            $result['info'] = "不在参与任务范围内！";
            return json($result);
        }

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
        $uids = $taskTakeEmployeeIds;
        if(in_array($uid,$uids)){
            $result['info'] = "已参与任务！";
            return json($result);
        }

        $money = 0;
        if($task_type<3){
            $money = 0+input('money');
            $paypassword = input('paypassword');
            if(empty($task_id)||empty($money)||empty($paypassword)){
                $result['info'] = '参数错误';
                return json($result);
            }
            $userinfo = get_userinfo();
            $uid = $userinfo["userid"];
            if (md5($paypassword) != $userinfo['userinfo']['pay_password']) {
                $result['info'] = '支付密码错误';
                $result['status'] = 6;
                return json($result);
            }
            $save_money = intval($money*100);
            $time = time();
            if ($userinfo['userinfo']['left_money'] < $save_money) {
                $info['info'] = '账户余额不足';
                $info['status'] = 5;
                return json($info);
            }
        }
        $taskTakeInfos[] = [
            "take_id"=>$task_id,
            "take_employee"=>$uid,
            "take_time"=>$time
        ];

        $employM = new Employee($this->corp_id);
        try{
            $employeeTaskM->link->startTrans();
            $takeFlg = $taskTakeM->addTaskTake($taskTakeInfos);
            if(!$takeFlg){
                exception("参与任务发生错误!");
            }
            if($task_type==2) {
                $cashM = new TakeCash($this->corp_id);
                $tip_from_user = $employM->setEmployeeSingleInfo($userinfo["telephone"], ['left_money' => ['exp', "left_money - $save_money"]], ["left_money" => ["egt", $save_money]]);
                if (!$tip_from_user) {
                    exception("更新参与任务更新余额发生错误!");
                }
                $order_data = [
                    'userid' => $userinfo['userinfo']['id'],
                    'take_money' => -$save_money,
                    'status' => 1,
                    'took_time' => $time,
                    'remark' => '参与任务'
                ];
                $tip_from_cash_rec = $cashM->addOrderNumber($order_data);
                if (!$tip_from_cash_rec) {
                    exception("添加参与任务交易记录发生错误!");
                }
            }
            $employeeTaskM->link->commit();
        }catch(\Exception $ex){
            $employeeTaskM->link->rollback();
            $result['info'] = '参与任务失败';
            return json($result);
        }

        $telphone = $userinfo["telephone"];
        $userinfo = $employM->getEmployeeByTel($telphone);
        set_userinfo($this->corp_id,$telphone,$userinfo);

        $result['info'] = '参与任务成功';
        $result['status'] = 1;
        return json($result);
    }
}