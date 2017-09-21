<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\Task\model;

use app\common\model\Base;
use think\Db;

class TaskGuess extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'employee_task_guess';
        parent::__construct($corp_id);
    }

    public function guess($uid,$take_id,$task_id,$money){
    	$data['task_id'] = $task_id;
    	$data['guess_take_employee'] = $take_id;
    	$data['guess_employee'] = $uid;
    	$data['guess_money'] = $money;
    	$data['guess_time'] = time();

    	$flg = $this->model->table($this->table)->insertGetId($data);
    	return $flg;
    }

    public function getGuessList($task_id){
    	$map["etg.task_id"] = $task_id;
        $map["et.status"]=2;
        $order="etg.id desc";
        $employeeList = $this->model->table($this->table)->alias('etg')
            ->join($this->dbprefix.'employee et','et.id = etg.guess_take_employee',"LEFT")
            ->join($this->dbprefix.'employee eo','eo.id = etg.guess_employee',"LEFT")
            ->where($map)
            ->order($order)
            ->group("etg.id")
            ->field("etg.*,et.truename as take_truename,et.telephone as take_telephone,et.userpic as take_userpic,eo.truename,eo.telephone,eo.userpic")
            ->select();
        return $employeeList;
    }

    public function getGuessInfoList($task_id){
        $map["etg.task_id"] = $task_id;
        $order="etg.id desc";
        $taskGuessInfoList = $this->model->table($this->table)->alias('etg')
            ->where($map)
            ->order($order)
            ->group("etg.id")
            ->field("etg.*")
            ->select();
        return $taskGuessInfoList;
    }

    public function getMyGuess($uid,$task_id){
    	if($task_id){
            $map["etg.task_id"] = $task_id;
        }
        $map["etg.guess_employee"] = $uid;
        $map["e.status"]=1;
        $myTipMoney = $this->model->table($this->table)->alias('etg')
        	->join($this->dbprefix.'employee e','e.id = etg.guess_take_employee',"LEFT")
            ->group("etg.guess_take_employee")
            ->where($map)
            ->field("sum(etg.guess_money) money,e.id as user_id,e.truename,e.telephone,e.userpic")
            ->find();
        return $myTipMoney;
    }

    public function getGuessEmployeeList($employe_id,$task_id){
        $map["etg.task_id"] = $task_id;
        $map["etg.guess_take_employee"] = $employe_id;
        $myTipMoney = $this->model->table($this->table)->alias('etg')
            ->where($map)
            ->field("etg.*")
            ->order("etg.id asc")
            ->select();
        return $myTipMoney;
    }

    public function getLastGuessInfo($uid,$task_id){
    	return $this->model->table($this->table)->where(['guess_employee'=>$uid,'task_id'=>$task_id])->field("guess_take_employee")->find();
    }

    public function getGuessEmployeeMoneyList($task_id){
        $map["etg.task_id"] = $task_id;
        $order="etg.id desc";
        $employeeMoneyList = $this->model->table($this->table)->alias('etg')
            ->where($map)
            ->order($order)
            ->group("etg.guess_take_employee")
            ->column("sum(etg.guess_money) money,count(distinct etg.guess_employee) guess_employee_num","etg.guess_take_employee");
        return $employeeMoneyList;
    }

    public function getEmployeeGuessMoneyList($task_id){
        $map["etg.task_id"] = $task_id;
        $order="etg.id desc";
        $employeeMoneyList = $this->model->table($this->table)->alias('etg')
            ->where($map)
            ->order($order)
            ->group("etg.guess_employee")
            ->column("sum(etg.guess_money) money","etg.guess_employee");
        return $employeeMoneyList;
    }
}