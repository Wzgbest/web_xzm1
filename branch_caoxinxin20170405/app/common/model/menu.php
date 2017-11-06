<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\common\model;

use app\common\model\Base;

class menu extends Base
{
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'menu';
        parent::__construct($corp_id);
    }

    /**
     * 根据员工id获取菜单
     * @param $uid int 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getMenusByUid($uid,$status=1){
        $map['re.user_id'] = $uid;
        if($status){
            $map['mu.status'] = $status;
            $map['ru.status'] = $status;
        }
        return $this->model->table($this->table)->alias('mu')
            ->join(config('database.prefix').'rule ru','ru.menu_id = mu.id','left')
            ->join(config('database.prefix').'role_rule rr','rr.rule_id = ru.id','left')
            ->join(config('database.prefix').'role_employee re','re.role_id = rr.role_id','left')
            ->field('mu.id,rr.role_id,rr.role_id,mu.pid,mu.type,mu.name,mu.title,mu.url,mu.is_jump')
            ->group("mu.id")
            ->where($map)
            ->select();
    }
}