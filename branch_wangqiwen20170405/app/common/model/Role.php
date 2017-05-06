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
        $this->table=config('database.prefix').'role_employer';
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

    public function addRole($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    public function setRole($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}