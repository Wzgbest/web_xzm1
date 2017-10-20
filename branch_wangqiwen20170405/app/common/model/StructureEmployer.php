<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\common\model;

use app\common\model\Base;

class StructureEmployer extends Base
{
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'structure_employer';
        parent::__construct($corp_id);
    }

    /**
     * 根据部门struct_ids获取所有员工id
     * @param $struct_ids 存放部门信息的ids
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getEmployerByStructIds($struct_ids)
    {
        return $this->model->table($this->table)->where('struct_id','in',$struct_ids)->field('user_id')->select();
    }

    /**
     * 更改单个员工的部门
     * @param $user_id 员工id
     * @param $struct_id 部门id
     * @param $data  数据
     * @return int|string
     * @throws \think\Exception
     */
    public function setStructureEmployerById($user_id,$struct_id,$data)
    {
        return $this->model->table($this->table)
            ->where('user_id',$user_id)
            ->where('struct_id',$struct_id)
            ->update($data);
    }
    /**
     * 根据用户ids更新部门信息
     * @param $user_ids 用户id逗号分隔
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setStructureEmployerbyIds($user_ids,$data)
    {
        return $this->model->table($this->table)->where('user_id','in',$user_ids)->update($data);
    }

    /**
     * 增加部门员工
     * @param $data
     * @return int|string
     */
    public function addStructureEmployer($data)
    {
        return $this->model->table($this->table)->insert($data);
    }
}