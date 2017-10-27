<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\common\model;

use app\common\model\Base;

class StructureEmployee extends Base
{
    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'structure_employee';
        parent::__construct($corp_id);
    }

    /**
     * 根据部门struct_ids获取所有员工id
     * @param $struct_ids 存放部门信息的ids
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByStructIds($struct_ids)
    {
        return $this->model->table($this->table)->where('struct_id','in',$struct_ids)->field('user_id')->select();
    }

    /**
     * 根据员工id获取部门ids
     * @param $user_id 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getStructIdsByEmployee($user_id)
    {
        return $this->model->table($this->table)->where('user_id',$user_id)->field('struct_id')->select();
    }


    /**
     * 根据员工id获取部门ids
     * @param $user_ids array 员工id列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getStructIdsByEmployeeIds($user_ids)
    {
        $link_list = $this->model->table($this->table)->where('user_id',"in",$user_ids)->field('user_id,struct_id')->select();
        $user_struct_ids = [];
        foreach ($link_list as $link){
            $user_struct_ids[$link["user_id"]][] = $link["struct_id"];
        }
        return $user_struct_ids;
    }

    /**
     * 更改单个员工的部门
     * @param $user_id 员工id
     * @param $struct_id 部门id
     * @param $data  数据
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setStructureEmployeeById($user_id,$struct_id,$data)
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
     * created by messhair
     */
    public function setStructureEmployeebyIds($user_ids,$struct_id,$data)
    {
        return $this->model->table($this->table)
            ->where('user_id','in',$user_ids)
            ->where('struct_id',$struct_id)
            ->update($data);
    }

    /**
     * 增加部门员工
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addStructureEmployee($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    /**
     * 批量增加部门员工
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addMultipleStructureEmployee($data)
    {
        return $this->model->table($this->table)->insertAll($data);
    }

    /**
     * 删除员工的部门信息
     * @param $user_id 员工id
     * @param $data
     * @return int
     * @throws \think\Exception
     * created by messhair
     */
    public function deleteMultipleStructureEmployee($user_id,$data=null)
    {
        if (is_null($data)) {
            return $this->model->table($this->table)->where('user_id','in',$user_id)->delete();
        } else {
            $ids = implode(',',$data);
            return $this->model->table($this->table)
                ->where('user_id',$user_id)
                ->where('struct_id','in', $ids)->delete();
        }
    }

    /**
     * 查询员工所有部门信息
     * @param $user_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeStructure($user_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'structure b','a.struct_id = b.id')
            ->field('a.struct_id,b.struct_name')
            ->where('a.user_id',$user_id)
            ->select();
    }


    /**
     * 查询员工所有部门信息
     * @param $user_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllStructureAndEmployee()
    {
        return $this->model->table($this->table)->alias('se')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->group("s.id")
            ->field('s.id,s.struct_pid,s.struct_name,GROUP_CONCAT( distinct se.user_id) as employee_ids')
            ->select();
    }

 /**
     * 根据员工id获取群组id
     * @param $user_id 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getGroupIdsByEmployee($user_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'structure b','a.struct_id = b.id')
            ->field('b.groupid')
            ->where('a.user_id',$user_id)
            ->select();
    }

    /**
     * 查询员工部门信息
     * @param $user_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function findEmployeeStructure($user_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'structure b','a.struct_id = b.id')
            ->field('a.struct_id,b.struct_name')
            ->where('a.user_id',$user_id)
            ->order("id desc")
            ->find();
    }

    /*
    查询是否存在该条信息
     */
    public function getOneInfo($user_id,$group){
        return $this->model->table($this->table)->where(['user_id'=>$user_id,'struct_id'=>$group])->find();
    }
    /*
    删除部门员工
     */
    public function delStructureEmployee($struct_id,$data=[]){
        if (empty($data)) {
            return $this->model->table($this->table)->where('struct_id','in',$struct_id)->delete();
        } else {
            return $this->model->table($this->table)
                ->where('struct_id',$struct_id)
                ->where('user_id','in', $data)->delete();
        }
    }
}