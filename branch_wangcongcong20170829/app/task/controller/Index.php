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

class Index extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
    }
    public function show(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误");
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        //$time = time();
        $this->assign('user_money',$userinfo["userinfo"]['left_money']/100);
        $this->assign("id",$id);
        $this->assign("fr",input('fr','','string'));
        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskMoreInfo($uid,$id);
        if(empty($taskInfo)){
            $result['info'] = "未找到任务！";
            return json($result);
        }
        $start_time = $taskInfo["task_start_time"];
        $end_time = $taskInfo["task_end_time"];
        $task_type = $taskInfo["task_type"];
        $task_method = $taskInfo["task_method"];
        $view_name = 'incentive_details';
        if($task_type==2){
            $view_name = "pk_details";
        }else if($task_type==3){
            $view_name = "reward_details";
        }
        $this->assign('task_type',$task_type);
        //var_exp($taskInfo,'$taskInfo',1);
        $this->assign('task_info',$taskInfo);
        $TipModel = new TaskTipModel($this->corp_id);
        $all_tip_money = $TipModel->getAllTipMoneyById($id);
        $my_tip_money = $TipModel->getMyTipMoney($uid,$id);
        $this->assign('all_tip_money',$all_tip_money);
        $this->assign('my_tip_money',$my_tip_money);
        $this->assign('truename',$userinfo["truename"]);
        $taskRewardM = new TaskRewardModel($this->corp_id);
        $taskReward = $taskRewardM->findTaskRewardByTaskId($id);
        $reward_amount = $taskReward["reward_amount"];
        $this->assign('reward_amount',$reward_amount);
        $this->assign('uid',$uid);
        $this->assign('now_time',$this->request->time());
        return view($view_name);
    }
    protected function _new_task_default(){
        $this->assign("fr",input('fr','','string'));
        $userinfo = get_userinfo();
        $this->assign('user_money',$userinfo["userinfo"]['left_money']/100);
        $time = time();
        $this->assign('now_time',time_format_html5($time));
        $customerModel=new CustomerModel($this->corp_id);
        $customer_helpList=$customerModel->getMycustomerForHelpList($userinfo['userid']);
        $this->assign('customer_helpList',$customer_helpList);
    }
    public function new_task(){
        $this->_new_task_default();
        return view();
    }
    public function PKnew_task(){
        $this->_new_task_default();
        return view();
    }
    public function rewardnew_task(){
        $this->_new_task_default();
        return view();
    }
    public function pay(){
        $type = input('type',0,'int');
        $money = input('money',0,'int');
        if ($type!=1) {
            if (!$money) {
                $this->error("输入的金额有误!");
            }
        }
        $pay_title = input('pay_title',"支付",'string');
        $this->assign("fr",input('fr','','string'));
        $userinfo = get_userinfo();
        $this->assign('user_money',$userinfo["userinfo"]['left_money']/100);
        $this->assign('company_money',$userinfo["userinfo"]['corp_left_money']/100);
        $this->assign('type',$type);
        $this->assign('money',$money);
        $this->assign('pay_title',$pay_title);
        return view();
    }
    public function get_ranking_page(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误");
        }
        $num = input("num",20,"int");
        $page = input("page",1,"int");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

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

        if($task_type>3){
            $result['info'] = "任务类型不符！";
            return json($result);
        }
        $this->assign('task_type',$task_type);


        $take_in = false;

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
        $uids = $taskTakeEmployeeIds;
        if(in_array($uid,$uids)){
            $take_in = true;
        }

        $taskGuessM = new TaskGuessModel($this->corp_id);
        $task_guess_employee_ids = $taskGuessM->getEmployeeGuessIdList($id);
        if(in_array($uid,$task_guess_employee_ids)){
            $take_in = true;
        }

        $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
        $target_type = $taskTarget["target_type"];
        $standard = $taskTarget["target_num"];
        if($task_method>5){
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
        $rankingdata = $employeeTaskService->getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$id,$standard,$num,$page);

        //var_exp($uids,'$uids');
