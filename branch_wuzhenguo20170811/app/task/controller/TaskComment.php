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
use app\task\model\TaskComment as TaskCommentModel;
use app\task\model\EmployeeTask as EmployeeTaskModel;

class TaskComment extends Initialize{
	public function show(){
		$id = input('id',0,'int');
		if(!$id){
			$this->error("参数错误");
		}
		$taskCommentModel = new TaskCommentModel($this->corp_id);
		$comment_list = $taskCommentModel->getAllTaskComment($id);
		//var_exp($comment_list,'$comment_list');
		$this->assign('comment_list',$comment_list);
		return view();
	}
	/**
	 * 发表任务评论借口
	 */
	public function addTaskComment(){
		$result = ['status'=>0,'info'=>"评论任务时发生错误!"];

		$task_id = input('task_id',0,"int");
		$reply_content = input('reply_content',"","string");
		if (empty($task_id) || empty($reply_content)) {
				exception("参数错误!");
			}	

		$userinfo = get_userinfo();
		$uid = $userinfo['userid'];
		$replyer_id = $uid;
		$comment_id = input('comment_id',0,"int");
		$reviewer_id = 0;
		$taskCommentModel = new TaskCommentModel($this->corp_id);
		if ($comment_id) {
			$reply_comment = $taskCommentModel->getOneTaskComment($comment_id);
			$reviewer_id = $reply_comment['replyer_id'];
		}
		$comment['task_id'] = $task_id;
		$comment['replyer_id'] = $replyer_id;
		$comment['reply_content'] = $reply_content;
		$comment['reviewer_id'] = $reviewer_id;
		$comment['reply_comment_id'] = $comment_id;
		$comment['comment_time'] = time();

		$add_result = $taskCommentModel->creatTaskComment($comment);
		$result['data'] = $add_result;
		$result['status'] = 1;
		$result['info'] = "评论成功!";

		$employeeTaskModel = new EmployeeTaskModel($this->corp_id);
		$task_data = $employeeTaskModel->getEmployeeById($task_id);
		//发送评论消息
        $userinfos = $userinfo['userinfo'];
        $str = $userinfos['truename']."评论了你发布的".$task_data['task_name']."任务";
        $receive_uids[] = $task_data['create_employee'];
        $sms['img_url'] = "/message/images/pinglun.png";
        save_msg($str,"/task/index/show/id/".$task_id,$receive_uids,3,$task_data['task_type'],$uid,$task_id,$sms);
        if ($comment_id) {
        	save_msg($userinfos['truename']."回复了你的评论","/task/index/show/id/".$task_id,[$reviewer_id],3,$task_data['task_type'],$uid,$task_id,$sms);
        }

		return json($result);

	}

}