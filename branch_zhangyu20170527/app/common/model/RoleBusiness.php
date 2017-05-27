<?php
/**
 * Created by: messhair
 * Date: 2017/5/5
 */
namespace app\common\model;

use app\common\model\Base;

class RoleBusiness extends Base
{
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'role_business';
        parent::__construct($corp_id);
    }

    /**
     * 根据业务id查找对应的所有角色
     * @param $business_id 业务id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getRoleByBusiness($business_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'role b','a.role_id = b.id')
            ->field('a.business_id,a.role_id,b.role_name')
            ->where('a.business_id',$business_id)
            ->select();
    }

    /**
     * 根据角色id查找对应的所有业务
     * @param $role_id 角色id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getBusinessByRole($role_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'business b','a.business_id = b.id')
            ->field('a.role_id,a.business_id,b.business_name')
            ->where('a.role_id',$role_id)->select();
    }
}