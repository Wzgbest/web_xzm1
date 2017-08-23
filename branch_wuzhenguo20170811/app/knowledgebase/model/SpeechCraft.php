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

class SpeechCraft extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'talk_article';
        parent::__construct($corp_id);
    }

    /**
     * 添加话术库
     * @param arr $data 话术信息
     */
    public function addArticleInfo($data){
    	return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 获取所有文章分类
     * @return arr [description]
     */
    public function getAllArticleType(){
    	return $this->model->table($this->dbprefix."talk_article_type")->select();
    }

    /**
     * 添加文章分类
     * @param [type] $data [description]
     */
    public function addClassInfo($data){
    	return $this->model->table($this->dbprefix.'talk_article_type')->insertGetId($data);
    }

    /**
     * 获取所有文章
     * @return [type] [description]
     */
    public function getAllArticle($key,$class_id,$page=1,$num=20,$mapStr='',$map=[]){
    	$order = "in_top desc, ta.article_edit_time desc, ta.id desc";
    	if ($class_id) {
    		$map['article_class'] = $class_id;
    	}
    	if ($key) {
    		$mapStr = "article_name like '%".$key."%'";
    	}
    	$offset = 0;
    	if ($page) {
    		$offset = ($page-1)*$num;
    	}
    	$allArticleInfo = $this->model->table($this->table)->alias('ta')
	    	->where($map)
	    	->where($mapStr)
	    	->order($order)
	    	->field("ta.id,ta.article_name,ta.article_edit_time,ta.article_content,
	    		(CASE WHEN article_is_top = 1 AND article_start_top_time <= UNIX_TIMESTAMP() AND article_end_top_time >= UNIX_TIMESTAMP() THEN 1 ELSE 0 END) AS in_top,
				(CASE WHEN article_release_type = 1 AND article_release_time <= UNIX_TIMESTAMP() THEN 1 ELSE 0 END) AS in_show")
	    	->limit($offset,$num)
	    	->select();

	    	return $allArticleInfo;
    }

    /**
     * 获取指定的文章
     * @param  int $id 文章id
     * @return [type]     [description]
     */
    public function getOneArticleById($id){
    	if (!$id) {
    		return [];
    	}
    	$articleInfo = $this->model->table($this->table)->where(['id'=>$id])->find();
    	return $articleInfo;
    }
}
