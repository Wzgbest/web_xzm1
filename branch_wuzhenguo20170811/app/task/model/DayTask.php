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
        $field = [
            "et.id",
            "ettg.target_type",
            "ettg.target_num",
        ];
        $map["et.task_type"] = 4;
        $map["ettk.take_employee"] = ["in",$employee_ids];
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
}