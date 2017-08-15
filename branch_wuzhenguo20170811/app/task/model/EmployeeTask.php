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
     * 获取任务列表
     * @param  int $uid     用户id
     * @param  int $num     获取数量
     * @param  int $last_id 获取到的最大id
     * @return arr          任务列表
     */
    public function getEmployeeTaskList($uid,$num=10,$last_id=0,$map=[]){
    	$order = "et.id desc";
    	$mapStr = "find_in_set('".$uid."',et.public_to_view)";

    	if($last_id){
            $map["et.id"] = ["lt",$last_id];
        }

        $employeeTaskList = $this->model->table($this->table)->alias('et')
                            ->join($this->dbprefix.'employee e','e.id = et.create_employee',"LEFT")
                            ->join($this->dbprefix.'employee_task_reward etr','etr.task_id = et.id',"LEFT")
                            ->join($this->dbprefix.'employee_task_target ett','ett.task_id = et.id',"LEFT")
                            ->join($this->dbprefix.'employee_task_like etl',"etl.task_id = et.id and etl.user_id = '$uid'","LEFT")
                            ->where($map)
                            ->where($mapStr)
                            ->order($order)
                            ->limit($num)
                            ->group("et.id")
                            ->field("et.*,e.telephone,e.truename,e.userpic,etr.reward_amount,etr.reward_num,etr.reward_type,ett.target_type,ett.target_customer,ett.target_appraiser,ett.target_num,case when etl.user_id>0 then 1 else 0 end as is_like")
                            ->select();

        return $employeeTaskList;
    }
}