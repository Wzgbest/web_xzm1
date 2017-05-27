<?php
/**
 * Created by messhair
 * Date: 17-3-17
 */
namespace app\huanxin\model;

use app\common\model\Base;

class TakeCash extends Base
{

    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'take_cash';
        parent::__construct($corp_id);
    }

    /**
     * 添加订单信息
     * @param $data
     * @return int|string
     */
    public function addOrderNumber($data)
    {
        return $this->model->table($this->table)->insert($data);
    }
}