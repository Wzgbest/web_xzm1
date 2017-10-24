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
use think\Exception;

class CorporationShare extends Base{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'corporation_share';
        parent::__construct($corp_id);
    }

    /**
     * 获取动态
     * @param $uid int 用户id
     * @param $map array 动态筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function getAllCorporationShare($uid,$map=null,$order="cs.id desc"){
        $map["cs.create_user"] = $uid;
        $map["e.status"]=1;
        $corporationShareList = $this->model->table($this->table)->alias('cs')
            ->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->where($map)
            ->order($order)
            ->group("cs.id")
            ->field("cs.*,csco.content,GROUP_CONCAT(csp.path) as img,e.truename,e.telephone,e.userpic")//TODO
            ->select();
        return $corporationShareList;
    }

    /**
     * 获取动态
     * @param $uid int 用户
     * @param $num int 数量
     * @param $last_id int 最后一条动态的id
     * @param $map array 动态筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function getCorporationShare($uid,$num=10,$last_id=0,$key='',$map=[]){
        $mapStr = '';
        $order="cs.id desc";
        if($last_id){
            $map["cs.id"] = ["lt",$last_id];
        }
        if ($key) {
            $mapStr = "csco.content like '%".$key."%'";
        }
        $corporationShareList = $this->model->table($this->table)->alias('cs')
            ->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->join($this->dbprefix.'corporation_share_like csl','cs.id = csl.share_id and csl.user_id='.$uid,"LEFT")
            ->join($this->dbprefix.'corporation_share_tip cst','cs.id = cst.share_id and cst.user_id='.$uid,"LEFT")
            ->where($map)
            ->where($mapStr)
            ->order($order)
            ->limit($num)
            ->group("cs.id")
            ->field("cs.*,case when csl.user_id>0 then 1 else 0 end as is_like,case when cst.user_id>0 then 1 else 0 end as is_tip,csco.content,csco.share_url,GROUP_CONCAT(csp.path) as img,e.telephone,e.truename,e.userpic")//TODO
            ->select();
        foreach ($corporationShareList as &$corporationShare){
            if($corporationShare["img"]){
                $corporationShare["img"] = explode(",",$corporationShare["img"]);
            }else{
                $corporationShare["img"] = null;
            }
        }
        return $corporationShareList;
    }

    /**
     * 获取动态
     * @param $share_id int 动态ID
     * @return array
     * @throws \think\Exception
     */
    public function getCorporationShareById($share_id){
        $order="cs.id desc";
        $map["cs.id"] = $share_id;
        $corporationShare = $this->model->table($this->table)->alias('cs')
            ->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->join($this->dbprefix.'corporation_share_like csl','cs.id = csl.share_id',"LEFT")
            ->where($map)
            ->order($order)
            ->group("cs.id")
            ->field("cs.*,case when csl.user_id>0 then 1 else 0 end as is_like,csco.content,GROUP_CONCAT(csp.path) as img,e.telephone,e.truename,e.userpic")//TODO
            ->find();
        if($corporationShare["img"]){
            $corporationShare["img"] = explode(",",$corporationShare["img"]);
        }else{
            $corporationShare["img"] = null;
        }
        return $corporationShare;
    }

    /**
     * 创建动态,并返回结果
     * @param $share array 动态信息
     * @return array
     * @throws \think\Exception
     */
    public function createCorporationShare($share){
        $userid = $share['userid'];
        if(empty($userid)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"参数错误！"];
        }
        return $this->model->table($this->table)->insertGetId($share);
    }

    /**
     * 创建转发动态,并返回结果
     * @param $share_id int 被转发的动态信息
     * @return array
     * @throws \think\Exception
     */
    public function relayCorporationShare($share_id,$userid){
        if(empty($userid)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"参数错误！"];
        }
        $flg = false;
        $from_share = $this->model->table($this->table)->where("id",$share_id)->find();
        $share["pid"] = $from_share["id"];
        $share["userid"] = $userid;
        $share["content_id"] = $from_share["content_id"];
        $share["business_id"] = $from_share["business_id"];
        $share["create_time"] = time();

        try{
            $this->link->startTrans();
            $flg = $this->model->table($this->table)
                ->where("id",$share_id)
                ->setInc("return_count");
            if(!$flg){
                exception("转发动态失败");
            }
            $flg = $this->model->table($this->table)->insertGetId($share);
            if(!$flg){
                exception("转发动态失败");
            }
            $this->link->commit();
        }catch (Exception $ex){
            $this->link->rollback();
        }
        return $flg;
    }

    /**
     * 删除动态,并返回结果
     * @param $map array 动态筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delCorporationShare($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return $b;
    }

    /**
     * 删除一条动态
     * @param  int $share_id 动态id
     * @return [type]           [description]
     */
    public function delOneShareById($share_id,$uid){
        $flg = false;
        $is_have = false;
        $shareInfo = $this->getCorporationShareById($share_id);
        $content_id = $shareInfo['content_id'];
        try{
            $this->link->startTrans();
            $flg = $this->model->table($this->table)->where(['id'=>$share_id,'userid'=>$uid])->delete();
            if (!$flg) {
                exception("删除任务失败!");
            }
            $is_have = $this->model->table($this->dbprefix."corporation_share_comment")->where(['share_id'=>$share_id])->select();
            if ($is_have) {
                $flg = $this->model->table($this->dbprefix."corporation_share_comment")->where(['share_id'=>$share_id])->delete();
                if (!$flg) {
                    exception("删除任务评论失败!");
                }
            }

            $is_have = $this->model->table($this->dbprefix."corporation_share_content")->where(['id'=>$content_id])->select();
            if ($is_have) {
                $flg = $this->model->table($this->dbprefix."corporation_share_content")->where(['id'=>$content_id])->delete();
                if (!$flg) {
                    exception("删除内容失败!");
                }
            }
            
            $is_have = $this->model->table($this->dbprefix."corporation_share_like")->where(['share_id'=>$share_id])->select();
            if ($is_have) {
                $flg = $this->model->table($this->dbprefix."corporation_share_like")->where(['share_id'=>$share_id])->delete();
                if (!$flg) {
                    exception("删除喜欢内容失败!");
                }
            }
            
            $is_have = $this->model->table($this->dbprefix."corporation_share_picture")->where(['content_id'=>$content_id])->select();
            if ($is_have) {
                $flg = $this->model->table($this->dbprefix."corporation_share_picture")->where(['content_id'=>$content_id])->delete();
                if (!$flg) {
                    exception("删除动态照片内容失败!");
                }
            }
            
            $is_have = $this->model->table($this->dbprefix."corporation_share_tape")->where(['content_id'=>$content_id])->select();
            if ($is_have) {
                $flg = $this->model->table($this->dbprefix."corporation_share_tape")->where(['content_id'=>$content_id])->delete();
                if (!$flg) {
                    exception("删除音频内容失败!");
                }
            }

            $is_have = $this->model->table($this->dbprefix."corporation_share_tip")->where(['share_id'=>$share_id])->select();
            if ($is_have) {
                $flg = $this->model->table($this->dbprefix."corporation_share_tip")->where(['share_id'=>$share_id])->delete();
                if (!$flg) {
                    exception("删除打赏失败!");
                }
            }
            $this->link->commit();

        }catch(Exception $ex){
            $this->link->rollback();
        }
        return $flg;
    }

    /**
     * 删除一条评论
     * @param  int $comment_id 评论id
     * @return [type]             [description]
     */
    public function delOneCommentById($comment_id,$uid){
        return $this->model->table($this->dbprefix.'corporation_share_comment')->where(['id'=>$comment_id,'replyer_id'=>$uid])->delete();
    }

}