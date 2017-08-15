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

class EmployeeTask extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'employee_task';
        parent::__construct($corp_id);
    }

    /**
     * 获取一条任务信息
     * @param  int $task_id 任务id
     * @return arr          任务信息
     */
    public function getEmployeeById($task_id){

    	$employeeTaskInfo = $this->model->table($this->table)->where("id",$task_id)->find();

    	return $employeeTaskInfo;
    }

    /**
     * 获取一条任务信息
     * @param  array $data 任务信息
     * @return int 任务ID
     */
    public function addTask($data){
        return $this->model->table($this->table)->insertGetId($data);
    }
}