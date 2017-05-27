<?php
/**
 * Created by messhair.
 * Date: 2017/5/15
 */
namespace app\crm\model;

use app\common\model\Base;

class CustomerTrace extends Base
{
    public function __construct()
    {
        $this->table = config('database.prefix').'customer_trace';
        parent::__construct();
    }

    /**
     * 添加单个客户更改信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addSingleCustomerMessage($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    /**
     * 添加多个客户信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addMultipleCustomerMessage($data)
    {
        return $this->model->table($this->table)->insertAll($data);
    }

}