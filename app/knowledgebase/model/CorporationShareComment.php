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

class CorporationShareComment extends Base{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'corporation_share_comment';
        parent::__construct($corp_id);
    }

    /**
     * 获取动态的评论
     * @param $share_ids array 评论筛选条件
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 评论筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function getCorporationShareComment($share_ids,$num=10,$page=0,$map=null,$order="id desc"){
        if(empty($share_ids)){
            return [];
        }
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map["share_id"] = ["in",$share_ids];
        $corporationShareCommentList = $this->model->table($this->table)->alias('csc')
            ->join($this->dbprefix.'employee re','re.id = csc.replyer_id',"LEFT")
            ->join($this->dbprefix.'employee rve','rve.id = csc.reviewer_id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("csc.*,re.telephone as replyer_telephone,re.truename as replyer_name,re.userpic as replyer_pic,rve.telephone as reviewer_telephone,rve.truename as reviewer_name,rve.userpic as reviewer_pic")//TODO
            ->select();
        return $corporationShareCommentList;
    }

    /**
     * 获取动态的评论
     * @param $share_ids array 评论筛选条件
     * @param $map array 评论筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function getAllCorporationShareComment($share_ids,$map=null,$order="id asc"){
        if(empty($share_ids)){
            return [];
        }
        $map["share_id"] = ["in",$share_ids];
        $corporationShareCommentList = $this->model->table($this->table)->alias('csc')
            ->join($this->dbprefix.'employee re','re.id = csc.replyer_id',"LEFT")
            ->join($this->dbprefix.'employee rve','rve.id = csc.reviewer_id',"LEFT")
            ->where($map)
            ->order($order)
            ->field("csc.*,re.telephone as replyer_telephone,re.truename as replyer_name,re.userpic as replyer_pic,rve.telephone as reviewer_telephone,rve.truename as reviewer_name")//TODO
            ->select();
        return $corporationShareCommentList;
    }

    /**
     * 根据评论id评论
     * @param $commont_id int 评论id
     * @return array
     * @throws \think\Exception
     */
    public function getOneComment($commont_id){
        $map["id"] = $commont_id;
        $corporationShareComment = $this->model
            ->table($this->table)
            ->where($map)
            ->field("*")//TODO
            ->find();
        return $corporationShareComment;
    }

    /**
     * 创建评论,并返回结果
     * @param $comment array 评论信息
     * @return array
     * @throws \think\Exception
     */
    public function createCorporationShareComment($comment){
        $b = $this->model->table($this->table)->insertGetId($comment);
        return $b;
    }

    /**
     * 删除评论,并返回结果
     * @param $map array 评论筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delCorporationShareComment($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return $b;
    }
}