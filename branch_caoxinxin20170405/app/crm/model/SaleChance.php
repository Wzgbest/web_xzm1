<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;

class SaleChance extends Base
{
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'sale_chance';
        parent::__construct($corp_id);
    }

    public function getAllSaleChances()
    {
        return $this->model->table($this->table)->select();
    }
}