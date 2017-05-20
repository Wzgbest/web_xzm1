<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\common\model;

use app\common\model\Base;

class Business extends Base
{
    public function __construct($corp_id = null)
    {
        $this->table = config('database.prefix').'business';
        parent::__construct($corp_id);
    }

    /**
     * 获取所有业务
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllBusiness()
    {
        return $this->model->table($this->table)->select();
    }

    public function addBusiness($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    public function setBusiness($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}