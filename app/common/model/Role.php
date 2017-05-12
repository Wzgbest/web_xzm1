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
     */
    public function __construct($corp_id)
    {
        $this->table=config('database.prefix').'role';
        parent::__construct($corp_id);
    }

    /**
     * 获取所有角色名称
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllRole()
    {
        return $this->model->table($this->table)->field('id,role_name')->select();
    }

    /**
     * 根据角色id查询
     * @param $role_id 角色id
     * @return mixed
     */
    public function getRoleName($role_id)
    {
        return $this->model->table($this->table)->where('id',$role_id)->value('role_name');
    }

    /**
     * 添加角色
     * @param $data
     * @return int|string
     */
    public function addRole($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    /**
     * 更新角色
     * @param $id 角色id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setRole($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}