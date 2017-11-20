<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

namespace app\Task\model;

use app\common\model\Base;

class TaskTarget extends Base{
    protected $dbprefix;
    public function __construct($corp_id = null){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix') . 'employee_task_target';
        parent::__construct($corp_id);
    }

    /**
     * 添加一条任务目标信息
     * @param  array $data 任务信息
     * @return int 任务ID
     */
    public function addTaskTarget($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 添加多条任务目标信息
     * @param  array $datas 任务信息
     * @return int 任务ID
     */
    public function addMutipleTaskTarget($datas){
        return $this->model->table($this->table)->insertAll($datas);
    }

    /**
     * 添更新任务目标信息
     * @param  int $employee_id 员工id
     * @param  int $type 类型
     * @param  int $time 时间
     * @return int 更新数量
     */
    public function updateTaskTarget($employee_id,$type,$time){
        $map["target_employee_id"] = $employee_id;
        $map["target_type"] = $type;
        $map["target_start_time"] = ["elt",$time];
        $map["target_end_time"] = ["egt",$time];

        $data["target_done_num"] = ["exp","target_done_num + 1"];
        $data["target_standard_time"] = ["exp","(
            CASE
            WHEN target_done_num = target_num THEN
                ".$time."
            ELSE
                target_standard_time
            END
        )"];
        $data["target_update_time"] = ["exp","(
            CASE
            WHEN target_update_time < ".$time." THEN
                ".$time."
            ELSE
                target_update_time
            END
        )"];
        $data["target_rate"] = ["exp","target_done_num*100/target_num"];
        $data["target_status"] = ["exp","(
            CASE
            WHEN target_done_num >= target_num THEN
                1
            ELSE
                0
            END
        )"];
        return $this->model->table($this->table)
            ->where($map)
            ->fetchSql(true)
            ->update($data);
    }

    /**
     * 获取某个任务的所有目标信息
     * @param  int $task_id 任务ID
     * @return array 任务信息
     */
    public function findTaskTargetByTaskId($task_id){
        return $this->model->table($this->table)
            ->where("task_id",$task_id)
            ->find();
    }

    /**
     * 获取某个任务的所有目标信息
     * @param  int $task_id 任务ID
     * @return array 任务信息
     */
    public function getTaskTargetListByTaskId($task_id){
        return $this->model->table($this->table)
            ->where("task_id",$task_id)
            ->select();
    }

    /**
     * 获取某个客户是否有帮跟信息
     * @param  int $uid 员工ID
     * @param  int $customer_id 客户ID
     * @param  int $time 时间戳
     * @return array 任务信息
     */
    public function findTaskTargetByCustomerId($uid,$customer_id,$time){
        return $this->model->table($this->table)->alias('ett')
            ->join($this->dbprefix.'employee_task et',"ett.task_id = et.id","LEFT")
            ->join($this->dbprefix.'employee_task_take ettk',"ettk.task_id = et.id","LEFT")
            ->where("ett.target_customer",$customer_id)
            ->where("ett.target_type",7)
            ->where("ett.target_method",2)
            ->where("et.task_start_time","elt",$time)
            ->where("et.task_end_time","egt",$time)
            ->where("et.status",2)
            ->where("ettk.take_employee",$uid)
            ->field("et.*")
            //->fetchSql(true)
            ->find();
    }
}