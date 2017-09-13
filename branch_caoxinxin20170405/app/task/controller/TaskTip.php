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
use app\task\model\TaskTip as TaskTipModel;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\common\model\Corporation;

class TaskTip extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function show(){
    }

    /*
    author:wuzhenguo
     */
    public function tip(){
        $result = ['status'=>0 ,'info'=>"打赏任务失败!"];
        $task_id = input('task_id',0,"int");
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

        $employeeTaskModel = new EmployeeTaskModel($this->corp_id);
        $TipModel = new TaskTipModel($this->corp_id);
        $employM = new Employee($this->corp_id);
        $cashM = new TakeCash($this->corp_id);
        $task_data = $employeeTaskModel->getEmployeeById($task_id);
        if(empty($task_data)){
            $result['info'] = '未找到任务';
            return json($result);
        }
        $flg = false;
        $TipModel->link->startTrans();
        try{
            $flg = $TipModel->tip($uid,$task_id,$money);
            if (!$flg) {
                exception("添加打赏记录发生错误!");
            }
            $tip_from_user = $employM->setEmployeeSingleInfo($userinfo["telephone"],['left_money'=>['exp',"left_money - $save_money"]],["left_money"=>["egt",$save_money]]);
            if (!$tip_from_user) {
                exception("更新打赏任务余额发生错误!");
            }
            $order_data = [
                'userid'=>$userinfo['userinfo']['id'],
                'take_money'=> -$save_money,
                'status'=>1,
                'took_time'=>$time,
                'remark' => '打赏任务'
            ];
            $tip_from_cash_rec = $cashM->addOrderNumber($order_data);
            if (!$tip_from_cash_rec) {
                exception("添加打赏交易记录发生错误!");
            }
            $TipModel->link->commit();
        }catch(\Exception $ex){
            $TipModel->link->rollback();
            $result['info'] = '打赏失败';
            return json($result);
        }

        $telphone = $userinfo["telephone"];
        $userinfo = $employM->getEmployeeByTel($telphone);
        set_userinfo($this->corp_id,$telphone,$userinfo);
        
        $task_data = $employeeTaskModel->getEmployeeById($task_id);
        
        $tipEmployeeList = $TipModel->getTipList($task_id);
        $myTipMoney = $TipModel->getMyTipMoney($uid,$task_id);
        $result['info'] = '打赏成功';
        $result['status'] = 1;
        $result['data']["tip_count"] = $task_data["tip_count"];
        $result['data']["my_tip"] = $myTipMoney;
        $result['data']["tip_list"] = $tipEmployeeList;
        return json($result);
    }
    public function get_list(){
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取任务打赏时发生错误！"];

        $result['status'] = 1;
        $result['info'] = "获取任务打赏成功！";
        return json($result);
    }
}