<?php
/**
 * Created by messhair
 * Date: 17-4-7
 */
namespace app\common\model;

use app\common\model\Base;

class Role extends Base
{
    /**
     * @param $corp_id 公司名代号，非id
     * created by messhair
     */
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'role';
        parent::__construct($corp_id);
    }

    /**
     * 获取所有角色名称
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllRole()
    {
        return $this->model->table($this->table)->alias('ro')
            ->join(config('database.prefix').'role_rule rr','rr.role_id = ro.id','left')
            ->group("ro.id")
            ->field('ro.id,ro.role_name,GROUP_CONCAT( distinct rr.rule_id) as rules')
            ->select();
    }

    /**
     * 根据角色id查询
     * @param $role_id int 角色id
     * @return mixed
     * created by messhair
     */
    public function getRoleInfo($role_id)
    {
        return $this->model->table($this->table)->alias('ro')
            ->join(config('database.prefix').'role_rule rr','rr.role_id = ro.id','left')
            ->where('ro.id',$role_id)
            ->group("ro.id")
            ->field('ro.id,ro.role_name,GROUP_CONCAT( distinct rr.rule_id) as rules')
            ->find();
    }

    /**
     * 添加角色
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addRole($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 更新角色
     * @param $id 角色id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setRole($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除角色
     * @param $role_id
     * @return int
     * @throws \think\Exception
     * created by messhair
     */
    public function deleteRole($role_id)
    {
        return $this->model->table($this->table)->where('id',$role_id)->delete();
    }
}
