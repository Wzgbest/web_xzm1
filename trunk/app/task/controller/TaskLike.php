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
		$taskModel = new EmployeeTaskModel($this->corp_id);
		$task_likeinfo = $taskLikeModel->getTaskLike($uid,$task_id);
		if ($not_like == 0) {	
			if ($task_likeinfo) {
				$result['status'] = 1;
				$result['info'] = "已经喜欢动态了!";
			}else{
				$like_info = $taskLikeModel->do_like($uid,$task_id);
				if ($like_info) {
					$result['status'] = 1;
					$result['info'] = "喜欢动态成功!";

					//发送点赞消息
					$task_data = $taskModel->getEmployeeById($task_id);
			        $userinfos = $userinfo['userinfo'];
			        $str = $userinfos['truename']."点赞了你发布的".$task_data['task_name']."任务";
			        $receive_uids[] = $task_data['create_employee'];
			        $sms['img_url'] = "/message/images/dianzan.png";
			        // save_msg($str,"/task/index/show/id/".$task_id,$receive_uids,3,$task_data['task_type'],$uid,$task_id,$sms);
				}
			}
		}
		if ($not_like == 1) {
			if (empty($task_likeinfo)) {
				$result['status'] = 1;
				$result['info'] = "已经不喜欢动态了!";
			}else{
				$not_like_info = $taskLikeModel->do_notliek($uid,$task_id);
				if ($not_like_info) {
					$result['status'] = 1;
					$result['info'] = "不喜欢动态成功!";
				}
			}	
		}
		//取得当前喜欢数量返回
		$employee_info = $taskModel->getEmployeeById($task_id);
		$result['data'] = $employee_info['like_count'];

		return json($result);
	}

}