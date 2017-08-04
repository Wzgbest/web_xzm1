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

    /**
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllSaleOrderContracts(){
        $field = [
            "soc.*",
            "e.truename as employee_name",
            "sc.business_id",
            "sc.sale_name",
            "sc.sale_status",
            "sc.guess_money",
            "sc.need_money",
            "sc.payed_money",
            "c.customer_name",
            "GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name`",
        ];
        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->field($field)
            ->group("soc.id")
            ->select();
    }

    /**
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 合同筛选条件
     * @param $field array 合同列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleOrderContractByPage($num=10,$page=0,$filter=null,$field=null,$order="ca.id",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $field = [
            "soc.*",
            "e.truename as employee_name",
            "sc.business_id",
            "sc.sale_name",
            "sc.sale_status",
            "sc.guess_money",
            "sc.need_money",
            "sc.payed_money",
            "c.customer_name",
            "GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name`",
        ];
        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field($field)
            ->group("soc.id")
            ->select();
    }

    /**
     * @param $filter array 合同筛选条件
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleChanceCount($filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->group("soc.id")
            ->where($map)
            ->count();
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        return $map;
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
    public function setSaleOrderContractBySaleId($sale_id,$data)
    {
        return $this->model->table($this->table)->where('sale_id',$sale_id)->update($data);
    }

    /**撤回
     * @param $id int 客户商机id
     * @param $uid int 用户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function retractSaleOrderContract($id,$uid){
        $map['sc.id'] = $id;
        $data["soc.status"] = 0;
        $map['sc.sale_status'] = 4;
        if($uid){
            $map["sc.employee_id"] = $uid;
        }
        $data["soc.status"] = 3;
        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->where($map)
            ->update($data);
    }

    /**通过
     * @param $id int 客户商机id
     * @param $uid int 用户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function approvedSaleOrderContract($id,$uid){
        $map['sc.id'] = $id;
        $data["soc.status"] = 0;
        $map['sc.sale_status'] = 4;
        if($uid){
            $map["sc.employee_id"] = $uid;
        }
        $data["soc.status"] = 1;
        $data['sc.sale_status'] = 5;
        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->where($map)
            ->update($data);
    }

    /**驳回
     * @param $id int 客户商机id
     * @param $uid int 用户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function rejectedSaleOrderContract($id,$uid){
        $map['sc.id'] = $id;
        $data["soc.status"] = 0;
        $map['sc.sale_status'] = 4;
        if($uid){
            $map["sc.employee_id"] = $uid;
        }
        $data["soc.status"] = 2;
        //$data['sc.sale_status'] = 6;
        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->where($map)
            ->update($data);
    }
}