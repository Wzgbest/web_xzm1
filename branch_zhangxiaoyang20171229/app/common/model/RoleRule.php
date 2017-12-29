<?php
/**
 * Created by: messhair
 * Date: 2017/5/5
 * 该类暂时废弃
 */
namespace app\common\model;

use app\common\model\Base;

class RoleRule extends Base
{
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'role_rule';
        parent::__construct($corp_id);
    }

    /**
     * 根据员工id查找对应的权限
     * @param $uid int 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getRulesByUid($uid,$status=1){
        $map['re.user_id'] = $uid;
        if($status){
            $map['ru.status'] = $status;
        }
        return $this->model->table($this->table)->alias('rr')
            ->join(config('database.prefix').'role_employee re','rr.role_id = re.role_id')
            ->join(config('database.prefix').'rule ru','rr.rule_id = ru.id')
            ->field('ru.id,rr.role_id,ru.rule_name,ru.rule_title')
            ->group("ru.id")
            ->where($map)
            ->select();
    }

    /**
     * 根据员工id查找对应的权限
     * @param $uid int 员工id
     * @return array
     * created by blu10ph
     */
    public function getRuleNamesByUid($uid,$status=1){
        $map['ru.type'] = 3;
        $map['re.user_id'] = $uid;
        if($status){
            $map['ru.status'] = $status;
        }
        return $this->model->table($this->table)->alias('rr')
            ->join(config('database.prefix').'role_employee re','rr.role_id = re.role_id')
            ->join(config('database.prefix').'rule ru','rr.rule_id = ru.id')
            ->group("ru.id")
            ->where($map)
            ->column('ru.rule_name','ru.id');
    }

    /**
     * 根据员工id获取菜单
     * @param $uid int 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getMenusByUid($uid,$status=1){
        $map['ru.type'] = ["in",[0,1]];
        $map['re.user_id'] = $uid;
        if($status){
            $map['ru.status'] = $status;
        }
        return $this->model->table($this->table)->alias('rr')
            ->join(config('database.prefix').'rule ru','ru.id = rr.rule_id','left')
            ->join(config('database.prefix').'role_employee re','re.role_id = rr.role_id','left')
            ->field('ru.id,rr.role_id,ru.pid,ru.type,ru.rule_name,ru.rule_title,ru.class,ru.name,ru.url,ru.is_jump')
            ->group("ru.id")
            ->order("ru.pid,ru.sort,ru.id")
            ->where($map)
            ->select();
    }

    /**
     * 根据角色id查找对应的权限
     * @param $role_id 角色id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getRulesByRole($role_id)
    {
        return $this->model->table($this->table)->alias('rr')
            ->join(config('database.prefix').'rule ru','rr.rule_id = ru.id')
            ->where('rr.role_id',$role_id)
            ->group("ru.id")
            ->field('ru.id,rr.role_id,ru.pid,ru.rule_name,ru.rule_title,ru.status')
            ->order("ru.pid,ru.sort,ru.id")
            ->select();
    }

    /**
     * 按角色id查询
     * @param $role_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getRuleIdByRoleId($role_id)
    {
        return $this->model->table($this->table)
            ->where('role_id',$role_id)
            ->field('rule_id')
            ->select();
    }

    /**
     * 根据权限id查找对应的角色
     * @param $rule_id 权限id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getRolesByRule($rule_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'role b','a.role_id = b.id')
            ->field('a.rule_id,a.role_id,b.role_name')
            ->where('a.rule_id',$rule_id)
            ->select();
    }

    public function addRoleRule($data)
    {
        return $this->model->table($this->table)->insertAll($data);
    }

    /**
     * 删除角色id对应的权限id
     * @param $role_id
     * @return int
     * @throws \think\Exception
     * created by messhair
     */
    public function deleteRoleRule($role_id,$rule_ids)
    {
        return $this->model->table($this->table)
            ->where('role_id',$role_id)
            ->where('rule_id','in',$rule_ids)
            ->delete();
    }
}