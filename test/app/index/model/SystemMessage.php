<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\index\model;

use app\common\model\Base;
use think\Db;
use think\Exception;

class SystemMessage extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'system_message';
        parent::__construct($corp_id);
    }

    //添加连接
    public function addMsgLink($data){
    	return $this->model->table($this->dbprefix."system_message_link")->insertAll($data);
    }
    //添加消息
    public function addMsg($data){
    	return $this->model->table($this->table)->insertGetId($data);
    }
    //跟新已读状态
    public function updateMsgStd($map,$data){
    	return $this->model->table($this->dbprefix."system_message_link")->where($map)->update($data);
    }
    //获取消息列表
    public function getMsgList($uid,$type=0,$status=2){
    	if (!$uid) {
    		return [];
    	}

    	$map['sml.receive_uid'] = $uid;
    	if ($type) {
    		$map['sm.type'] = $type;
    	}
    	if ($status == 0 || $status == 1) {
    		$map['sml.status'] = $status;
    	}
    	
    	$msg_list = $this->model->table($this->table)->alias('sm')
    	->join($this->dbprefix.'system_message_link sml','sml.msg_id = sm.id',"LEFT")
    	->where($map)
    	->order('sm.create_time desc')
    	->field("sm.id,sm.type,sm.send_uid,sm.msg,sm.url,sm.create_time,sml.receive_uid,sml.create_time as read_time,sml.status as is_read")
    	->select();

    	return $msg_list;
    }
    //获取未读消息数 默认未读消息数
    public function getNotReadMsgCount($uid,$type=0,$status=0){
    	$map['sml.receive_uid'] = $uid;
    	if ($type) {
    		$map['sm.type'] = $type;
    	}
    	$map['sml.status'] = $status;
    	$num = $this->model->table($this->table)->alias('sm')
    	->join($this->dbprefix.'system_message_link sml','sml.msg_id = sm.id','LEFT')
    	->where($map)
    	->count();

    	return $num;
    }
    //查找一条消息
    public function getOneMsg($uid,$msg_id){

    	if (!$uid || !$msg_id) {
    		return [];
    	}

    	$map['sml.msg_id'] = $msg_id;
    	$map['sml.receive_uid'] = $uid;

    	$msg = $this->model->table($this->table)->alias('sm')
    	->join($this->dbprefix.'system_message_link sml','sml.msg_id = sm.id',"LEFT")
    	->where($map)
    	->order('sm.create_time desc')
    	->field("sm.*,sml.receive_uid,sml.create_time,sml.status as is_read")
    	->find();

    }
   	//删除消息
    public function delMsg($uid,$msg_ids=[]){

    	if (!$uid) {
    		return false;
    	}
    	$map['receive_uid'] = $uid;
    	if (!empty($msg_ids)) {
    		$map['msg_id'] = ["in",$msg_ids];
    	}
    	$flg = $this->model->table($this->dbprefix."system_message_link")->where($map)->delete();

    	return $flg;
    }	
}
