<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\common\model;

use app\common\model\Base;

class Rule extends Base
{
    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'rule';
        parent::__construct($corp_id);
    }

    /**
     * 获取所有权限列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllRules($status=1)
    {
        return $this->model->table($this->table)->where("status",$status)->select();
    }

    /**
     * 根据id获取权限
     * @param $ids array id列表
     * @return false|\PDOStatement|string|\think\Collection created by blu10ph
     * created by blu10ph
     */
    public function getRulesByIds($ids)
    {
        return $this->model->table($this->table)
            ->where("ids","in",$ids)
            ->select();
    }

    /**
     * 根据id获取权限数组
     * @param $ids array id列表
     * @return false|\PDOStatement|string|\think\Collection created by blu10ph
     * created by blu10ph
     */
    public function getRulesColumnByIds($ids)
    {
        return $this->model->table($this->table)
            ->where("id","in",$ids)
            ->column("rule_name,rule_title","id");
    }

    public function addRule($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function setRule($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}