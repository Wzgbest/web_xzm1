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

    /**
     * 获取所有的评论
     * @param  $task_ids array 任务id
     * @param  $map array      条件
     * @param  string $order    排序
     * @return array
     */
    public function getAllTaskComment($task_ids,$map=null,$order="id asc"){
        if (empty($task_ids)) {
            return [];
        }
        $map['task_id'] = ['in',$task_ids];
        $employeeTaskCommentList = $this->model->table($this->table)->alias('etc')
            ->join($this->dbprefix.'employee rp','rp.id = etc.replyer_id and rp.id>0',"LEFT")
            ->join($this->dbprefix.'employee rv','rv.id = etc.reviewer_id and rv.id>0',"LEFT")
            ->where($map)
            ->order($order)
            ->field("etc.*,rp.telephone as replyer_telephone,rp.truename as replyer_name,rp.userpic as replyer_pic,rv.telephone as reviewer_telephone,rv.truename as reviewer_name,rv.userpic as reviewer_pic")
            ->select();

        return $employeeTaskCommentList;
    }

}