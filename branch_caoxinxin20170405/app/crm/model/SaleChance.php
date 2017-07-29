<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;

class SaleChance extends Base
{
    protected $dbprefix;
    public function __construct($corp_id)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'sale_chance';
        parent::__construct($corp_id);
    }

    /**
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllSaleChances()
    {
        return $this->model->table($this->table)->select();
    }

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleChancesByCustomerId($customer_id)
    {
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'business scb','scb.id = sc.business_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'employee ae','sc.associator_id = ae.id',"LEFT")
            ->where('customer_id',$customer_id)
            ->field("sc.*,scb.business_name,e.truename as employee,ae.truename as associator,c.remark")
            ->select();
    }

    /**获取数量
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceCount($customer_id)
    {
        return $this->model->table($this->table)->where('customer_id',$customer_id)->count();
    }

    /**添加
     * @param $data array 客户商机数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function addSaleChance($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**获取
     * @param $id int 客户商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChance($id)
    {
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**更新
     * @param $id int 客户商机id
     * @param $data array 客户商机数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChance($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**作废
     * @param $id int 客户商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function invalidSaleChance($id)
    {
        $data["sale_status"] = 7;
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**获取对应客户的正在进行的商机预计成单金额总额
     * @param $customer_ids array 客户ID列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getAllGuessMoneyByCustomerIds($customer_ids){
        $map["sale_status"] = ["in",[1,2,3,4]];
        $map["customer_id"] = ["in",$customer_ids];
        return $this->model->table($this->table)
            ->where($map)
            ->group("customer_id")
            ->column("SUM(guess_money)","customer_id");
    }

    /**获取对应客户所有已成单成单金额总额
     * @param $customer_ids array 客户ID列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getAllFinalMoneyByCustomerIds($customer_ids){
        $map["sale_status"] = 5;
        $map["customer_id"] = ["in",$customer_ids];
        return $this->model->table($this->table)
            ->where($map)
            ->group("customer_id")
            ->column("SUM(final_money)","customer_id");
    }
}