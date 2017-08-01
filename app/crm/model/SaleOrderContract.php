<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\model;

use app\common\model\Base;

class SaleOrderContract extends Base{
    protected $dbprefix;
    public function __construct($corp_id){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'sale_order_contract';
        parent::__construct($corp_id);
    }

    /**根据商机ID获取销售单
     * @param $sale_id int 商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleOrderContractBySaleId($sale_id)
    {
        return $this->model->table($this->table)->alias('soc')
            ->where('soc.sale_id',$sale_id)
            ->find();
    }

    /**获取
     * @param $id int 销售单id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleOrderContract($id)
    {
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**添加
     * @param $data array 销售单数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function addSaleOrderContract($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**更新
     * @param $id int 销售单id
     * @param $data array 销售单数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleOrderContract($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**更新
     * @param $sale_id int 客户商机id
     * @param $data array 销售单数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChanceVisitBySaleId($sale_id,$data)
    {
        return $this->model->table($this->table)->where('sale_id',$sale_id)->update($data);
    }
}