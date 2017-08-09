<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;
use app\systemsetting\model\CustomerSetting;

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
     * @param $uid int 用户id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 合同筛选条件
     * @param $field array 合同列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleChancesByPage($uid,$num=10,$page=0,$filter=null,$field=null,$order="ca.id",$direction="desc"){
        //获取客户配置
        $struct_ids = getStructureIds($uid);
        $customerSettingModel = new CustomerSetting();
        $searchCustomerList = $customerSettingModel->getCustomerSettingByStructIds($struct_ids);
        //$protect_customer_days = [];
        $protect_customer_day_max = 0;//暂定员工属于两个部门且有客户,保有时间按长的来
        $to_halt_day_max = 0;//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
        foreach ($searchCustomerList as $customerSetting){
            if(!$customerSetting){
                $customerSetting["protect_customer_day"] = 0;
                $customerSetting["to_halt_day"] = 0;
            }
            //$protect_customer_days[$structure] = $customerSetting["protect_customer_day"];
            if($customerSetting["protect_customer_day"]>$protect_customer_day_max){//暂定员工属于两个部门且有客户,保有时间按长的来
                $protect_customer_day_max = $customerSetting["protect_customer_day"];
            }
            if($customerSetting["to_halt_day"]>$to_halt_day_max){//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
                $to_halt_day_max = $customerSetting["to_halt_day"];
            }
        }

        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,[]);
        $map["sc.employee_id"] = $uid;
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $field = [
            "sc.*",
            "c.customer_name",
            "(case when sc.sale_status = 7 then 5 
            when sc.sale_status = 6 then 4 
            when sc.sale_status = 5 and soc.status = 1 then 3 
            when sc.sale_status = 9 then 2 
            when FLOOR((unix_timestamp()-ct.create_time)/60/60/24) >".$to_halt_day_max." then 2 
            else 1 end ) as in_column",
        ];
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'sale_order_contract soc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")
            ->where($map)
            ->limit($offset,$num)
            ->field($field)
            ->group("sc.id")
            ->order($order)
            ->having($having)
            ->select();
    }

    /**
     * @param $uid int 用户id
     * @param $filter array 合同筛选条件
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleChanceCount($uid,$filter=null){
        //获取客户配置
        $struct_ids = getStructureIds($uid);
        $customerSettingModel = new CustomerSetting();
        $searchCustomerList = $customerSettingModel->getCustomerSettingByStructIds($struct_ids);
        //$protect_customer_days = [];
        $protect_customer_day_max = 0;//暂定员工属于两个部门且有客户,保有时间按长的来
        $to_halt_day_max = 0;//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
        foreach ($searchCustomerList as $customerSetting){
            if(!$customerSetting){
                $customerSetting["protect_customer_day"] = 0;
                $customerSetting["to_halt_day"] = 0;
            }
            //$protect_customer_days[$structure] = $customerSetting["protect_customer_day"];
            if($customerSetting["protect_customer_day"]>$protect_customer_day_max){//暂定员工属于两个部门且有客户,保有时间按长的来
                $protect_customer_day_max = $customerSetting["protect_customer_day"];
            }
            if($customerSetting["to_halt_day"]>$to_halt_day_max){//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
                $to_halt_day_max = $customerSetting["to_halt_day"];
            }
        }

        //筛选
        $map = $this->_getMapByFilter($filter,[]);
        $map["sc.employee_id"] = $uid;
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

        $field = [
            "(case when sc.sale_status = 7 then 5 
            when sc.sale_status = 6 then 4 
            when sc.sale_status = 5 and soc.status = 1 then 3 
            when sc.sale_status = 9 then 2 
            when FLOOR((unix_timestamp()-ct.create_time)/60/60/24) >".$to_halt_day_max." then 2 
            else 1 end ) as in_column",
        ];
        
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'sale_order_contract soc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")
            ->where($map)
            ->field($field)
            ->group("sc.id")
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
    public function getAllColumnNum($uid,$filter=null){
        //获取客户配置
        $struct_ids = getStructureIds($uid);
        $customerSettingModel = new CustomerSetting();
        $searchCustomerList = $customerSettingModel->getCustomerSettingByStructIds($struct_ids);
        //$protect_customer_days = [];
        $protect_customer_day_max = 0;//暂定员工属于两个部门且有客户,保有时间按长的来
        $to_halt_day_max = 0;//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
        foreach ($searchCustomerList as $customerSetting){
            if(!$customerSetting){
                $customerSetting["protect_customer_day"] = 0;
                $customerSetting["to_halt_day"] = 0;
            }
            //$protect_customer_days[$structure] = $customerSetting["protect_customer_day"];
            if($customerSetting["protect_customer_day"]>$protect_customer_day_max){//暂定员工属于两个部门且有客户,保有时间按长的来
                $protect_customer_day_max = $customerSetting["protect_customer_day"];
            }
            if($customerSetting["to_halt_day"]>$to_halt_day_max){//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
                $to_halt_day_max = $customerSetting["to_halt_day"];
            }
        }

        //筛选
        $map = $this->_getMapByFilter($filter,[]);
        $map["sc.employee_id"] = $uid;

        $field = [
            "(case when sc.sale_status = 7 then 5 
            when sc.sale_status = 6 then 4 
            when sc.sale_status = 5 and soc.status = 1 then 3 
            when sc.sale_status = 9 then 2 
            when FLOOR((unix_timestamp()-ct.create_time)/60/60/24) >".$to_halt_day_max." then 2 
            else 1 end ) as in_column",
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

        $customerQuery = $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'sale_order_contract soc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")
            ->where($map)
            ->group("sc.id")
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
        if($listCount["0"]==0){
            foreach ($listCount as &$count){
                $count = 0;
            }
        }
        return $listCount;
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
            ->limit("999999")
            ->buildSql();
        return $this->model->table($subQuery)->alias('v')
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