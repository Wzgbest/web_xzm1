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
        $map = $this->_getMapByFilter($filter,["business_id","sale_status","sale_name","customer_name"]);
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
        $map = $this->_getMapByFilter($filter,["business_id","sale_status","sale_name","customer_name"]);
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
        $map = $this->_getMapByFilter($filter,["business_id","sale_status","sale_name","customer_name"]);
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
        //对应业务
        if(in_array("business_id",$filter_column) && array_key_exists("business_id", $filter)){
            $map["sc.business_id"] = $filter["business_id"];
        }
        //业务状态
        if(in_array("sale_status",$filter_column) && array_key_exists("sale_status", $filter)){
            $map["sc.sale_status"] = $filter["sale_status"];
        }
        //商机名称
        if(in_array("sale_name",$filter_column) && array_key_exists("sale_name", $filter)){
            $map["sc.sale_name"] = ["like","%".$filter["sale_name"]."%"];
        }
        //客户名称
        if(in_array("customer_name",$filter_column) && array_key_exists("customer_name", $filter)){
            $map["c.customer_name"] = ["like","%".$filter["customer_name"]."%"];
        }
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
            "scv.visit_time",
            "scv.create_time as visit_create_time",
            "scv.visit_place",
            "scv.location",
            "scv.partner_notice",
            "scv.add_note",
            "scv.visit_ok",
            "scsi.sign_in_time",
            "scsi.sign_in_place",
            "scsi.sign_in_location",
            "scsi.sign_in_ok",
            "soc.id as order_id",
            "sc.final_money as order_money",
            "soci.pay_money",
            "soc.status as order_status",
            "sob.id as bill_id",
            "sob.status as bill_status",
            "GROUP_CONCAT( distinct co.contract_no)",
            "cs.contract_name as contract_type_name",
        ];
        $subQuery = $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'business scb','scb.id = sc.business_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'sale_chance_visit scv','scv.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'sale_chance_sign_in scsi','scsi.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract_item soci','soci.sale_order_id = soc.id',"LEFT")
            ->join($this->dbprefix.'sale_order_bill sob','sob.contract_id = soci.contract_id',"LEFT")
            ->join($this->dbprefix.'contract co','co.id = soci.contract_id',"LEFT")
            ->join($this->dbprefix.'contract_applied ca','ca.id = co.applied_id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->where('sc.customer_id',$customer_id)
            ->group("sc.id")
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
     * @param $uid int 员工id
     * @param $last_id int 最后一条销售机会id
     * @param $num int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllSaleChancesByLastId($uid,$last_id=null,$num=10){
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
        $map["employee_id"] = $uid;
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

    public function getAllUseBusinessSaleChanceCount($ids){
        if(empty($ids)){
            return 0;
        }
        return $this->model->table($this->table)->alias('sc')
            ->where('sc.business_id',"in",$ids)
            ->where('sc.sale_status',"in",[1,2,3,4])
            ->count();
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
        $field = [
            "sc.*",
            "e.truename as employee_name",
        ];
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->where('sc.id',$id)
            ->field($field)
            ->find();
    }

    /**获取
     * @param $id int 客户商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceOrigin($id)
    {
        $field = [
            "sc.*",
        ];
        return $this->model->table($this->table)->alias('sc')
            ->where('sc.id',$id)
            ->field($field)
            ->find();
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

    /**获取对应客户的商机名称和金额总额
     * @param $customer_ids array 客户ID列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getNAmeAndMoneyByCustomerIds($customer_ids){
        if(empty($customer_ids)){
            return [];
        }
        $map["sc.customer_id"] = ["in",$customer_ids];
        return $this->model->table($this->table)->alias('sc')
            ->where($map)
            ->group("sc.customer_id")
            ->column("group_concat(case when sc.sale_status>0 and sc.sale_status<5 then sc.sale_name end) as sale_names,sum(case when sc.sale_status<1 then 0 when sc.sale_status>4 then 0 else sc.guess_money end) as all_guess_money,sum(case when sc.sale_status=5 then sc.final_money else 0 end) as all_final_money,sum(case when sc.sale_status=5 then sc.payed_money else 0 end) as all_payed_money","customer_id");
    }

    public function getNextStatusIsSignInWait($sale_id,$p=1){
        $map['sc.id'] = $sale_id;
        $map['bfiln.item_id'] = 3;
        $field = [
            "sc.id",
            "sc.sale_name",
        ];
        return $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'business_flow_item_link bfil','bfil.setting_id = sc.business_id and sc.sale_status=bfil.item_id',"LEFT")
            ->join($this->dbprefix.'business_flow_item_link bfiln','bfiln.setting_id = sc.business_id and bfiln.order_num = bfil.order_num+'.$p.' and bfiln.item_id=3',"LEFT")
            ->where($map)
            ->group("sc.id")
            ->field($field)
            ->fetchSql(true)
            ->find();
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



    /**
     * 查询商机数排名
     * @param $start_time int 开始时间
     * @param $end_time int 结束时间
     * @param $uids array 员工id数组
     * @param $standard int 达标数量
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 客户筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getSaleChanceRanking($start_time,$end_time,$uids,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["sc.create_time"][] = ["egt",$start_time];
        $map["sc.create_time"][] = ["elt",$end_time];
        $map["sc.employee_id"] = ["in",$uids];
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $group="sc.employee_id";
        $order="num desc,standard_time asc";
        $rankingList = $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->where($map)
            ->group($group)
            ->order($order)
            //->limit($offset,$num)
            ->field("e.id as employee_id,e.telephone,e.truename,count(sc.id) num,MAX(sc.create_time) as standard_time,IF (count(sc.id) >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($rankingList,'$rankingList',1);
        if($num==1&&$page==0&&$rankingList){
            $rankingList = $rankingList[0];
        }
        return $rankingList;
    }


    /**
     * 查询商机数达标
     * @param $start_time int 开始时间
     * @param $end_time int 结束时间
     * @param $uids array 员工id数组
     * @param $standard int 标准
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 客户筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getSaleChanceStandard($start_time,$end_time,$uids,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["sc.create_time"][] = ["egt",$start_time];
        $map["sc.create_time"][] = ["elt",$end_time];
        $map["sc.employee_id"] = ["in",$uids];
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $order="sc.employee_id asc,sc.id asc";
        $vl_order="standard desc";
        $standard_order="is_standard desc,standard_time asc,num desc";
        $subQuery = $this->model->table($this->table)->alias('sc')
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit(999999)//2147483647?
            ->field("e.id as employee_id,e.telephone,e.truename,sc.create_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $subQuery = $this->model->table($subQuery)->alias('sl')
            ->join("(SELECT @prev := '', @n := 0, @st := 0) init",'sl.employee_id = sl.employee_id',"LEFT")
            ->order($vl_order)
            ->limit(999999)//2147483647?
            ->field("sl.*,@n := IF (sl.employee_id != @prev, '1', @n + 1)+0 AS standard ,@st := IF (sl.employee_id != @prev, '0', @st) AS stre ,@prev := sl.employee_id AS ng ,@st := IF (@n = $standard, sl.create_time, @st) as standard_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $standardList = $this->model->table($subQuery)->alias('v')
            ->group("employee_id")
            ->order($standard_order)
            //->limit($offset,$num)
            ->field("employee_id,telephone,truename,standard as num,standard_time,IF (standard >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($standardList,'$standardList',1);
        if($num==1&&$page==0&&$standardList){
            $standardList = $standardList[0];
        }
        return $standardList;
    }
}