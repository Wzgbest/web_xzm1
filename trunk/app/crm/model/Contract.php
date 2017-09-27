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

class Contract extends Base{
    protected $dbprefix;
    public function __construct($corp_id)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'contract_applied';
        parent::__construct($corp_id);
    }
    /**
     * 查询所有合同
     * @param $user_id int 用户id
     * @param $type int 合同类型
     * @param $status array 状态
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getAllContract($user_id=null,$status=[],$type=null,$order="ca.id",$direction="desc"){
        //筛选
        $map = [];
        if($user_id){
            $map["ca.employee_id"] = $user_id;
        }
        if($type){
            $map["contract_type"] = $type;
        }
        if(!empty($type)){
            $map["ca.status"] = ["in",$status];
        }
        $map["c.status"] = 1;

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $contractList = $this->model->table($this->dbprefix."contract")->alias('c')
            ->join($this->table.' ca','ca.id = c.applied_id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->where($map)
            ->order($order)
            ->field('c.*,ca.contract_type,ca.contract_num,cs.contract_name as contract_type_name')
            ->select();
        return $contractList;
    }
    /**
     * 查询所有合同
     * @param $user_id int 用户id
     * @param $sale_id int 销售机会id
     * @param $status array 状态
     * @param $type int 合同类型
     * @param $in_id array 加入这些id的合同
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getAllContractNoAndType($user_id=null,$sale_id=null,$status=[],$type=null,$in_id=[],$order="ca.id",$direction="desc"){
        //筛选
        $map = [];
        $in_id_map = [];
        if($user_id){
            $map["ca.employee_id"] = $user_id;
        }
        if($sale_id){
            $map["soc.sale_id"] = $sale_id;
        }else{
            $map["soc.sale_id"] = ["exp","is null"];
        }
        if(!empty($status)){
            $map["ca.status"] = ["in",$status];
        }
        if($type){
            $map["ca.contract_type"] = $type;
        }
        if(!empty($in_id)){
            $in_id_map["c.id"] = ["in",$in_id];
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $contractList = $this->model->table($this->dbprefix."contract")->alias('c')
            ->join($this->table.' ca','ca.id = c.applied_id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->join($this->dbprefix.'sale_order_contract_item soci','soci.contract_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.id = soci.sale_order_id',"LEFT")
            ->where($map)
            ->whereOr($in_id_map)
            ->order($order)
            ->column("c.contract_no,ca.contract_type,cs.contract_name as contract_type_name","c.id");
        return $contractList;
    }
    /**
     * 查询合同申请
     * @param $uid int 员工id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 合同筛选条件
     * @param $field array 合同列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getContractApplied($uid,$num=10,$page=0,$filter=null,$field=null,$order="ca.id",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,["contract_type","business_id","contract_no","customer_name"]);
        $map["ca.employee_id"] = $uid;
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
            'ca.*',
            'GROUP_CONCAT(distinct co.contract_no) as contract_no',
            'co.status as contract_status',
            'cs.contract_name as contract_type_name',
            "sc.sale_name",
            "sc.sale_status",
            'soc.status as order_status',
            "c.customer_name",
            "bfs.business_flow_name",
            "(case when ca.status = 0 then 1 
            when ca.status = 2 then 3 
            when ca.status = 3 then 4 
            when ca.status = 1 and co.status = 6 then 5 
            when ca.status = 1 and sc.sale_status = 4 and soc.status = 0 then 6 
            when ca.status = 1 and sc.sale_status = 5 and soc.status = 1 then 7 
            when ca.status = 1 and (co.status = 1 or co.status = 4 or co.status = 5 or co.status = 7 or co.status = 8) then 2 
            else 8 end ) as in_column",
        ];

        $contractAppliedList = $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract co','co.applied_id = ca.id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.contract_id = co.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','c.id = sc.customer_id',"LEFT")
            ->join($this->dbprefix.'business_flow_setting bfs','bfs.id = sc.business_id',"LEFT")
            ->where($map)
            ->group("ca.id,co.group_field")
            ->order($order)
            ->having($having)
            ->limit($offset,$num)
            ->field($field)
            ->select();
        //var_exp($contractAppliedList,'$contractAppliedList',1);
        if($num==1&&$page==0&&$contractAppliedList){
            $contractAppliedList = $contractAppliedList[0];
        }
        return $contractAppliedList;
    }
    /**
     * @param $uid int 员工id
     * 查询合同数量
     * @param $filter array 合同筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getContractAppliedCount($uid,$filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,["contract_type","business_id","contract_no","customer_name"]);
        $map["ca.employee_id"] = $uid;
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

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

        $contractAppliedCount= $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract co','co.applied_id = ca.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.contract_id = co.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','c.id = sc.customer_id',"LEFT")
            ->where($map)
            ->field($field)
            ->group("ca.id,co.group_field")
            ->having($having)
            ->count();
        return $contractAppliedCount;
    }

    /**
     * 查询列上的数量
     * @param $uid int 员工id
     * @param $filter array 过滤条件
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function getColumnNum($uid,$filter=null){

        //筛选
        $map = $this->_getMapByFilter($filter,["contract_type","business_id","contract_no","customer_name"]);
        $map["ca.employee_id"] = $uid;

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
        if($listCount["0"]==0){
            foreach ($listCount as &$count){
                $count = 0;
            }
        }
        return $listCount;
    }
    /**
     * 查询合同申请
     * @param $uid int 员工id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 合同筛选条件
     * @param $field array 合同列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getVerificationContractApplied($uid,$num=10,$page=0,$filter=null,$field=null,$order="ca.id",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,["order_status","structure","contract_type","business_id","contract_no","apply_employee","customer_name"]);
        $map["ca.status"] = ["neq",3];
        //$map["ca.contract_apply_now"] = $uid;
        $mapStr = "find_in_set('".$uid."',ca.contract_apply_now)";
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
            'ca.*',
            'GROUP_CONCAT(distinct co.contract_no) as contract_no',
            'co.id as contract_id',
            'co.status as contract_status',
            'cs.contract_name as contract_type_name',
            "sc.sale_name",
            "sc.sale_status",
            'soc.status as order_status',
            "c.customer_name",
            "bfs.business_flow_name",
            "e.truename as employee_name",
            "GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name`",
            "(case when ca.status = 0 then 1 
            when ca.status = 1 and co.status = 4 then 2 
            when ca.status = 1 and sc.sale_status = 4 and soc.status = 0 then 4 
            when ca.status = 1 and sc.sale_status = 5 and soc.status = 1 then 5 
            when ca.status = 2 then 6 
            when sc.sale_status = 6 then 7 
            when sc.sale_status = 9 then 8 
            else 3 end ) as in_column",
        ];

        $contractAppliedList = $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract co','co.applied_id = ca.id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->join($this->dbprefix.'sale_order_contract_item soci','soci.contract_id = co.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soci.sale_order_id = soc.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','c.id = sc.customer_id',"LEFT")
            ->join($this->dbprefix.'employee e','ca.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure_employee ses','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->join($this->dbprefix.'business_flow_setting bfs','bfs.id = sc.business_id',"LEFT")
            ->where($map)
            ->where($mapStr)
            ->group("ca.id,co.group_field")
            ->order($order)
            ->having($having)
            ->limit($offset,$num)
            ->field($field)
            ->select();
        //var_exp($contractAppliedList,'$contractAppliedList',1);
        if($num==1&&$page==0&&$contractAppliedList){
            $contractAppliedList = $contractAppliedList[0];
        }
        return $contractAppliedList;
    }
    /**
     * 查询合同数量
     * @param $uid int 员工id
     * @param $filter array 合同筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getVerificationContractAppliedCount($uid,$filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,["order_status","structure","contract_type","business_id","contract_no","apply_employee","customer_name"]);
        $map["ca.status"] = ["neq",3];
        //$map["ca.contract_apply_now"] = $uid;
        $mapStr = "find_in_set('".$uid."',ca.contract_apply_now)";
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

        $field = [
            "(case when ca.status = 0 then 1 
            when ca.status = 1 and co.status = 4 then 2 
            when ca.status = 1 and sc.sale_status = 4 and soc.status = 0 then 4 
            when ca.status = 1 and sc.sale_status = 5 and soc.status = 1 then 5 
            when ca.status = 2 then 6 
            when sc.sale_status = 6 then 7 
            when sc.sale_status = 9 then 8 
            else 3 end ) as in_column",
        ];

        $contractAppliedCount= $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract co','co.applied_id = ca.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract_item soci','soci.contract_id = co.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.id = soci.sale_order_id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','c.id = sc.customer_id',"LEFT")
            ->join($this->dbprefix.'employee e','ca.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure_employee ses','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->join($this->dbprefix.'business_flow_setting bfs','bfs.id = sc.business_id',"LEFT")
            ->where($map)
            ->where($mapStr)
            ->field($field)
            ->group("ca.id,co.group_field")
            ->having($having)
            ->count();
        return $contractAppliedCount;
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
        $map = $this->_getMapByFilter($filter,["order_status","structure","contract_type","business_id","contract_no","apply_employee","customer_name"]);
        $map["ca.status"] = ["neq",3];
        //$map["ca.contract_apply_now"] = $uid;
        $mapStr = "find_in_set('".$uid."',ca.contract_apply_now)";

        $field = [
            "(case when ca.status = 0 then 1 
            when ca.status = 1 and co.status = 4 then 2 
            when ca.status = 1 and sc.sale_status = 4 and soc.status = 0 then 4 
            when ca.status = 1 and sc.sale_status = 5 and soc.status = 1 then 5 
            when ca.status = 2 then 6 
            when sc.sale_status = 6 then 7 
            when sc.sale_status = 9 then 8 
            else 3 end ) as in_column",
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
        ];

        $customerQuery = $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract co','co.applied_id = ca.id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->join($this->dbprefix.'sale_order_contract_item soci','soci.contract_id = co.id',"LEFT")
            ->join($this->dbprefix.'sale_order_contract soc','soc.id = soci.sale_order_id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.id = soc.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','c.id = sc.customer_id',"LEFT")
            ->join($this->dbprefix.'employee e','ca.employee_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure_employee ses','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->join($this->dbprefix.'business_flow_setting bfs','bfs.id = sc.business_id',"LEFT")
            ->where($map)
            ->where($mapStr)
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
        //订单状态
        if(in_array("order_status",$filter_column) && array_key_exists("order_status", $filter)){
            $map["soc.status"] = $filter["order_status"];
        }
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
        //合同编码
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

    public function addContract($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function addAllContract($datas){
        $field = [
            'employee_id',
            'contract_type',
            'contract_num',
            'contract_apply_1',
            'contract_apply_2',
            'contract_apply_3',
            'contract_apply_4',
            'contract_apply_5',
            'contract_apply_6',
            'contract_apply_status',
            'contract_apply_now',
            'update_time',
            'create_time',
            'status',
        ];
        return $this->model->table($this->table)->field($field)->insertAll($datas);
    }

    public function createContractNo($data){
        return $this->model->table($this->dbprefix."contract")->insertGetId($data);
    }

    public function createContractNos($datas){
        $field = [
            'applied_id',
            'contract_no',
            'update_time',
            'create_time',
            'status',
        ];
        return $this->model->table($this->dbprefix."contract")->field($field)->insertAll($datas);
    }

    public function updateContractNos($id){
        $data["c.group_field"] = ['exp',"contract_no"];
        return $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract c','c.applied_id = ca.id',"LEFT")
            ->where('ca.id',$id)
            ->update($data);
    }

    public function getContract($id){
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    public function getContractNoInfo($id){
        return $this->model->table($this->dbprefix."contract")->where('id',$id)->find();
    }

    public function setContract($id,$data,$map=null){
        return $this->model->table($this->table)->where('id',$id)->where($map)->update($data);
    }

    //撤回
    public function retract($id,$user_id=null){
        $data["status"] = 3;
        if($user_id){
            $map["employee_id"] = $user_id;
        }
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    //驳回
    public function rejected($id){
        $data["status"] = 2;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    //作废
    public function invalid($id){
        $data["status"] = 6;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }

    //已领取
    public function received($id){
        $data["status"] = 5;
        $map["id"] = $id;
        $map["status"] = 4;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }

    //提醒
    public function remind($id){
        $data["status"] = 8;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }

    //已退款
    public function refunded($id){
        $data["status"] = 9;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }

    //收回
    public function withdrawal($id){
        $data["status"] = 7;
        $map["id"] = $id;
        $map["status"] = 5;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }
    //上次合同申请的申请人
    public function getLastApply($con){
        $subQuery=$this->model
            ->table($this->table)
            ->where($con)
            ->field("contract_type,contract_apply_1,contract_apply_2,contract_apply_3,contract_apply_4,contract_apply_5,contract_apply_6")
            ->order("update_time","DESC")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $lastAppliedList = $this->model
            ->table($subQuery." l")
            ->group("contract_type")
            ->select();
        return $lastAppliedList;
        //return $this->model->table($this->table)->where($con)->field("contract_type,contract_apply_1,contract_apply_2,contract_apply_3,contract_apply_4,contract_apply_5,contract_apply_6")->order("update_time","DESC")->group("contract_type")->select();
    }
}