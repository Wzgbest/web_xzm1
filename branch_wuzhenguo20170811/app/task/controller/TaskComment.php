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

class TaskComment extends Initialize{
	/**
	 * 发表任务评论借口
	 */
	public function addTaskComment(){
		$result = ['satus'=>0,'info'=>"评论任务时发生错误!"];

		$task_id = input('task_id',0,"int");
		$replay_content = input('replay_content',"","string");
		if (empty($task_id) || empty($replay_content)) {
				exception("参数错误!");
			}	

		$userinfo = get_userinfo();
		$uid = $userinfo['userid'];
		$replayer_id = $uid;
		$comment_id = input('comment_id',0,"int");
		$reviewer_id = 0;
		$taskCommentModel = new TaskCommentModel($this->corp_id);
		if ($comment_id) {
			$replay_comment = $taskCommentModel->getOneTaskComment($comment_id);
			$reviewer_id = $replay_comment['replayer_id'];
		}
		$comment['task_id'] = $task_id;
		$comment['replayer_id'] = $replayer_id;
		$comment['replay_content'] = $replay_content;
		$comment['reviewer_id'] = $reviewer_id;
		$comment['replay_comment_id'] = $comment_id;
		$comment['comment_time'] = time();

		$add_result = $taskCommentModel->creatTaskComment($comment);
		$result['data'] = $add_result;
		$result['status'] = 1;
		$result['info'] = "评论成功!";

		return json($result);

	}

}