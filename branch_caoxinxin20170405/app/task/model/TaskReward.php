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

class TaskReward extends Base{
    protected $dbprefix;
    public function __construct($corp_id = null){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix') . 'employee_task_reward';
        parent::__construct($corp_id);
    }

    /**
     * 获取一条任务奖励信息
     * @param  array $data 任务信息
     * @return int 任务ID
     */
    public function addTaskReward($data){
        return $this->model->table($this->table)->insertGetId($data);
    }
}