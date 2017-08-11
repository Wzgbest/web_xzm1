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

class CorporationShareTip extends Base{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'corporation_share_tip';
        parent::__construct($corp_id);
    }

    public function tip($user_id,$share_id,$money){
        $flg = false;
        $data['user_id'] = $user_id;
        $data['share_id'] = $share_id;
        $data['money'] = $money;
        $data['tip_time'] = time();
        try{
            $this->link->startTrans();
            $flg = $this->model->table($this->table)->insert($data);
            if(!$flg){
                exception("打赏动态失败");
            }
            $flg = $this->model->table($this->dbprefix.'corporation_share')
                ->where("id",$share_id)
                ->setInc("rewards",$money);
            if(!$flg){
                exception("更新打赏动态数失败");
            }
            $this->link->commit();
        }catch (Exception $ex){
            $this->link->rollback();
        }
        return $flg;
    }

    public function getTipList($share_id){
        $map["cst.share_id"] = $share_id;
        $map["e.status"]=1;
        $order="cst.id desc";
        $employeeList = $this->model->table($this->table)->alias('cst')
            ->join($this->dbprefix.'employee e','e.id = cst.user_id',"LEFT")
            ->where($map)
            ->order($order)
            ->group("cst.id")
            ->field("cst.*,e.truename,e.telephone,e.userpic")
            ->select();
        return $employeeList;
    }

    public function getTipEmployee($share_id){
        $map["cst.share_id"] = $share_id;
        $map["e.status"]=1;
        $order="cst.id desc";
        $employeeList = $this->model->table($this->table)->alias('cst')
            ->join($this->dbprefix.'employee e','e.id = cst.user_id',"LEFT")
            ->where($map)
            ->order($order)
            ->group("cst.user_id")
            ->field("sum(cst.money) money,count(cst.id) count,e.truename,e.telephone,e.userpic")
            ->select();
        return $employeeList;
    }

    public function getMyTip($user_id,$share_id){
        if($share_id){
            $map["cst.share_id"] = $share_id;
        }
        $map["cst.user_id"] = $user_id;
        //$map["e.status"]=1;
        $order="cst.id desc";
        $corporationShareList = $this->model->table($this->table)->alias('cst')
            //->join($this->dbprefix.'corporation_share cs','cs.id = cst.share_id',"LEFT")
            //->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            //->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = cs.id',"LEFT")
            //->join($this->dbprefix.'corporation_share_like csl','cs.id = csl.share_id',"LEFT")
            //->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->where($map)
            ->order($order)
            //->group("cst.id")
            ->field("cst.money,cst.tip_time")//cs.*,case when csl.user_id>0 then 1 else 0 end as is_like,csco.content,GROUP_CONCAT(csp.path) as img,e.telephone,e.truename,e.userpic,
            ->select();
        return $corporationShareList;
    }

    public function getMyTipMoney($user_id,$share_id){
        if($share_id){
            $map["cst.share_id"] = $share_id;
        }
        $map["cst.user_id"] = $user_id;
        //$map["e.status"]=1;
        $myTipMoney = $this->model->table($this->table)->alias('cst')
            //->join($this->dbprefix.'corporation_share cs','cs.id = cst.share_id',"LEFT")
            //->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            //->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = cs.id',"LEFT")
            //->join($this->dbprefix.'corporation_share_like csl','cs.id = csl.share_id',"LEFT")
            //->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->group("cst.user_id")
            ->where($map)
            ->field("sum(cst.money) money")
            ->find();
        if($myTipMoney&&isset($myTipMoney["money"])){
            $myTipMoney = $myTipMoney["money"];
        }else{
            $myTipMoney = 0;
        }
        return $myTipMoney;
    }
}