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
    public function getAllRules()
    {
        return $this->model->table($this->table)->select();
    }

    public function addRule($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    public function setRule($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}