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
}