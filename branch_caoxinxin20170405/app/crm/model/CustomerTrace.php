<?php
/**
 * Created by messhair.
 * Date: 2017/5/15
 */
namespace app\crm\model;

use app\common\model\Base;

class CustomerTrace extends Base
{
    public function __construct($corp_id = null)
    {
        $this->table = config('database.prefix').'customer_trace';
        parent::__construct($corp_id);
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

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllCustomerTraceByCustomerId($customer_id)
    {
        return $this->model->table($this->table)->alias('ct')
            ->join($this->dbprefix.'employee e','ct.operator_id = e.id',"LEFT")
            ->where('customer_id',$customer_id)
            ->field("ct.*,e.truename operator_user_name")
            ->order("ct.id desc")
            ->select();
    }

    /**获取
     * @param $id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTrace($id)
    {
        return $this->model->table($this->table)->where('d',$id)->find();
    }

    /**获取次数
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTraceCount($customer_id)
    {
        return $this->model->table($this->table)->where('customer_id',$customer_id)->group("create_time,operator_id")->count();
    }

    /**获取数量
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTraceLineCount($customer_id)
    {
        return $this->model->table($this->table)->where('customer_id',$customer_id)->count();
    }
}