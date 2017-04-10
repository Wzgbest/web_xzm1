<?php
/**
 * Created by messhair
 * Date: 17-4-7
 */
namespace app\common\model;

use app\common\model\Base;

class RoleEmployer extends Base
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
     * 根据用户id字段查询角色
     * @param $userid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getRolebyEmployerId($userid)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'role b','a.role_id = b.id')
            ->field('a.user_id,a.role_id,b.role_name')
            ->where('a.user_id',$userid)
            ->find();
    }

    /**
     * 根据角色id查找员工列表
     * @param $role_id
     * @return array
     */
    public function getEmployerListbyRole($role_id)
    {
        return $this->model->table($this->table)->field('user_id')->where('role_id',$role_id)->select();
    }

}