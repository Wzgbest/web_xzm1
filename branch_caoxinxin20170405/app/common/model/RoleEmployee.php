<?php
/**
 * Created by messhair
 * Date: 17-4-7
 *
 * 该类暂时不用（废弃）
 */
namespace app\common\model;

use app\common\model\Base;

class RoleEmployee extends Base
{
    /**
     * @param $corp_id 公司名代号，非id
     */
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'role_employee';
        parent::__construct($corp_id);
    }

    /**
     * 创建用户职位连接,并返回结果
     * @param $roleEmployees array 用户职位信息数组
     * @return array
     * @throws \think\Exception
     */
    public function createRoleEmployee($roleEmployees){
        return $this->model->table($this->table)->insertGetId($roleEmployees);
    }

    /**
     * 创建用户职位连接,并返回结果
     * @param $roleEmployees array 用户职位信息数组
     * @return array
     * @throws \think\Exception
     */
    public function createMultipleRoleEmployee($roleEmployees){
        return $this->model->table($this->table)->insertAll($roleEmployees);
    }

    /**
     * 根据员工id列表查询所有角色
     * @param $userids 员工id列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getRolesByEmployeeIds($userids)
    {
        return $this->model->table($this->table)->alias('re')
            ->join(config('database.prefix').'role r','re.role_id = r.id')
            ->join(config('database.prefix').'role_rule rr','rr.role_id = r.id','left')
            ->field('re.user_id,re.role_id,r.role_name,GROUP_CONCAT( distinct rr.rule_id) as rules')
            ->where('re.user_id',"in",$userids)
            ->group('re.user_id')
            ->select();
    }

    /**
     * 根据用户id字段查询角色
     * @param $userids
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getRolesbyEmployeeId($userid)
    {
        return $this->model->table($this->table)->alias('re')
            ->join(config('database.prefix').'role r','re.role_id = r.id')
            ->join(config('database.prefix').'role_rule rr','rr.role_id = r.id','left')
            ->field('re.role_id as id,r.role_name,GROUP_CONCAT( distinct rr.rule_id) as rules')
            ->where('re.user_id',$userid)
            ->group('re.user_id')
            ->select();
    }

    /**
     * 根据用户id字段聚合查询角色
     * @param $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getRolebyEmployeeId($userid)
    {
        $role = $this->model->table($this->table)->alias('re')
            ->join(config('database.prefix').'role r','re.role_id = r.id')
            ->join(config('database.prefix').'role_rule rr','rr.role_id = r.id','left')
            ->field('GROUP_CONCAT( distinct re.role_id) as id,GROUP_CONCAT( distinct r.role_name) as role_name,GROUP_CONCAT( distinct rr.rule_id) as rules')
            ->where('re.user_id',$userid)
            ->group('re.user_id')
            ->find();
        return $role;
    }

    /**
     * 根据用户id字段聚合查询角色id
     * @param $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getRoleIdsByEmployee($userid)
    {
        $role = $this->model->table($this->table)->alias('re')
            ->join(config('database.prefix').'role r','re.role_id = r.id')
            ->join(config('database.prefix').'role_rule rr','rr.role_id = r.id','left')
            ->field('GROUP_CONCAT( distinct re.role_id) as role_id')
            ->where('re.user_id',$userid)
            ->group('re.user_id')
            ->find();
        return $role;
    }

    /**
     * 根据角色id查找员工列表
     * @param $role_id　角色id
     * @return array ['user_id'=>(int)3]
     * created by messhair
     */
    public function getEmployeeListbyRole($role_id)
    {
        return $this->model->table($this->table)
            ->field('user_id')
            ->where('role_id',$role_id)
            ->select();
    }

    /**
     * 根据角色id查询所有角色成员
     * @param $role_id 角色id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeesByRole($role_id)
    {
        return $this->model->table($this->table)->alias('re')
            ->join(config('database.prefix').'employee e','re.user_id = e.id')
            ->join(config('database.prefix').'structure_employee se','se.user_id =e.id')
            ->field('re.role_id,re.user_id,e.truename,GROUP_CONCAT( distinct se.struct_id) as structid,e.telephone,e.gender,e.age,e.email,e.qqnum,e.wechat,e.worknum,e.is_leader,e.on_duty')
            ->where('re.role_id',$role_id)
            ->select();
    }

    public function deleteMultipleRoleEmployee($user_id,$data=null){
        if (is_null($data)) {
            return $this->model->table($this->table)->where('user_id','in',$user_id)->delete();
        } else {
            $ids = implode(',',$data);
            return $this->model->table($this->table)
                ->where('user_id',$user_id)
                ->where('role_id','in', $ids)->delete();
        }
    }
}