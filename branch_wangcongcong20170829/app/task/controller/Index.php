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
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\common\model\Corporation;
use app\crm\model\CallRecord;
use app\crm\model\SaleChance;

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

    public function test(){
        $start_time = input("start_time",0,"int");
        $end_time = input("end_time",0,"int");
        $uids_str = input("uids","","string");
        $standard = input("standard",0,"int");
        $num = input("num",0,"int");
        $page = input("page",1,"int");
        $uids = explode(",",$uids_str);
//        $callRecordM = new CallRecord($this->corp_id);
//        $callRecordData = $callRecordM->getCallRecordStandard($start_time,$end_time,$uids,$standard,$num,$page);

        $saleChanceM = new SaleChance($this->corp_id);
        $saleChanceData = $saleChanceM->getSaleChanceStandard($start_time,$end_time,$uids,$standard,$num,$page);
        return json($saleChanceData);
    }

    public function get(){
        $result = ['status'=>0 ,'info'=>"获取任务时发生错误！"];

        $result['status'] = 1;
        $result['info'] = "获取任务成功！";
        return json($result);
    }
    protected function _getTaskForInput($uid){
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
    protected function _getTaskTargetForInput(){
        $task_target_info['target_type'] = input("target_type",0,"int");
        $task_target_info['target_num'] = input("target_num",0,"int");
        $task_target_info['target_customer'] = input("target_customer",0,"int");
        $task_target_info['target_appraiser'] = input("target_appraiser","","string");
        return $task_target_info;
    }
    protected function _getTaskRewardForInput(){
        $task_reward_info['reward_type'] = input("reward_type",0,"int");
        $task_reward_info['reward_amount'] = input("reward_amount",0,"int");
        $task_reward_info['reward_num'] = input("reward_num",0,"int");
        return $task_reward_info;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建任务时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $taskInfo = $this->_getTaskForInput($uid);
        $taskTargetInfo = $this->_getTaskTargetForInput();
        $taskRewardInfo = $this->_getTaskRewardForInput();
        //TODO 检验和判断
        $money = $taskRewardInfo["reward_amount"];
        $paypassword = input('paypassword');
        if(empty($money)||empty($paypassword)){
            $result['info'] = '参数错误';
            return json($result);
        }
        if (md5($paypassword) != $userinfo['userinfo']['pay_password']) {
            $result['info'] = '支付密码错误';
            $result['status'] = 6;
            return json($result);
        }
        //TODO 冻结金额计算

        $save_money = intval($money*100);

        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskTargetM = new TaskTargetModel($this->corp_id);
        $taskRewardM = new TaskRewardModel($this->corp_id);
        $employM = new Employee($this->corp_id);
        $cashM = new TakeCash($this->corp_id);

        try{
            $employeeTaskM->link->startTrans();

            $taskId = $employeeTaskM->addTask($taskInfo);
            if(!$taskId){
                exception('提交任务失败!');
            }

            $taskTargetInfo['task_id'] = $taskId;
            $taskTargetId = $taskTargetM->addTaskTarget($taskTargetInfo);
            if(!$taskTargetId){
                exception('提交任务目标失败!');
            }

            $taskRewardInfo['task_id'] = $taskId;
            $taskRewardId = $taskRewardM->addTaskReward($taskRewardInfo);
            if(!$taskRewardId){
                exception('提交任务目标失败!');
            }

            $employee_data['corp_left_money'] = ['exp',"corp_left_money - $save_money"];
            $employee_data['corp_frozen_money'] = ['exp',"corp_frozen_money + $save_money"];
            $employee_map["corp_left_money"] = ["egt",$save_money];
            $tip_from_user = $employM->setEmployeeSingleInfo($userinfo["telephone"],$employee_data,$employee_map);
            if (!$tip_from_user) {
                exception("更新用户公司余额发生错误!");
            }

            $order_data = [
                'money_type'=>2,
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

            $de_corp_money["corp_reserved_money"] = ['exp',"corp_reserved_money - $save_money"];
            $de_corp_money["corp_reserved_forzen_money"] = ['exp',"corp_reserved_forzen_money + $save_money"];
            $de_corp_mone_map["corp_reserved_money"] = ["egt",$save_money];
            $de_corp_money_flg = Corporation::setCorporationInfo($this->corp_id,$de_corp_money,$de_corp_mone_map);
            if (!$de_corp_money_flg) {
                exception("更新公司保留额度发生错误!");
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