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

class CorporationShareLike extends Base{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'corporation_share_like';
        parent::__construct($corp_id);
    }

    public function like($user_id,$share_id){
        $flg = false;
        $data['user_id'] = $user_id;
        $data['share_id'] = $share_id;
        try{
            $this->link->startTrans();
            $flg = $this->model->table($this->table)->insert($data);
            if(!$flg){
                exception("喜欢动态失败");
            }
            $flg = $this->model->table($this->dbprefix.'corporation_share')
                ->where("id",$share_id)
                ->setInc("good_count");
            $this->link->commit();
        }catch (Exception $ex){
            $this->link->rollback();
        }
        return $flg;
    }

    public function not_like($user_id,$share_id){
        $flg = false;
        $map['user_id'] = $user_id;
        $map['share_id'] = $share_id;
        try{
            $this->link->startTrans();
            $flg = $this->model->table($this->table)->where($map)->delete();
            if(!$flg){
                exception("不喜欢动态失败");
            }
            $flg = $this->model->table($this->dbprefix.'corporation_share')
                ->where("id",$share_id)
                ->setDec("good_count");
            $this->link->commit();
        }catch (Exception $ex){
            $this->link->rollback();
        }
        return $flg;
    }

    public function getLikeEmployee($share_id){
        $map["cs.id"] = $share_id;
        $map["e.status"]=1;
        $order="cs.id desc";
        $employeeList = $this->model->table($this->table)->alias('cs')
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->where($map)
            ->order($order)
            ->group("e.id")
            ->field("e.truename,e.telephone,e.userpic")
            ->select();
        return $employeeList;
    }

    public function getLikeShare($user_id){
        $map["cs.create_user"] = $user_id;
        $map["e.status"]=1;
        $order="cs.id desc";
        $corporationShareList = $this->model->table($this->table)->alias('cs')
            ->join($this->dbprefix.'corporation_share_content csco','cs.content_id = csco.id',"LEFT")
            ->join($this->dbprefix.'corporation_share_picture csp','csp.content_id = cs.id',"LEFT")
            ->join($this->dbprefix.'employee e','e.id = cs.userid',"LEFT")
            ->where($map)
            ->order($order)
            ->group("cs.id")
            ->field("cs.*,csco.content,GROUP_CONCAT(csp.path) as img")//TODO
            ->select();
        return $corporationShareList;
    }
}