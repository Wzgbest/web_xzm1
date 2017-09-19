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
use app\task\model\TaskTake as TaskTakeModel;
use app\task\model\TaskTarget as TaskTargetModel;
use app\task\model\TaskReward as TaskRewardModel;
use app\task\model\TaskGuess as TaskGuessModel;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\common\model\Corporation;

class TaskGuess extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function show(){
    }


    public function guess(){
        $result = ['status'=>0,'info'=>'猜输赢失败!'];

        $task_id = input('task_id',0,'int');
        $take_employee_id = input('take_employee_id',0,'int');
        $paypassword = input('paypassword');
        $money = 0 + input('money');
        $userinfo = get_userinfo();
        $uid = $userinfo['userid'];

        if (empty($task_id) || empty($take_employee_id) || empty($paypassword) || empty($money)) {
            $result['info'] = "输入的参数错误";
            return json($result);
        }
        if (md5($paypassword) != $userinfo['userinfo']['pay_password']) {
            $result['info'] = "支付密码错误";
            return json($result);
        }
        $save_money = intval($money*100);
        if ($save_money > $userinfo['userinfo']['left_money']) {
            $result['info'] = "账号余额不足";
            return json($result);
        }
        $time = time();

        $taskGussModel = new TaskGuessModel($this->corp_id);
        $employeeModel = new EmployeeTaskModel($this->corp_id);
        $taskTakeM = new TaskTakeModel($this->corp_id);
        $employM = new Employee($this->corp_id);
        $cashM = new TakeCash($this->corp_id);

        $task_data = $employeeModel->getEmployeeById($task_id);
        if (empty($task_data)) {
            $result['info'] = "没有任务信息";
            return json($result);
        }
        if($task_data["task_type"]!=2){
            $result['info'] = "任务类型不符";
            return json($result);
        }
        if($task_data["status"]<2 || $task_data["task_start_time"]>$time){
            $result['info'] = "任务未开始!";
            return json($result);
        }
        if($task_data["status"]>2 || $task_data["task_end_time"]<$time){
            $result['info'] = "任务过期!";
            return json($result);
        }

        $taskTakeEmployeeIds = $taskTakeM->getTaskTakeIdsByTaskId($task_id);
        if(in_array($uid,$taskTakeEmployeeIds)){
            $result['info'] = "已经加入过任务了,不能参与猜输赢";
            return json($result);
        }
        $last_employee_id = $taskGussModel->getLastGuessInfo($uid,$task_id);
        if ($last_employee_id['guess_take_employee'] && $last_employee_id['guess_take_employee'] != $take_employee_id) {
            $result['info'] = "已经猜过其他人了";
            return json($result);
        }

        $flg = false;
        $taskGussModel->link->startTrans();
        try {
            
            $flg = $taskGussModel->guess($uid,$take_employee_id,$task_id,$money);
            if (!$flg) {
                exception("添加猜输赢记录失败");
            }
            $tip_from_user = $employM->setEmployeeSingleInfo($userinfo["telephone"],['left_money'=>['exp',"left_money - $save_money"]],["left_money"=>["egt",$save_money]]);
            if (!$tip_from_user) {
                exception("更新猜输赢任务余额发生错误!");
            }
            $order_data = [
                'userid'=>$userinfo['userinfo']['id'],
                'take_money'=> -$save_money,
                'status'=>1,
                'took_time'=>$time,
                'remark' => '猜输赢任务'
            ];
            $tip_from_cash_rec = $cashM->addOrderNumber($order_data);
            if (!$tip_from_cash_rec) {
                exception("添加猜输赢交易记录发生错误!");
            }
            $taskGussModel->link->commit();
        } catch (\Exception $ex) {
            $taskGussModel->link->rollback();
            $result['info'] = '猜输赢失败!';
            return json($result);
        }

        $telphone = $userinfo["telephone"];
        $userinfo = $employM->getEmployeeByTel($telphone);
        set_userinfo($this->corp_id,$telphone,$userinfo);

        $guessList = $taskGussModel->getGuessList($task_id);
        $myGuess = $taskGussModel->getMyGuess($uid,$task_id);
        $result['info'] = '猜输赢成功';
        $result['status'] = 1;
        $result['data']["my_guess"] = $myGuess;
        $result['data']["guess_list"] = $guessList;
        return json($result);
    }

    public function get_list(){
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取任务猜输赢时发生错误！"];

        $result['status'] = 1;
        $result['info'] = "获取任务猜输赢成功！";
        return json($result);
    }
}