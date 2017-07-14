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
    public function getAllCorporationShare($uid,$map=null,$order="id desc"){
        $map["create_user"] = $uid;
        $map["status"]=1;
        $corporationShareList = $this->model->table($this->table)->alias('cs')
            ->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = cs.id',"LEFT")
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
     * @param $num int 数量
     * @param $last_id int 最后一条动态的id
     * @param $map array 动态筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function getCorporationShare($num=10,$last_id=0,$map=[]){
        $order="cs.id desc";
        if($last_id){
            $map["cs.id"] = ["lt",$last_id];
        }
        $corporationShareList = $this->model->table($this->table)->alias('cs')
            ->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = cs.id',"LEFT")
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($num)
            ->group("cs.id")
            ->field("cs.*,csco.content,GROUP_CONCAT(csp.path) as img,e.telephone,e.truename,e.userpic")//TODO
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
        $from_share = $this->model->table($this->table)->where("id",$share_id)->find();
        $share["pid"] = $from_share["id"];
        $share["userid"] = $userid;
        $share["content_id"] = $from_share["content_id"];
        $share["business_id"] = $from_share["business_id"];
        $share["create_time"] = time();
        return $this->model->table($this->table)->insertGetId($share);
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
}