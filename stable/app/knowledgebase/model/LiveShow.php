<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\knowledgebase\model;

use app\common\model\Base;
use think\Db;
use think\Exception;

class LiveShow extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'live_show';
        parent::__construct($corp_id);
    }
    //添加一条直播信息
    public function addActivity($data){
    	return $this->model->table($this->table)->insertGetId($data);
    }
    //跟新一条直播信息
    public function updateActivity($webinar_id,$data){
    	return $this->model->table($this->table)->where(['show_num'=>$webinar_id])->update($data);
    }
    //删除一条直播信息
    public function delActive($webinar_id){
    	return $this->model->table($this->table)->where(['show_num'=>$webinar_id])->delete();
    }
}