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

class TaskLike extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'employee_task_like';
        parent::__construct($corp_id);
    }

    /**
     * 获取点赞信息
     * @param  int $user_id 用户id
     * @param  int $task_id 任务id
     * @return arr          点赞信息
     */
    public function getTaskLike($user_id,$task_id){
    	$task_likeinfo = $this->model->table($this->talbe)
    		->where(['task_id'=>$task_id,'user_id'=>$user_id])
    		->find();

    		return $task_likeinfo;
    }
    /**
     * 点击喜欢
     * @param  int $uid     用户id
     * @param  int $task_id 喜欢任务id
     * @return bool          
     */
    public function do_like($uid,$task_id){
    	$flg = false;
    	$data['task_id'] = $task_id;
    	$data['user_id'] = $uid;
    	$data['like_time'] = time();
    	try{
    		$this->link->startTrans();
    		$flg = $this->model->table($this->table)->insert($data);
    		if (!$flg) {	
    			exception("喜欢任务失败!");
    		}
    		$flg = $this->model->table($this->dbprefix.'employee_task')->where("id",$task_id)->setInc("like_count");
    		if (!$flg) {
    			exception("更新喜欢数量失败!");
    		}
    		$this->link->commit();
    	}catch(Exception $ex){
    		$this->link->rollback();
    	}

    	return $flg;
    }

    /**
     * 点击不喜欢
     * @param  int $uid     用户id
     * @param  int $task_id 喜欢任务id
     * @return bool          
     */
    public function do_notliek($uid,$task_id){

    	$flg = false;
    	$map['task_id'] = $task_id;
    	$map['user_id'] = $uid;
    	try{
    		$this->link->startTrans();
    		$flg = $this->model->table($this->table)->where($map)->delete();
    		if (!$flg) {	
    			exception("不喜欢任务失败!");
    		}
    		$flg = $this->model->table($this->dbprefix.'employee_task')->where("id", $task_id)->setDec("like_count");
    		if (!$flg) {
    			exception("更新喜欢数量失败!");
    		}
    		$this->link->commit();
    	}catch(Exception $ex){
    		$this->link->rollback();
    	}

    	return $flg;

    }
}