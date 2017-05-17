<?php
/**
 * Created by: messhair
 * Date: 2017/5/5
 */
namespace app\common\model;

use app\common\model\Base;

class RoleRule extends Base
{
    public function __construct($corp_id)
    {
        $this->table=config('database.prefix').'role_rule';
        parent::__construct($corp_id);
    }

    /**
     * 根据角色id查找对应的权限
     * @param $role_id 角色id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRulesByRole($role_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'rule b','a.rule_id = b.id')
            ->field('a.role_id,a.rule_id,b.rule_name,b.status,b.rule_title')
            ->where('a.role_id',$role_id)->select();
    }

    /**
     * 根据权限id查找对应的角色
     * @param $rule_id 权限id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRolesByRule($rule_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'role b','a.role_id = b.id')
            ->field('a.rule_id,a.role_id,b.role_name')
            ->where('a.rule_id',$rule_id)->select();
    }
}