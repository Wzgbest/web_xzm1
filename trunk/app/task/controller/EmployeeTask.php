<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\Task\controller;

use app\common\controller\Initialize;
use app\task\model\TaskTip;
use app\task\model\EmployeeTask as EmployeeTaskModel;
use app\task\model\TaskComment as TaskCommentModel;

class EmployeeTask extends Initialize{

	/**
	 * 获取任务列表
	 * @return arr [description]
	 */
	public function taskList(){
		$result = ['status'=>0,'info'=>"获取列表时失败!"];

		$num = input('num',10,'int');
		$last_id = input('last_id',0,'int');
		$task_type = input('task_type',0,'int');
		$user_info = get_userinfo();
		$uid = $user_info['userid'];
		$employeeTaskModel = new EmployeeTaskModel($this->corp_id);
		$task_list = $employeeTaskModel->getEmployeeTaskAndRedEnvelopeList($uid,$num,$last_id,$task_type);
		$result['data'] = $task_list;
		$result['status'] = 1;
		$result['info'] = "获取成功!";

		return json($result);
	}

	/**
	 * 我的任务列表
	 * @return arr 任务列表
	 */
	public function myTaskList(){
		$result = ['status'=>0,'info'=>"获取列表失败!"];

		$num = input('num',10,'int');
		$last_id = input('last_id',0,'int');
		$task_type = input('task_type',0,'int');
		$is_direct = input('is_direct',0,'int');
		$is_indirect = input('is_indirect',0,'int');
		$is_own = input('is_own',0,'int');
		$is_old = input('is_old',0,'int');
		$user_info = get_userinfo();
		$uid = $user_info['userid'];
		$employeeTaskModel = new EmployeeTaskModel($this->corp_id);
		$my_task_list = $employeeTaskModel->getMyTaskList($uid,$num,$last_id,$task_type,$is_direct,$is_indirect,$is_own,$is_old);

		$result['status'] = 1;
		$result['info'] = "获取列表成功!";
		$result['data'] = $my_task_list;
		return json($result);
	}

    /**
     * 任务大厅里的任务
     */
	public function get_task_list(){
        $result = ['status'=>0,'info'=>"获取列表时失败!"];

        $num = input('num',10,'int');
        $p = input("p",1,"int");
        $task_type = input('task_type',0,'int');

        $map=[];
        if($task_type)
        {
            $map['task_type']=$task_type;
        }
        $user_info = get_userinfo();
        $uid = $user_info['userid'];
        $employeeTaskModel = new EmployeeTaskModel($this->corp_id);
        $task_list = $employeeTaskModel->getEmployeeTaskList($uid,$num,$p,$field='*',$order="id",$direction="desc",$map);
        $countField=["
        count(1) as `0`,
        sum((case when task_type = 1 then 1 else 0 end)) as `1`,
        sum((case when task_type =2 then 1 else 0 end)) as `2`,
        sum((case when task_type =3 then 1 else 0 end)) as `3`,
        sum((case when task_type =4 then 1 else 0 end)) as `4`
        "];//统计个数的field
        $task_count=$employeeTaskModel->getEmployeeTaskCount($uid,$countField,$con=[]);
        $this->assign('task_list',$task_list);
        $this->assign('task_count',$task_count);
    }
    public function reward_task()
    {
        return view();
    }
    public function hot_task()
    {
        $this->get_task_list();
        return view();
    }
    public function hot_task_load(){
        $this->get_task_list();
        return view();
    }

}