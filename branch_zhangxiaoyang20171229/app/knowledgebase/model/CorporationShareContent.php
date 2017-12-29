<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\knowledgebase\model;

use app\common\model\Base;
use think\Db;

class CorporationShareContent extends Base{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'corporation_share_content';
        parent::__construct($corp_id);
    }

    public function getAllContentHash(){
        return $this->model->table($this->table)->column("id","hash");
    }

    public function getContentByHash($hash){
        return $this->model->table($this->table)->where("hash",$hash)->order("id desc")->find();
    }

    /**
     * 创建动态内容,并返回结果
     * @param $content array 动态内容信息数组
     * @return array
     * @throws \think\Exception
     */
    public function createCorporationShareContent($content){
        return $this->model->table($this->table)->insertGetId($content);
    }
}