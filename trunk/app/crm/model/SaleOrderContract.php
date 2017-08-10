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
    public function getAllSaleOrderContractByPage($num=10,$page=0,$filter=null,$field=null,$order="soc.create_time",$direction="desc"){
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
            "co.contract_no",
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
            ->join($this->dbprefix.'contract co','co.id = soc.contract_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->limit($offset,$num)
            ->field($field)
            ->group("soc.id")
            ->order($order)
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

    /**
     * 查询列上的数量
     * @param $uid int 员工id
     * @param $filter array 过滤条件
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function getAllColumnNum($uid,$filter=null){

        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        $field = [
            "(case when ca.status = 0 then 1 
            when ca.status = 2 then 3 
            when ca.status = 3 then 4 
            when ca.status = 1 and co.status = 6 then 5 
            when ca.status = 1 and sc.sale_status = 4 and soc.status = 0 then 6 
            when ca.status = 1 and sc.sale_status = 5 and soc.status = 1 then 7 
            when ca.status = 1 and (co.status = 1 or co.status = 4 or co.status = 5 or co.status = 7 or co.status = 8) then 2 
            else 8 end ) as in_column",
        ];
        $getCountField = [
            "(case when in_column = 1 then 1 else 0 end) as `1`",
            "(case when in_column = 2 then 1 else 0 end) as `2`",
            "(case when in_column = 3 then 1 else 0 end) as `3`",
            "(case when in_column = 4 then 1 else 0 end) as `4`",
            "(case when in_column = 5 then 1 else 0 end) as `5`",
            "(case when in_column = 6 then 1 else 0 end) as `6`",
            "(case when in_column = 7 then 1 else 0 end) as `7`",
        ];
        $countField = [
            "count(*) as `0`",
            "sum(`1`) as `1`",
            "sum(`2`) as `2`",
            "sum(`3`) as `3`",
            "sum(`4`) as `4`",
            "sum(`5`) as `5`",
            "sum(`6`) as `6`",
            "sum(`7`) as `7`",
        ];

        $customerQuery = $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract co','co.applied_id = ca.id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.contract_id = co.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','c.id = sc.customer_id',"LEFT")
            ->join($this->dbprefix.'business_flow_setting bfs','bfs.id = sc.business_id',"LEFT")
            ->where($map)
            ->group("ca.id,co.group_field")
            ->field($field)
            ->buildSql();
        //var_exp($contractAppliedList,'$contractAppliedList',1);
        $getListCount = $this->model
            ->table($customerQuery." glc")
            ->field($getCountField)
            ->buildSql();
        //var_exp($getListCount,'$listCount');
        $listCount = $this->model
            ->table($getListCount." lc")
            ->field($countField)
            ->find();
        //var_exp($listCount,'$listCount',1);
        return $listCount;
    }

    /**
     * @param $uid int 员工id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 合同筛选条件
     * @param $field array 合同列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getVerificationSaleOrderContractByPage($uid,$num=10,$page=0,$filter=null,$field=null,$order="soc.create_time",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,["contract_type","structure","business_id","pay_type","order_status","contract_no","apply_employee","customer_name"]);
        if(!isset($map["soc.status"])){
            $map["soc.status"] = ["neq",3];
        }
        //$map["soc.handle_now"] = $uid;
        $mapStr = "find_in_set('".$uid."',soc.handle_now)";
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " (case when sc.sale_status = 4 and soc.status = 0 then 1 
            when sc.sale_status = 5 and soc.status = 1 then 7 
            when sc.sale_status = 4 and soc.status = 2 then 8 
            when sc.sale_status = 9 then 9 
            else 10 end ) = $in_column ";
            }
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $field = [
            "soc.*",
            "co.contract_no",
            "e.truename as employee_name",
            "sc.business_id",
            "sc.sale_name",
            "sc.sale_status",
            "sc.guess_money",
            "sc.need_money",
            "sc.payed_money",
            "c.customer_name",
            "GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name`",
            "(case when sc.sale_status = 4 and soc.status = 0 then 1 
            when sc.sale_status = 5 and soc.status = 1 then 7 
            when sc.sale_status = 4 and soc.status = 2 then 8 
            when sc.sale_status = 9 then 9 
            else 10 end ) as in_column",
        ];
        $query = $this->model->table($this->table)->alias('soc');
        $sale_chance_list = $query
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'contract co','co.id = soc.contract_id',"LEFT")
            ->join($this->dbprefix.'contract_applied ca','ca.id = co.applied_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure_employee ses','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->where($mapStr)
            ->limit($offset,$num)
            ->field($field)
            ->group("soc.id")
            ->order($order)
            ->having($having)
            ->select();
        //var_exp($sale_chance_list,'$sale_chance_list',1);
        //var_exp($query->getLastSql(),'lastsql',1);
        return $sale_chance_list;
    }

    /**
     * @param $uid int 员工id
     * @param $filter array 合同筛选条件
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getVerificationSaleChanceCount($uid,$filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,["contract_type","structure","business_id","pay_type","order_status","contract_no","apply_employee","customer_name"]);
        $map["soc.status"] = ["neq",3];
        //$map["soc.handle_now"] = $uid;
        $mapStr = "find_in_set('".$uid."',soc.handle_now)";
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " (case when sc.sale_status = 4 and soc.status = 0 then 1 
            when sc.sale_status = 5 and soc.status = 1 then 7 
            when sc.sale_status = 4 and soc.status = 2 then 8 
            when sc.sale_status = 9 then 9 
            else 10 end ) = $in_column ";
            }
        }

        $field = [
            "soc.status",
            "sc.sale_status",
            "(case when sc.sale_status = 4 and soc.status = 0 then 1 
            when sc.sale_status = 5 and soc.status = 1 then 7 
            when sc.sale_status = 4 and soc.status = 2 then 8 
            when sc.sale_status = 9 then 9 
            else 10 end ) as in_column",
        ];

        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'contract co','co.id = soc.contract_id',"LEFT")
            ->join($this->dbprefix.'contract_applied ca','ca.id = co.applied_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure_employee ses','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->where($mapStr)
            ->field($field)
            ->group("soc.id")
            ->having($having)
            ->count();
    }

    /**
     * 查询列上的数量
     * @param $uid int 员工id
     * @param $filter array 过滤条件
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function getVerificationColumnNum($uid,$filter=null){

        //筛选
        $map = $this->_getMapByFilter($filter,["contract_type","structure","business_id","pay_type","order_status","contract_no","apply_employee","customer_name"]);
        $map["soc.status"] = ["neq",3];
        //$map["soc.handle_now"] = $uid;
        $mapStr = "find_in_set('".$uid."',soc.handle_now)";

        $field = [
            "(case when sc.sale_status = 4 and soc.status = 0 then 1 
            when sc.sale_status = 5 and soc.status = 1 then 7 
            when sc.sale_status = 4 and soc.status = 2 then 8 
            when sc.sale_status = 9 then 9 
            else 10 end ) as in_column",
        ];
        $getCountField = [
            "(case when in_column = 1 then 1 else 0 end) as `1`",
            "(case when in_column = 2 then 1 else 0 end) as `2`",
            "(case when in_column = 3 then 1 else 0 end) as `3`",
            "(case when in_column = 4 then 1 else 0 end) as `4`",
            "(case when in_column = 5 then 1 else 0 end) as `5`",
            "(case when in_column = 6 then 1 else 0 end) as `6`",
            "(case when in_column = 7 then 1 else 0 end) as `7`",
            "(case when in_column = 8 then 1 else 0 end) as `8`",
            "(case when in_column = 9 then 1 else 0 end) as `9`",
        ];
        $countField = [
            "count(*) as `0`",
            "sum(`1`) as `1`",
            "sum(`2`) as `2`",
            "sum(`3`) as `3`",
            "sum(`4`) as `4`",
            "sum(`5`) as `5`",
            "sum(`6`) as `6`",
            "sum(`7`) as `7`",
            "sum(`8`) as `8`",
            "sum(`9`) as `9`",
        ];

        $customerQuery = $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'contract co','co.id = soc.contract_id',"LEFT")
            ->join($this->dbprefix.'contract_applied ca','ca.id = co.applied_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure_employee ses','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->where($mapStr)
            ->group("soc.id")
            ->field($field)
            ->buildSql();
        //var_exp($contractAppliedList,'$contractAppliedList',1);
        $getListCount = $this->model
            ->table($customerQuery." glc")
            ->field($getCountField)
            ->buildSql();
        //var_exp($getListCount,'$listCount');
        $listCount = $this->model
            ->table($getListCount." lc")
            ->field($countField)
            ->find();
        if($listCount["0"]==0){
            foreach ($listCount as &$count){
                $count = 0;
            }
        }
        //var_exp($listCount,'$listCount',1);
        return $listCount;
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        //合同类型
        if(in_array("contract_type",$filter_column) && array_key_exists("contract_type", $filter)){
            $map["ca.contract_type"] = $filter["contract_type"];
        }
        //对应部门
        if(in_array("structure",$filter_column) && array_key_exists("structure", $filter)){
            $map["ses.struct_id"] = $filter["structure"];
        }
        //对应业务
        if(in_array("business_id",$filter_column) && array_key_exists("business_id", $filter)){
            $map["sc.business_id"] = $filter["business_id"];
        }
        //打款方式
        if(in_array("pay_type",$filter_column) && array_key_exists("pay_type", $filter)){
            $map["soc.pay_type"] = $filter["pay_type"];
        }
        //订单状态
        if(in_array("order_status",$filter_column) && array_key_exists("order_status", $filter)){
            $map["soc.status"] = $filter["order_status"];
        }
        //合同号
        if(in_array("contract_no",$filter_column) && array_key_exists("contract_no", $filter)){
            $map["co.contract_no"] = ["like","%".$filter["contract_no"]."%"];
        }
        //负责人
        if(in_array("apply_employee",$filter_column) && array_key_exists("apply_employee", $filter)){
            $map["e.truename"] = ["like","%".$filter["apply_employee"]."%"];
        }
        //客户名称
        if(in_array("customer_name",$filter_column) && array_key_exists("customer_name", $filter)){
            $map["c.customer_name"] = ["like","%".$filter["customer_name"]."%"];
        }
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
     * @param $map array 筛选条件
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleOrderContract($id,$data,$map=null){
        return $this->model->table($this->table)->where('id',$id)->where($map)->update($data);
    }

    /**更新
     * @param $sale_id int 客户商机id
     * @param $data array 销售单数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleOrderContractBySaleId($sale_id,$data)
    {
        return $this->model->table($this->table)
            ->where('sale_id',$sale_id)
            ->where('status',"in",[2,3])
            ->update($data);
    }

    /**撤回
     * @param $id int 客户商机id
     * @param $uid int 用户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function retractSaleOrderContract($id,$uid){
        $map['sc.id'] = $id;
        $map["soc.status"] = 0;
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
     * @param $data array 销售单数据
     * @param $map array 筛选条件
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function approvedSaleOrderContract($id,$data,$map){
        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->where('soc.id',$id)
            ->where($map)
            ->update($data);
    }

    /**驳回
     * @param $id int 客户商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function rejectedSaleOrderContract($id){
        $map['soc.id'] = $id;
        $map["soc.status"] = 0;
        $map['sc.sale_status'] = 4;
        $data["soc.status"] = 2;
        //$data['sc.sale_status'] = 6;
        return $this->model->table($this->table)->alias('soc')
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->where($map)
            ->update($data);
    }
}