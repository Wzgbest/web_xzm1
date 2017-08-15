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

class TaskComment extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'employee_task_comment';
        parent::__construct($corp_id);
    }

    /**
     * 获取指定评论
     * @param  int $comment_id 评论id
     * @return arr             返回一条评论
     */
    public function getOneTaskComment($comment_id){
    	$map["id"] = $comment_id;
    	$taskComment = $this->model
    	->table($this->table)
    	->where($map)
    	->field("*")
    	->find();

    	return $taskComment;
    }

    /**
     * 插入一条新评论
     * @param  arr $comment 评论信息
     * @return int          插入信息id
     */
    public function creatTaskComment($comment){
    	$inser_info = $this->model->table($this->table)->insertGetId($comment);

    	return $inser_info;
    }

}