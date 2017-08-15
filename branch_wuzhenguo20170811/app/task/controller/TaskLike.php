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
use app\task\model\TaskLike as TaskLikeModel;
use app\task\model\EmployeeTask as EmployeeTaskModel;

class TaskLike extends Initialize{
	/**
	 * 点赞接口
	 */
	public function taskLike(){
		$result = ['status'=>0,'info'=>"喜欢动态时发生错误!"];

		$task_id = input('task_id',0,"int");
		$not_like = input('not_like',0,"int");
		if (empty($task_id)) {
			exception("参数错误!");
		}
		// var_dump($task_id);die();
		$userinfo = get_userinfo();
		$uid = $userinfo['userid'];
		$taskLikeModel = new TaskLikeModel($this->corp_id);
		// $task_likeinfo = $taskLikeModel->getTaskLike($uid,$task_id);
		if ($not_like == 0) {	
			$like_info = $taskLikeModel->do_like($uid,$task_id);
			if ($like_info) {
				$result['status'] = 1;
				$result['info'] = "喜欢动态成功!";
			}
		}
		if ($not_like == 1) {
			$not_like_info = $taskLikeModel->do_notliek($uid,$task_id);
			if ($not_like_info) {
				$result['status'] = 1;
				$result['info'] = "不喜欢动态成功!";
			}
		}
		//取得当前喜欢数量返回
		$taskModel = new EmployeeTaskModel($this->corp_id);
		$employee_info = $taskModel->getEmployeeById($task_id);
		$result['data'] = $employee_info['like_count'];

		return json($result);
	}

}