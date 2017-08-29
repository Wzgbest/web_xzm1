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
		$task_list = $employeeTaskModel->getEmployeeTaskList($uid,$num,$last_id,$task_type);
		
		/*
			不需要直接获取用户的评论，可以点击后在获取用户评论
			暂时用不到
		 */
		// $task_ids = array_column($task_list,"id");
		// $taskCommentModel = new TaskCommentModel($this->corp_id);
		// $task_list_comment = $taskCommentModel->getAllTaskComment($task_ids);

		// 	//评论分组
		// $task_data_arr = [];
		// foreach($task_list_comment as $task_comment){
		// 		$task_data_arr[$task_comment['task_id']][] = $task_comment;
		// 	}
		// foreach ($task_list as $key => $value) {
		// 	if (isset($task_data_arr[$value['id']])) {
		// 		$task_list[$key]['comment_list'] = $task_data_arr[$value['id']];
		// 	}else{
		// 		$task_list[$key]['comment_list'] = [];
		// 	}
		// }


		//======获取打赏金额
		//======有直接的字段存总的打赏数
		// $taskTipModel = new TaskTip($this->corp_id);
		// $task_tip = $taskTipModel->getEmloyeeTaskTip($task_ids);
		// $tip_data_arr = [];
		// foreach ($task_tip as $one_task_tip){
  //           $tip_data_arr[$one_task_tip["share_id"]][] = $one_task_tip;
  //       }

		$result['data'] = $task_list;
		$result['status'] = 1;
		$result['info'] = "获取成功!";

		return json($result);
	}

	/**
	 * 我的直接参与任务列表
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

}