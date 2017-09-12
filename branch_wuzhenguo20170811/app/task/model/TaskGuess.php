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
        $map["e.status"]=1;
        $order="etg.id desc";
        $employeeList = $this->model->table($this->table)->alias('etg')
            ->join($this->dbprefix.'employee e','e.id = etg.guess_take_employee',"LEFT")
            ->where($map)
            ->order($order)
            ->group("etg.id")
            ->field("etg.*,e.truename,e.telephone,e.userpic")
            ->select();
        return $employeeList;
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
            ->field("sum(etg.guess_money) money,e.truename,e.telephone,e.userpic")
            ->select();
        return $myTipMoney;
    }
}