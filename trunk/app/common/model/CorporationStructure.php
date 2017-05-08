<?php
/**
 * Created by messhair
 * Date: 17-2-17
 */
namespace app\common\model;

use app\common\model\Base;

class CorporationStructure extends Base
{
    public function __construct($corp_id)
    {
        $this->table=config('database.prefix').'corporation_structure';
        parent::__construct($corp_id);
    }

    /**
     * 获取所有部门信息
     * @return mixed
     */
    public function getAllStructure()
    {
        return $this->model->table($this->table)->select();
    }

    /**
     * 获取单个部门信息
     * @param $struct_id 部门id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getStructureInfo($struct_id)
    {
        return $this->model->table($this->table)->where('id',$struct_id)->find();
    }

    /**
     * 添加部门信息
     * @param $data
     * @return int|string
     */
    public function addStructure($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    /**
     * 更新单个部门信息
     * @param $id 部门id
     * @param $data 数据信息
     * @return int|string
     * @throws \think\Exception
     */
    public function setStructure($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除部门
     * @param $id 部门id
     * @return int
     * @throws \think\Exception
     */
    public function deleteStructure($id)
    {
        return $this->model->table($this->table)->where('id',$id)->delete();
    }
}