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
     * created by blu10ph
     */
    public function getAllSaleChances(){
        $field = [
            "sc.*",
            "c.customer_name",
        ];
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->field($field)
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
    public function getAllSaleChancesByPage($num=10,$page=0,$filter=null,$field=null,$order="ca.id",$direction="desc"){
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
            "sc.*",
            "c.customer_name",
        ];
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field($field)
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
        
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->where($map)
            ->count();
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        return $map;
    }

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleChancesByCustomerId($customer_id){
        $field = [
            "sc.*",
            "scb.business_name",
            "e.truename as employee_name",
            "ae.truename as associator_name",
            "scv.visit_time",
            "scv.create_time as visit_create_time",
            "scv.visit_place",
            "scv.location",
            "scv.partner_notice",
            "scv.add_note",
            "scv.visit_ok",
            "soc.id as order_id",
            "soc.order_money",
            "soc.pay_money",
            "soc.status as order_status",
            "sob.id as bill_id",
            "sob.status as bill_status",
            "co.contract_no",
            "cs.contract_name as contract_type_name",
        ];
        $subQuery = $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'business scb','scb.id = sc.business_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'employee ae','sc.associator_id = ae.id',"LEFT")
            ->join($this->dbprefix.'sale_chance_visit scv','scv.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'sale_order_bill sob','sob.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'contract co','co.id = soc.contract_id',"LEFT")
            ->join($this->dbprefix.'contract_applied ca','ca.id = co.applied_id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->where('sc.customer_id',$customer_id)
            ->field($field)
            ->order("sc.id desc,sob.id desc")
            ->buildSql();
        return $this->model->table($subQuery)->alias('v')
            ->where('customer_id',$customer_id)
            ->group("id")
            ->order("id desc")
            ->select();
    }

    /**根据客户ID获取所有
     * @param $last_id int 最后一条销售机会id
     * @param $num int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleChancesByLastId($last_id=null,$num=10){
        $field = [
            "sc.*",
            "c.customer_name",
            "bfi.item_name as sale_status_name",
            "bfs.business_flow_name as business_name",
            "e.truename as employee_name",
            "ae.truename as associator_name",
            "scv.visit_time",
            "scv.create_time",
            "scv.visit_place",
            "scv.location",
            "scv.partner_notice",
            "scv.add_note",
            "scv.visit_ok",
            "soc.status as order_status",
        ];
        $map = [];
        if($last_id){
            $map['sc.id'] = ["lt",$last_id];
        }
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'business_flow_setting bfs','bfs.id = sc.business_id',"LEFT")
            ->join($this->dbprefix.'business_flow_item bfi','bfi.id = sc.sale_status',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'employee ae','sc.associator_id = ae.id',"LEFT")
            ->join($this->dbprefix.'sale_chance_visit scv','scv.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.sale_id = sc.id',"LEFT")
            ->where($map)
            ->field($field)
            ->order("sc.id desc")
            ->limit($num)
            ->select();
    }

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChancesByLastId($customer_id,$last_id=null,$num=10){
        $field = [
            "sc.*",
            "scb.business_name",
            "e.truename as employee_name",
            "ae.truename as associator_name",
            "scv.visit_time",
            "scv.create_time",
            "scv.visit_place",
            "scv.location",
            "scv.partner_notice",
            "scv.add_note",
            "scv.visit_ok",
            "soc.status as order_status",
        ];
        $map['sc.customer_id'] = $customer_id;
        if($last_id){
            $map['sc.id'] = ["lt",$last_id];
        }
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'business scb','scb.id = sc.business_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'employee ae','sc.associator_id = ae.id',"LEFT")
            ->join($this->dbprefix.'sale_chance_visit scv','scv.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.sale_id = sc.id',"LEFT")
            ->where($map)
            ->field($field)
            ->order("sc.id desc")
            ->limit($num)
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

    /**作废
     * @param $id int 客户商机id
     * @param $uid int 用户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function invalidSaleChance($id,$uid){
        if($uid){
            $data["employee_id"] = $uid;
        }
        $data["sale_status"] = 7;
        return $this->model->table($this->table)
            ->where('id',$id)
            ->update($data);
    }

    /**输单
     * @param $id int 客户商机id
     * @param $uid int 用户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function abandonedSaleOrderContract($id,$uid){
        $map['id'] = $id;
        $map['sale_status'] = 4;
        if($uid){
            $map["employee_id"] = $uid;
        }
        $data['sale_status'] = 6;
        return $this->model->table($this->table)
            ->where($map)
            ->update($data);
    }
}