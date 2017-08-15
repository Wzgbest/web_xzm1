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
     * 获取所有的评论
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
}