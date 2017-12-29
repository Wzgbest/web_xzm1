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

class SaleOrderContractItem extends Base{
    protected $dbprefix;
    public function __construct($corp_id){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'sale_order_contract_item';
        parent::__construct($corp_id);
    }

    public function getContractInfo($id){
        return $this->model->table($this->table)->alias('soci')
            ->join($this->dbprefix.'sale_order_contract soc','soci.sale_order_id = soc.id',"LEFT")
            ->join($this->dbprefix.'contract co','co.id = soci.contract_id',"LEFT")
            ->where('soci.id',$id)
            ->field("soci.*,co.contract_no")
            ->find();
    }

    public function getContractItemBySaleId($sale_id){
        return $this->model->table($this->table)->alias('soci')
            ->join($this->dbprefix.'sale_order_contract soc','soci.sale_order_id = soc.id',"LEFT")
            ->where('soc.sale_id',$sale_id)
            ->field("soci.*")
            ->select();
    }

    public function getContractItemBySaleIdArr($sale_id_arr){
        if(empty($sale_id_arr)){
            return [];
        }
        $field = [
            "soci.*",
            "cs.contract_name as contract_type_name",
            "co.contract_no",
            "sob.id as bill_id",
            "sob.status as bill_status",
        ];
        return $this->model->table($this->table)->alias('soci')
            ->join($this->dbprefix.'sale_order_contract soc','soci.sale_order_id = soc.id',"LEFT")
            ->join($this->dbprefix.'contract co','co.id = soci.contract_id',"LEFT")
            ->join($this->dbprefix.'contract_applied ca','ca.id = co.applied_id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->join($this->dbprefix.'sale_order_bill sob','sob.contract_id = soci.contract_id',"LEFT")
            ->where('soc.sale_id',"in",$sale_id_arr)
            ->field($field)
            ->select();
    }

    /**添加
     * @param $data array 销售单合同数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function addContractItem($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**添加多个
     * @param $datas array 销售单合同数据数组
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function addMultipleContractItem($datas)
    {
        return $this->model->table($this->table)->insertAll($datas);
    }

    /**更新
     * @param $id int 销售单id
     * @param $data array 销售单数据
     * @param $map array 筛选条件
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setContractItem($id,$data,$map=null){
        return $this->model->table($this->table)->where('id',$id)->where($map)->update($data);
    }

    /**删除
     * @param $ids array 销售单数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function delContractItem($ids)
    {
        return $this->model->table($this->table)->delete($ids);
    }
}