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

class SaleChanceVisit extends Base{
    protected $dbprefix;
    public function __construct($corp_id){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'sale_chance_visit';
        parent::__construct($corp_id);
    }

    /**根据商机ID获取客户商机拜访
     * @param $sale_id int 商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceVisitBySaleId($sale_id)
    {
        return $this->model->table($this->table)->alias('scv')
            ->where('scv.sale_id',$sale_id)
            ->find();
    }

    /**获取
     * @param $id int 客户商机拜访id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceVisit($id)
    {
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**添加
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function addSaleChanceVisit($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**更新
     * @param $id int 客户商机拜访id
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChanceVisit($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**更新
     * @param $sale_id int 客户商机id
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChanceVisitBySaleId($sale_id,$data)
    {
        return $this->model->table($this->table)->where('sale_id',$sale_id)->update($data);
    }

    /**更新签到数据
     * @param $customer_id int 客户id
     * @param $lat double 经度
     * @param $lng double 纬度
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function sign_in($customer_id,$lat,$lng){
        $data["scv.sign_in_location"] = $lat.",".$lng;
        $data["sc.sale_status"] = 3;
        $map["sc.customer_id"] = $customer_id;
        $map["sc.sale_status"] = 2;
        return $this->model->table($this->table)->alias('scv')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scv.sale_id',"LEFT")
            ->where($map)
            ->update($data);
    }
}