//        var_exp($rankingdata,'rankingdata',1);

        $employeeM = new Employee($this->corp_id);
        $employee_info = $employeeM->getEmployeeNameAndTelephoneByUserids($uids);

        if(is_array($rankingdata)){
            $in_employee_idx = array_column($rankingdata,"employee_id");
            //var_exp($in_employee_idx,'$in_employee_idx');
            $not_in_employee_idx = [];
            foreach ($uids as $uid_item){
                if(!in_array($uid_item,$in_employee_idx)){
                    $not_in_employee_idx[] = $uid_item;
                }
            }
            //var_exp($not_in_employee_idx,'$not_in_employee_idx');

            if(!empty($not_in_employee_idx)) {

                foreach ($not_in_employee_idx as $uid_item) {
                    if (isset($employee_info[$uid_item])) {
                        $result_item = [];
                        $result_item = ["employee_id" => $uid_item, "telephone" => $employee_info[$uid_item]["telephone"], "truename" => $employee_info[$uid_item]["truename"],"struct_name"=> $employee_info[$uid_item]["struct_name"], "is_standard" => 0, "num" => 0, "standard_time" => 0];
                        $rankingdata[] = $result_item;
                    }
                }
            }
            //var_exp($employee_info,'$employee_info');
            //var_exp($rankingdata,'$rankingdata');
        }
        if($task_type==2){
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

        $redEnvelopeM = new RedEnvelopeModel($this->corp_id);
        $redEnvelopeInfos = $redEnvelopeM->getRedEnvelopeByTaskAndUid($id,[]);
        $redEnvelopeInfoIdx = [];
        foreach ($redEnvelopeInfos as $redEnvelopeInfo){
            $redEnvelopeInfoIdx[$redEnvelopeInfo["took_user"]][] = $redEnvelopeInfo;
        }

        $reward_idx = 0;
        $self_idx = -1;
        $hide_name = (!$take_in && $uid!=$taskInfo['create_employee'] && $task_type==2 && ($taskInfo["status"]==2&&$time<$taskInfo["task_end_time"]));
//        var_exp($rankingdata,'$rankingdata',1);
        for($ranking_index=1;$ranking_index<=count($rankingdata);$ranking_index++){
//var_exp($rankingdata[$ranking_index-1]["employee_id"],'$rankingdata[$ranking_index-1]["employee_id"]');
            $rankingdata[$ranking_index-1]["struct_name"]=$employee_info[$rankingdata[$ranking_index-1]["employee_id"]]["struct_name"];

            if($hide_name){
                $rankingdata[$ranking_index-1]["truename"] ='***';// mb_substr($rankingdata[$ranking_index-1]["truename"],0,1,'utf-8')."**";
                $rankingdata[$ranking_index-1]["struct_name"]='***';
            }

            if(isset($reward_idx_arr[$ranking_index])){
                $reward_idx = $ranking_index;
            }
            $rankingdata[$ranking_index-1]["reward_money"] = $reward_idx_arr[$reward_idx]["reward_amount"];

            if($rankingdata[$ranking_index-1]["employee_id"]==$uid){
                $self_idx = $ranking_index-1;
            }
            if(isset($redEnvelopeInfoIdx[$rankingdata[$ranking_index-1]["employee_id"]])){
                $rankingdata[$ranking_index-1]["red_info"] = $redEnvelopeInfoIdx[$rankingdata[$ranking_index-1]["employee_id"]];
                $untook = 0;
                foreach ($redEnvelopeInfoIdx[$rankingdata[$ranking_index-1]["employee_id"]] as $red_item){
                    if($red_item["is_token"]==0){
                        $untook = 1;
                        break;
                    }
                }
                $rankingdata[$ranking_index-1]["untook"] = $untook;
            }else{
                $rankingdata[$ranking_index-1]["red_info"] = 0;
                $rankingdata[$ranking_index-1]["untook"] = 0;
            }
        }
        if($this->request->time()>$taskInfo['task_end_time']){
            $task_end_flag=1;//任务已经结束
        }
        else{
            $task_end_flag=0;
        }
//        var_exp($rankingdata,'$rankingdata',1);
        $this->assign('self_idx',$self_idx);
        $this->assign('rankingdata',$rankingdata);
        $this->assign('uid',$uid);
        $this->assign('create_employee',$taskInfo['create_employee']);
        $this->assign('task_end_flag',$task_end_flag);//任务是否已经结束的标识
        $this->assign('target_type',$target_type);//任务目标类型,1:通话数,2:商机数,3:成交额,4:成单数,5:拜访数,6:新增客户数
        return view();
    }
    public function employee_data(){
        $result = 'var cityData=';
        $result_arr = [];
        $structureModel = new Structure($this->corp_id);
        $employeeM = new Employee($this->corp_id);
        $structure = $structureModel->getAllStructure();
        $friendsInfo = $employeeM->getAllUsers();
//        var_exp($structure,'$structure');
//        var_exp($friendsInfo,'$friendsInfo');
        $structure_idx = [];
        foreach($structure as $structure_item){
            $structure_item["child"] = [];
            $structure_idx[$structure_item["id"]] = $structure_item;
        }

        foreach($friendsInfo as $friend_info){
            $struct_arr = explode(",",$friend_info["struct_id"]);
            foreach($struct_arr as $struct){
                if(isset($structure_idx[$struct])){
                    $structure_idx[$struct]["child"][] = [$friend_info["id"]=>$friend_info["nickname"]];
                }
            }
        }
        //var_exp($structure_idx,'$structure_idx');
        foreach ($structure_idx as $structure_item){
            //var_exp($structure_item,'$structure_item');
            $result_item = [
                $structure_item["id"]=>$structure_item["struct_name"],
                "childCity"=>$structure_item["child"]
            ];
            $result_arr[] = $result_item;
        }
        //var_exp($result_arr,'$result_arr');
        $result .= json_encode($result_arr,true).";";
        echo $result;
    }

    public function getRedEnvelope(){
        $result = ['status'=>0 ,'info'=>"获取任务红包时发生错误！"];
        $red_id = input('redid',"",'string');
        if(empty($red_id)){
            $result["info"] = "红包ID不能为空";
            return json($result);
        }
        if (!preg_match('/[0-9a-fA-F]{32}/',$red_id)) {
            $result["info"] = "参数错误";
            return json($result);
        }
        $redEnvelopeS = new RedEnvelopeService($this->corp_id);
        $result = $redEnvelopeS->getRedEnvelope($red_id);
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
        $taskCommentModel = new TaskCommentModel($this->corp_id);
        $employeeM = new Employee($this->corp_id);

        $taskInfo = $employeeTaskM->getTaskMoreInfo($uid,$id);
        if(empty($taskInfo)){
            $result['info'] = "未找到任务！";
            return json($result);
        }
        $taskInfo["public_to_take_name"] = $employeeM->getEmployeeNameByUserids($taskInfo["public_to_take"]);

        $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
        $taskInfo["target_type"] = $taskTarget["target_type"];
        $taskInfo["target_num"] = $taskTarget["target_num"];

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
        $taskInfo["take"] = $taskTakeEmployeeIds;

        $taskReward = $taskRewardM->getTaskRewardListByTaskId($id);
        $taskInfo["reward"] = $taskReward;

        $task_comment = $taskCommentModel->getAllTaskComment($id);
        $taskInfo["comment"] = $task_comment;


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
        $time = time();

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
        if($task_type>3){
            $result['info'] = "任务类型不符！";
            return json($result);
        }

        $take_in = false;

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($id);
        $uids = $taskTakeEmployeeIds;
        if(in_array($uid,$uids)){
            $take_in = true;
        }

        $taskGuessM = new TaskGuessModel($this->corp_id);
        $task_guess_employee_ids = $taskGuessM->getEmployeeGuessIdList($id);
        if(in_array($uid,$task_guess_employee_ids)){
            $take_in = true;
        }

        $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
        $target_type = $taskTarget["target_type"];
        $standard = $taskTarget["target_num"];
        if($task_method>5){
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
        $rankingdata = $employeeTaskService->getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$id,$standard,$num,$page);

        //var_exp($rankingdata,'$rankingdata',1);


        if(is_array($rankingdata)){
            $in_employee_idx = array_column($rankingdata,"employee_id");
            $not_in_employee_idx = [];
            foreach ($uids as $uid_item){
                if(!in_array($uid_item,$in_employee_idx)){
                    $not_in_employee_idx[] = $uid_item;
                }
            }
            if(!empty($not_in_employee_idx)) {
                $employeeM = new Employee($this->corp_id);
                $employee_info = $employeeM->getEmployeeNameAndTelephoneByUserids($not_in_employee_idx);
                foreach ($not_in_employee_idx as $uid_item) {
                    $result_item = [];
                    if (isset($employee_info[$uid_item])) {
                        $result_item = ["employee_id" => $uid_item, "telephone" => $employee_info[$uid_item]["telephone"], "truename" => $employee_info[$uid_item]["truename"],"struct_name"=> $employee_info[$uid_item]["struct_name"], "is_standard" => 0, "num" => 0, "standard_time" => 0];
                    }
                    $rankingdata[] = $result_item;
                }
            }
        }
        if($task_type==2){
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
        $hide_name = (!$take_in && $uid!=$taskInfo['create_employee'] && $task_type==2 && ($taskInfo["status"]==2&&$time<$taskInfo["task_end_time"]));
        for($ranking_index=1;$ranking_index<=count($rankingdata);$ranking_index++){
            if($hide_name){
                $rankingdata[$ranking_index-1]["truename"] ='***';// mb_substr($rankingdata[$ranking_index-1]["truename"],0,1,'utf-8')."**";
                $rankingdata[$ranking_index-1]["struct_name"]='***';
            }

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
    public function get_customer(){
        $result = ['status'=>0 ,'info'=>"获取任务排行时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        //$time = time();

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskInfo($id);
        if(empty($taskInfo)){
            $result['info'] = "未找到任务！";
            return json($result);
        }
        $task_type = $taskInfo["task_type"];
        if($task_type!=3){
            $result['info'] = "任务类型不符！";
            return json($result);
        }
        $taskTargetM = new TaskTargetModel($this->corp_id);
        $taskTarget = $taskTargetM->findTaskTargetByTaskId($id);
        $customer_id = $taskTarget["target_customer"];

        //TODO 获取客户信息
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
        $public_uids = explode(",",$task_info['public_to_take']);
        $public_uids = array_filter($public_uids);
        $public_uids = array_unique($public_uids);
        $task_info['public_to_take'] = implode(",",$public_uids);

        $task_info['public_to_view'] = input("public_to_view","","string");
        $public_uids = explode(",",$task_info['public_to_view']);
        $public_uids = array_filter($public_uids);
        $public_uids = array_unique($public_uids);
        $task_info['public_to_view'] = implode(",",$public_uids);
        
        $task_info['create_employee'] = $uid;
        $task_info['create_time'] = time();
        $task_info['status'] = 2;
        return $task_info;
    }
    protected function _getTaskTargetForInput($task_method){
        $task_target_info['target_type'] = input("target_type",0,"int");
        $task_target_info['target_num'] = input("target_num",0,"int");
        $task_target_info['target_method']=input("target_method",0,"int");

        if($task_method==5){
            $task_target_info['target_type'] = 7;
            if($task_target_info['target_method']==1) {
                $task_target_info['target_description']=input("target_description","","string");
                if(empty($task_target_info['target_description'])){
                    return [];
                }
            }elseif($task_target_info['target_method']==2) {
                $task_target_info['target_customer']=input("target_customer","","int");
                if(!$task_target_info['target_customer']){
                    return [];
                }
            }
        }else{
            if($task_target_info['target_type']<=0){
                return [];
            }
        }
        //var_exp($task_target_info,'$task_target_info',1);
        return $task_target_info;
    }
    protected function _getTaskRewardForInput($task_method){
        $task_reward_infos["all_reward_amount"] = 0;
        $task_reward_infos["reward_max_num"] = 0;
        $reward_type = 2;
        $reward_method = 1;
        if($task_method==1) {
            $reward_method = 4;
        }elseif($task_method==2) {
            $reward_type = 1;
        }
        $reward_str = input("reward");
        $reward_arr = json_decode($reward_str,true);
        //var_exp($reward_arr,'$reward_arr',1);
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

                if($task_method==4){
                    $task_reward_infos["all_reward_amount"] = $task_reward_info['reward_amount'];
                }else{
                    $task_reward_infos["all_reward_amount"] += $task_reward_info['reward_num']*$task_reward_info['reward_amount'];
                }
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
            $taskInfo["task_method"] = 4;
        }elseif($taskInfo["task_type"]==3){
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
            $taskInfo["task_method"] = 5;
        }
        $taskTargetInfo = $this->_getTaskTargetForInput($taskInfo["task_method"]);
//        var_exp($taskTargetInfo,'$taskTargetInfo',1);
        if(empty($taskTargetInfo)){
            $result['info'] = '任务目标参数错误';
            return json($result);
        }
        $taskRewardInfos = $this->_getTaskRewardForInput($taskInfo["task_method"]);
        if(empty($taskRewardInfos)){
            $result['info'] = '分配规则参数错误';
            return json($result);
        }
        //var_exp($taskRewardInfos,'$taskRewardInfos',1);
        $taskInfo["reward_max_num"] = $taskRewardInfos["reward_max_num"];
        $taskTakeInfos = [];
        $public_uids = explode(",",$taskInfo["public_to_take"]);
        $public_uids = array_filter($public_uids);
        $public_uids = array_unique($public_uids);
        if($taskInfo["task_type"]==1){
            foreach ($public_uids as $employee_id){
                $taskTakeInfos[] = [
                    "take_employee"=>$employee_id,
                    "take_time"=>$time
                ];
            }
        }
        if($taskInfo["task_type"]==2){
            $taskTakeInfos[] = [
                "take_employee"=>$uid,
                "take_time"=>$time
            ];
            if(!in_array($uid,$public_uids)){
                $taskInfo["public_to_take"] .= ",".$uid;
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
        $pay_type = input('pay_type',0,'int');
        $taskInfo['pay_type'] = $pay_type;
        if($taskInfo["task_type"]==1 && $pay_type == 1) {
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
        $employeeM = new Employee($this->corp_id);
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

            if(!empty($taskTakeInfos)){
                foreach ($taskTakeInfos as &$taskTakeInfo) {
                    $taskTakeInfo['task_id'] = $taskId;
                }
                $taskTakeM = new TaskTakeModel($this->corp_id);
                $taskTakeId = $taskTakeM->addMutipleTaskTake($taskTakeInfos);
                if(!$taskTakeId){
                    exception('提交任务参与信息失败!');
                }
            }


            if($taskInfo["task_type"]==1 && $pay_type == 1) {
                $employee_data['corp_left_money'] = ['exp',"corp_left_money - $save_money"];
                $employee_data['corp_frozen_money'] = ['exp',"corp_frozen_money + $save_money"];
                $employee_map["corp_left_money"] = ["egt",$save_money];
                $tip_from_user = $employeeM->setEmployeeSingleInfo($userinfo["telephone"],$employee_data,$employee_map);
                if (!$tip_from_user) {
                    exception("更新企业余额发生错误!");
                }
            }else{
                $employee_data['left_money'] = ['exp',"left_money - $save_money"];
                $employee_data['frozen_money'] = ['exp',"frozen_money + $save_money"];
                $employee_map["left_money"] = ["egt",$save_money];
                $tip_from_user = $employeeM->setEmployeeSingleInfo($userinfo["telephone"],$employee_data,$employee_map);
                if (!$tip_from_user) {
                    exception("更新账户余额发生错误!");
                }
            }

            $order_data = [
                'userid'=>$uid,
                "take_type"=>5,
                "take_type_sub"=>5,
                "take_id"=>$taskId,
                'take_money'=> -$save_money,
                'take_status'=>1,
                'took_time'=>$time,
                'remark' => '发起任务',
                "status"=>1,
                'money_type'=>($taskInfo["task_type"]==1)?2:1
            ];
            $tip_from_cash_rec = $cashM->addOrderNumber($order_data);
            if (!$tip_from_cash_rec) {
                exception("添加交易记录发生错误!");
            }

            if($taskInfo["task_type"]==1 && $pay_type == 1) {
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
            //print_r($ex->getTrace());die();
            $result['info'] = $ex->getMessage();
            return json($result);
        }

        $user_infomation = $userinfo["userinfo"];
        $receive_uids = explode(',',$taskInfo['public_to_take']);
        if ($taskInfo['task_type'] == 1) {
            $str = "你已参与由".$user_infomation["truename"]."发的的激励任务，查看详情";
        }else if($taskInfo['task_type'] == 2){
            $str = $user_infomation["truename"]."向你发起了通话个数的pk任务挑战，快去领取任务，接受挑战吧";
        }else{
            $str = $user_infomation["truename"]."向你发起了悬赏任务求助，看看能不能帮到他";
        }
        $flg = save_msg($str,"/task/index/show/id/".$taskId,$receive_uids,3,$taskInfo['task_type'],$uid,$taskId);

        $telphone = $userinfo["telephone"];
        $userinfo = $employeeM->getEmployeeByTel($telphone);
        set_userinfo($this->corp_id,$telphone,$userinfo);
       
        $result['status'] = 1;
        $result['info'] = "新建任务成功！";
        return json($result);
    }
    public function take(){
        $result = ['status'=>0 ,'info'=>"参与任务失败!"];
        $task_id = input('task_id',0,"int");
        if(!$task_id){
            $result['info'] = "参数错误！";  
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTakeM = new TaskTakeModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskInfo($task_id);
        if(empty($taskInfo)){
            $result['info'] = "未找到任务！";
            return json($result);
        }
        $start_time = $taskInfo["task_start_time"];
        if($taskInfo["status"]<2 || $start_time>$time){
            $result['info'] = "任务未开始！";
            return json($result);
        }
        $end_time = $taskInfo["task_end_time"];
        if($taskInfo["status"]>2 || $end_time<$time){
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
        $public_take_uids = explode(",",$taskInfo["public_to_take"]);
        if(!in_array($uid,$public_take_uids)){
            $result['info'] = "不在参与任务范围内！";
            return json($result);
        }

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($task_id);
        $uids = $taskTakeEmployeeIds;
        if(in_array($uid,$uids)){
            $result['info'] = "已参与任务！";
            return json($result);
        }
        $taskTakeNumMax = 0;
        $taskTakeAmountMax = 0;
        if($task_type>1) {
            $taskRewardM = new TaskRewardModel($this->corp_id);
            $taskReward = $taskRewardM->getTaskRewardListByTaskId($task_id);
            foreach ($taskReward as $reward_item) {
                $taskTakeNumMax += $reward_item['reward_num'];
                $taskTakeAmountMax += $reward_item['reward_amount'];
            }
            if(count($taskTakeEmployeeIds)>=$taskTakeNumMax){
                $result['info'] = "参与任务人数已满！";
                return json($result);
            }
        }
        $taskGussModel = new TaskGuessModel($this->corp_id);
        $last_employee_id = $taskGussModel->getLastGuessInfo($uid,$task_id);
        if ($last_employee_id['guess_take_employee']) {
            $result['info'] = "已经参与过猜输赢了,不能加入任务";
            return json($result);
        }

        $money = $taskTakeAmountMax;
        if($task_type==2){
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
        $taskTakeInfo = [
            "task_id"=>$task_id,
            "take_employee"=>$uid,
            "take_time"=>$time
        ];

        $employeeM = new Employee($this->corp_id);
        try{
            $employeeTaskM->link->startTrans();
            $takeFlg = $taskTakeM->addTaskTake($taskTakeInfo);
            if(!$takeFlg){
                exception("参与任务发生错误!");
            }
            if($task_type==2) {
                $updataData["reward_count"] = ["exp","reward_count + ".$money];
                $taskInfoUpdataFlg = $employeeTaskM->setTaskInfo($task_id,$updataData);
                if (!$taskInfoUpdataFlg) {
                    exception("更新任务奖励总额发生错误!");
                }
                
                $cashM = new TakeCash($this->corp_id);
                $tip_from_user = $employeeM->setEmployeeSingleInfo($userinfo["telephone"], ['left_money' => ['exp', "left_money - $save_money"]], ["left_money" => ["egt", $save_money]]);
                if (!$tip_from_user) {
                    exception("更新参与任务更新余额发生错误!");
                }
                $order_data = [
                    'userid' => $userinfo['userinfo']['id'],
                    "take_type"=>5,
                    "take_type_sub"=>6,
                    "take_id"=>$task_id,
                    'take_money' => -$save_money,
                    'take_status' => 1,
                    'took_time' => $time,
                    'remark' => '参与任务',
                    "status"=>1
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

        if($task_type==2) {
            $telphone = $userinfo["telephone"];
            $userinfo = $employeeM->getEmployeeByTel($telphone);
            set_userinfo($this->corp_id, $telphone, $userinfo);
        }

        $result['info'] = '参与任务成功';
        $result['status'] = 1;
        return json($result);
    }
}