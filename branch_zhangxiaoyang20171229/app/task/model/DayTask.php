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

class DayTask extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'employee_task';
        parent::__construct($corp_id);
    }
    public function getAllDayTaskByEmployeeIds($employee_ids){
        if(empty($employee_ids)){
            return [];
        }
        $field = [
            "et.id",
            "ettg.target_type",
            "ettg.target_num",
        ];
        $map["et.task_type"] = 4;
        $map["ettk.take_employee"] = ["in",$employee_ids];
        $map["et.status"] = ["gt",0];
        $order = "et.id asc";
        $standardTaskList = $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee_task_take ettk',"ettk.task_id = et.id","LEFT")
            ->join($this->dbprefix.'employee_task_target ettg','ettg.task_id = et.id',"LEFT")
            ->where($map)
            ->order($order)
            ->field($field)
            ->group('et.id,ettg.id')
            ->select();
        //var_exp($standardTaskList,'$standardTaskList',1);
        return $standardTaskList;
    }

    /**
     * 获取某种任务的某些员工参与信息
     * @param $task_type
     * @param $employee_ids
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getTaskNameByTaskTypeAndEmployee($task_type,$employee_ids){
        if(empty($employee_ids)){
            return [];
        }
        $map["et.task_type"] = $task_type;
        $map["ettk.take_employee"] = ["in",$employee_ids];
        $map["et.status"] = ["gt",0];
        return $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee_task_take ettk',"ettk.task_id = et.id","LEFT")
            ->join($this->dbprefix.'employee e',"e.id = ettk.take_employee","LEFT")
            ->where($map)
            ->field(["et.id,et.task_name,ettk.take_employee,e.truename"])
            ->select();
    }

    /**
     * 获取某人设置的每日任务
     * @param $create_employee
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getDayTaskByCreateEmployee($create_employee){
        $map["et.task_type"] = 6;
        $map["et.create_employee"] = $create_employee;
        $map["et.status"] = ["gt",0];
        return $this->model->table($this->table)->alias('et')
            ->where($map)
//            ->group("et.id")
            ->field(["et.id,et.task_name"])
            ->select();
    }

    /**
     * 获取某些任务的目标
     * @param $task_type
     * @param $task_ids
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getTaskTargetByTaskIds($task_type,$task_ids){
        if(empty($task_ids)){
            return [];
        }
        $field = [
            "et.id",
            "ettg.target_type",
            "ettg.target_num",
        ];
        $map["et.task_type"] = $task_type;
        $map["et.id"] = ["in",$task_ids];
        $map["et.status"] = ["gt",0];
        $order = "et.id asc";
        $standardTaskList = $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee_task_target ettg','ettg.task_id = et.id',"LEFT")
            ->where($map)
            ->order($order)
            ->field($field)
            ->group('et.id,ettg.id')
            ->select();
        //var_exp($standardTaskList,'$standardTaskList',1);
        return $standardTaskList;
    }

    /**
     * 获取某些人的每日任务
     * @param $task_type
     * @param $employee_ids
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getDayTaskEmployeeByCreateEmployee($task_type,$employee_ids){
        if(empty($employee_ids)){
            return [];
        }
        $map["et.task_type"] = $task_type;
        $map["ettk.take_employee"] = ["in",$employee_ids];
        $map["et.status"] = ["gt",0];
        return $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee_task_target ettg','ettg.task_id = et.id')
            ->join($this->dbprefix.'employee_task_take ettk',"ettk.task_id = et.id")
            ->join($this->dbprefix.'employee e',"e.id = ettk.take_employee")
            ->where($map)
            ->group("et.id,ettk.take_employee,ettg.id")
            ->field(["et.id,et.task_name,ettk.take_employee,e.truename,ettg.target_type,ettg.target_num"])
            ->select();
    }
}