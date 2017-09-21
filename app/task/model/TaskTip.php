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

class TaskTip extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'employee_task_tip';
        parent::__construct($corp_id);
    }

    /**
     * 获取所有的打赏
     * @param  arr $task_ids 任务id
     * @param  arr $map      条件
     * @param  string $order    排序
     * @return arr           
     */
    public function getEmloyeeTaskTip($task_ids,$map=null,$order="id desc"){
    	if (empty($task_ids)) {
    		return [];
    	}
    	$map['task_id'] = ['in',$task_ids];

    	$task_tip_list = $this->model->table($this->table)->alias('ett')
    	->join($this->dbprefix.'emloyee e','e.id = ett.tip_employee',"LETF")
    	->where($map)
    	->order($order)
    	->field("ett.*,e.telephone,e.truename,e.pic")
    	->select();

    	return $task_tip_list;
    }
    /**
     * 添加任务打赏
     * @param  [type] $user_id  [description]
     * @param  [type] $task_id [description]
     * @param  [type] $money    [description]
     * @return [type]           [description]
     */
    public function tip($user_id,$task_id,$money){
        $flg = false;
        $data['tip_employee'] = $user_id;
        $data['task_id'] = $task_id;
        $data['tip_money'] = $money;
        $data['tip_time'] = time();
        try{
            $this->link->startTrans();
            $flg = $this->model->table($this->table)->insert($data);
            if(!$flg){
                exception("添加打赏信息失败");
            }
            $flg = $this->model->table($this->dbprefix.'employee_task')
                ->where("id",$task_id)
                ->setInc("tip_count",$money);
            if(!$flg){
                exception("更新打赏数量失败");
            }
            $this->link->commit();
        }catch (Exception $ex){
            $this->link->rollback();
        }
        return $flg;
    }

    public function getMyTipMoney($user_id,$task_id){
        if($task_id){
            $map["ett.task_id"] = $task_id;
        }
        $map["ett.tip_employee"] = $user_id;
        //$map["e.status"]=1;
        $myTipMoney = $this->model->table($this->table)->alias('ett')
            ->group("ett.tip_employee")
            ->where($map)
            ->field("sum(ett.tip_money) money")
            ->find();
        if($myTipMoney&&isset($myTipMoney["tip_money"])){
            $myTipMoney = $myTipMoney["tip_money"];
        }else{
            $myTipMoney = 0;
        }
        return $myTipMoney;
    }

    public function getTipList($task_id){
        $map["ett.task_id"] = $task_id;
        $map["e.status"]=1;
        $order="ett.id desc";
        $employeeList = $this->model->table($this->table)->alias('ett')
            ->join($this->dbprefix.'employee e','e.id = ett.tip_employee',"LEFT")
            ->where($map)
            ->order($order)
            ->group("ett.id")
            ->field("ett.*,e.truename,e.telephone,e.userpic")
            ->select();
        return $employeeList;
    }

    public function getTipMoneyList($task_id){
        $map["ett.task_id"] = $task_id;
        $order="ett.id desc";
        $employeeList = $this->model->table($this->table)->alias('ett')
            ->where($map)
            ->order($order)
            ->group("ett.id")
            ->field("ett.*")
            ->select();
        return $employeeList;
    }

    public function getAllTipMoneyById($task_id){
        $map["ett.task_id"] = $task_id;
        $order="ett.id desc";
        $allTipMoney = $this->model->table($this->table)->alias('ett')
            ->where($map)
            ->order($order)
            ->group("ett.id")
            ->sum("ett.tip_money");
        return $allTipMoney;
    }
}