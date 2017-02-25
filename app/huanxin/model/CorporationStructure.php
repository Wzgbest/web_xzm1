<?php
/**
 * Created by messhair
 * Date: 17-2-17
 */
namespace app\huanxin\model;

use app\huanxin\model\Base;

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
}