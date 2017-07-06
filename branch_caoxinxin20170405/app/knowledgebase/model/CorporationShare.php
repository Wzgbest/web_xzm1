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
            ->join($this->dbprefix.'corporation_share_picture csp','csp.share_id = cs.id',"LEFT")
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->where($map)
            ->order($order)
            ->group("cs.id")
            ->field("cs.*,GROUP_CONCAT(csp.path) as img,e.truename,e.userpic")//TODO
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
            ->join($this->dbprefix.'corporation_share_picture csp','csp.share_id = cs.id',"LEFT")
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($num)
            ->group("cs.id")
            ->field("cs.*,GROUP_CONCAT(csp.path) as img,e.truename,e.userpic")//TODO
            ->select();
        foreach ($corporationShareList as &$corporationShare){
            $corporationShare["img"] = explode(",",$corporationShare["img"]);
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
     * 更新动态,并返回结果
     * @param $share array 动态信息
     * @param $map array 动态筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function updateCorporationShare($share,$map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"更新参数错误！"];
        }
        $b = $this->model->table($this->table)->where($map)->data($share)->save();
        return $b;
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