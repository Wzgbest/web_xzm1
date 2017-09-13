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
     * 添加一条任务信息
     * @param  array $data 任务信息
     * @return int 任务ID
     */
    public function addTask($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 获取一条任务信息
     * @param  int $id 任务ID
     * @return array 任务信息
     */
    public function getTaskInfo($id){
        return $this->model->table($this->table)
            ->where("id",$id)
            ->find();
    }

     /**
     * 获取任务列表
     * @param  int $uid     用户id
     * @param  int $num     获取数量
     * @param  int $last_id 获取到的最大id
     * @param  int $task_type 任务类型
     * @return arr          任务列表
     */
    public function getEmployeeTaskList($uid,$num=10,$last_id=0,$task_type=0,$map=[]){
        $order = "et.id desc";
        $mapStr = "find_in_set('".$uid."',et.public_to_view)";
        if ($task_type) {
            $map['et.task_type'] = $task_type;
        }
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
            ->field("et.*,e.telephone,e.truename,e.userpic,etr.reward_amount,etr.reward_num,etr.reward_type,etr.reward_method,ett.target_type,ett.target_customer,ett.target_appraiser,ett.target_num,case when etl.user_id>0 then 1 else 0 end as is_like")
            ->select();

        return $employeeTaskList;
    }

    /**
     * 我的直接参与的任务
     * @param  int $uid       用户id
     * @param  int $num       获取数量默认10
     * @param  int $last_id   最后一天任务的id     
     * @param  int $task_type 任务类型（悬赏、pk）
     * @param  int $is_direct 直接任务
     * @param  int $is_indirect 间接任务
     * @param  int $is_own 我发布的任务
     * @return arr            [description]
     */
    public function getMyTaskList($uid,$num=10,$last_id=0,$task_type=0,$is_direct=0,$is_indirect=0,$is_own=0,$is_old=0,$map=[]){

        $order = "et.id desc";
        if ($last_id) {
            $map['et.id'] = ['lt',$last_id];
        }
        if ($task_type) {
            $map['et.task_type'] = $task_type;
        }
        if ($is_direct) {
            $map['ett.take_employee'] = $uid;
        }
        if ($is_indirect) {
            $map['ettip.tip_employee'] = $uid;
        }
        if ($is_own) {
            $map['et.create_employee'] = $uid;
        }
        if ($is_old) {
            $map['et.status'] = 5;
        }
        $myTaskList = $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee eown','eown.id = et.create_employee',"LEFT")
            ->join($this->dbprefix.'employee_task_take ett',"ett.task_id = et.id and ett.take_employee = '$uid'","LEFT")
            ->join($this->dbprefix.'employee_task_reward etr','etr.task_id = et.id',"LEFT")
            ->join($this->dbprefix.'employee_task_target ettar','ettar.task_id = et.id',"LEFT")
            ->join($this->dbprefix.'employee_task_like etl',"etl.task_id = et.id and etl.user_id = '$uid'","LEFT")
            ->join($this->dbprefix.'employee_task_tip ettip',"ettip.task_id = et.id and ettip.tip_employee = '$uid'","LEFT")
            ->join($this->dbprefix.'red_envelope re',"re.task_id = et.id and re.type = 3 and re.took_user = ".$uid,"LEFT")
            ->where($map)
            ->order($order)
            ->limit($num)
            ->group('et.id')
            ->field("et.*,eown.telephone as own_telephone,eown.truename as own_truename,eown.userpic as own_userpic,ett.take_employee,ett.take_time,etr.reward_type,etr.reward_method,etr.reward_amount,etr.reward_num,ettar.target_type,ettar.target_num,ettar.target_customer,ettar.target_appraiser,case when etl.user_id>0 then 1 else 0 end as is_like,ettip.tip_employee,ettip.tip_money,ettip.tip_time,re.redid,re.is_token")
            ->select();

        return $myTaskList;
    }
    public function getAllStandardTaskId($time){
        $map["task_type"] = ["eq",1];
        $map["task_method"] = ["eq",1];
        $map["task_start_time"] = ["elt",$time];
        $map["task_end_time"] = ["egt",$time];
        $map["status"] = ["eq",2];
        $order = "et.id asc";
        $standardTaskList = $this->model->table($this->table)->alias('et')
            ->where($map)
            ->order($order)
            ->group('et.id')
            ->column("et.id");
        //var_exp($standardTaskList,'$standardTaskList',1);
        return $standardTaskList;
    }
    public function getAllOverTimeTask($time){
        $map["task_end_time"] = ["lt",$time];
        $map["status"] = 2;
        $order = "et.id asc";
        $field = ["et.*"];
        $standardTaskList = $this->model->table($this->table)->alias('et')
            ->where($map)
            ->order($order)
            ->group('et.id')
            ->field($field)
            ->select();
        return $standardTaskList;
    }
    public function getStandardTaskInfoById($id){
        $map["et.id"] = $id;
        $employeeTaskInfo = $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee_task_take ett',"ett.task_id = et.id","LEFT")
            ->join($this->dbprefix.'red_envelope re',"re.task_id = et.id and re.type = 3 and re.took_user = ett.take_employee","LEFT")
            ->where($map)
            ->group('ett.take_employee')
            ->column("re.is_token","ett.take_employee");
        return $employeeTaskInfo;
    }
    public function setTaskStatus($ids,$from_status,$to_status){
        $map["id"] = ["in",$ids];
        $map["status"] = $from_status;
        $data["status"] = $to_status;
        $updateTaskResult = $this->model->table($this->table)
            ->where($map)
            ->data($data)
            ->update();
        return $updateTaskResult;
    }
}