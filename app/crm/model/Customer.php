<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;
use app\systemsetting\model\CustomerSetting;
//use phpDocumentor\Reflection\Types\This;

class Customer extends Base
{
    protected $dbprefix;
    public function __construct($corp_id = null)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'customer';
        parent::__construct($corp_id);
    }

    /**
     * 获取管理的客户
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @param $direction string 排序方向
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getManageCustomer($num=10,$page=0,$filter=null,$field=null,$order="id",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,["grade","resource_from","comm_status","take_type","tracer","guardian","add_man"]);
        $map['belongs_to'] = ["gt",0];

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $orderPrefix = "";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        $listOrder = [$order=>$direction];//聚合后排序
        switch ($order){
            case "id":
            case "customer_name":
            case "grade":
            case "is_public":
            case "add_time":
                $orderPrefix = "c.";
                $subOrder = [$orderPrefix.$order=>$direction];
                $listOrder = [$order=>$direction];
                break;
        }
        $subOrder["cn.id"] = "desc";//沟通状态

        //固定显示字段
        $subField = [
            "c.id",
            "c.customer_name",
            "c.grade",
            "c.belongs_to",
            "c.resource_from",
            "c.is_public",
            "c.public_to_employee",
            "c.public_to_department",
            "te.truename as tracer",
            "'' as guardian",
            "ae.truename as add_man",
            "c.add_time",
            "c.take_type",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
        ];
        $listField = [
            "id",
            "customer_name",
            "grade",
            "belongs_to",
            "resource_from",
            "is_public",
            "public_to_employee",
            "public_to_department",
            "tracer",
            "guardian",
            "add_man",
            "add_time",
            "take_type",
            "tend_to",
            "phone_correct",
            "profile_correct",
            "call_through",
            "is_wait",
        ];

        $subQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'employee ae','c.add_man = ae.id',"LEFT")
            ->join($this->dbprefix.'employee te','c.handle_man = te.id',"LEFT")
            ->where($map)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $customerList = $this->model
            ->table($subQuery." l")
            ->group("id")
            ->order($listOrder)
            ->limit($offset,$num)
            ->field($listField)
            ->select();
        //var_exp($customerList,'$customerList',1);
        foreach ($customerList as &$customer) {
            $customer['comm_status'] = getCommStatusByArr([
                "tend_to" => $customer['tend_to'],
                "phone_correct" => $customer['phone_correct'],
                "profile_correct" => $customer['profile_correct'],
                "call_through" => $customer['call_through'],
                "is_wait" => $customer['is_wait'],
            ]);
        }
        return $customerList;
    }
    /**
     * 获取管理的客户数量
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @param $direction string 排序方向
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getManageCustomerCount($filter=null,$order="id",$direction="desc"){

        //筛选
        $map = $this->_getMapByFilter($filter,["grade","resource_from","comm_status","take_type","tracer","guardian","add_man"]);
        //$map['belongs_to'] = 1;
        //var_exp($map,'$map',1);

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $orderPrefix = "";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        switch ($order){
            case "id":
            case "customer_name":
            case "grade":
            case "is_public":
            case "add_time":
                $orderPrefix = "c.";
                $subOrder = [$orderPrefix.$order=>$direction];
                break;
        }
        $subOrder["cn.id"] = "desc";//沟通状态

        //固定显示字段
        $subField = [
            "c.id",
            "c.customer_name",
            "c.grade",
            "c.belongs_to",
            "c.resource_from",
            "c.is_public",
            "c.public_to_employee",
            "c.public_to_department",
            "te.truename as tracer",
            "'' as guardian",
            "ae.truename as add_man",
            "c.add_time",
            "c.take_type",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
        ];

        $subQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'employee ae','c.add_man = ae.id',"LEFT")
            ->join($this->dbprefix.'employee te','c.handle_man = te.id',"LEFT")
            ->where($map)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $customerCount = $this->model
            ->table($subQuery." l")
            ->group("id")
            ->count();
        //var_exp($customerCount,'$customerCount',1);
        return $customerCount;
    }

    /**
     * 获取公开公海池中的客户
     * @param $uid int 员工id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getPublicPoolCustomer($uid,$num=10,$page=0,$filter=null,$field=null,$order="id",$direction="desc"){
        //部门
        $struct_ids = getStructureIds($uid);
        //$struct_ids_str = array_column($struct_ids, 'struct_id');
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,["resource_from","grade","customer_name"]);
        $map['belongs_to'] = 2;
        $map_str = " is_public = 1 or find_in_set($uid,public_to_employee) ";
        foreach ($struct_ids as $struct_id){
            $map_str .=" or find_in_set(".$struct_id["struct_id"].",public_to_department) ";
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $orderPrefix = "";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        $listOrder = [$order=>$direction];//聚合后排序
        switch ($order){
            case "id":
            case "customer_name":
            case "resource_from":
            case "grade":
                $orderPrefix = "c.";
                $subOrder = [$orderPrefix.$order=>$direction];
                $listOrder = [$order=>$direction];
                break;
        }
        $subOrder["cc.id"] = "desc";//联系人

        //固定显示字段
        $subField = [
            "c.id",
            "c.customer_name",
            "c.resource_from",
            "c.grade",
            "ae.truename as add_man",
            "cc.contact_name",
            "IFNULL(cc.phone_first,c.telephone) as phone_first",
        ];
        $listField = [
            "id",
            "customer_name",
            "resource_from",
            "grade",
            "add_man",
            "contact_name",
            "phone_first",
        ];

        $subQuery = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'employee ae','c.add_man = ae.id',"LEFT")
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->where($map)
            //->where($map_str)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $customerList = $this->model
            ->table($subQuery." l")
            ->group("id")
            ->order($listOrder)
            ->limit($offset,$num)
            ->field($listField)
            ->select();
        //var_exp($customerList,'$customerList',1);
        return $customerList;
    }

    /**
     * 获取公海池中的客户
     * @param $uid int 员工id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getPoolCustomer($uid,$num=10,$page=0,$filter=null,$field=null,$order="id",$direction="desc"){
        //部门
        $struct_ids = getStructureIds($uid);
        //$struct_ids_str = array_column($struct_ids, 'struct_id');

        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,["resource_from","is_public","customer_name"]);
        $map['belongs_to'] = 2;
        $map_str = " is_public = 1 or find_in_set($uid,public_to_employee) ";
        foreach ($struct_ids as $struct_id){
            $map_str .=" or find_in_set(".$struct_id["struct_id"].",public_to_department) ";
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $Order = [$order=>$direction];
        switch ($order){
            case "id":
            case "customer_name":
            case "resource_from":
            case "add_batch":
                $Order = [$order=>$direction];
                break;
        }

        //固定显示字段
        $Field = [
            "c.id",
            "c.customer_name",
            "c.resource_from",
            "c.add_batch",
            "ae.truename as add_man",
            "c.is_public",
            "c.add_time",
        ];

        $customerList = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'employee ae','c.add_man = ae.id',"LEFT")
            ->where($map)
            ->where($map_str)
            ->order($Order)
            ->field($Field)
            ->limit($offset,$num)
            ->select();
        //var_exp($customerList,'$customerList',1);
        foreach ($customerList as &$customer){
            $customer["customer_name"] = mb_substr($customer["customer_name"],0,3)."XXXXXXXX";
        }
        return $customerList;
    }
    /**
     * 获取公开公海池中的客户数量
     * @param $uid int 员工id
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @param $direction string 排序方向
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getPublicPoolCustomerCount($uid,$filter=null,$order="id",$direction="desc"){
        //部门
        $struct_ids = getStructureIds($uid);
        //$struct_ids_str = array_column($struct_ids, 'struct_id');

        //筛选
        $map = $this->_getMapByFilter($filter,["resource_from","grade","customer_name"]);
        $map['belongs_to'] = 2;
        $map_str = " is_public = 1 or find_in_set($uid,public_to_employee) ";
        foreach ($struct_ids as $struct_id){
            $map_str .=" or find_in_set(".$struct_id["struct_id"].",public_to_department) ";
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $orderPrefix = "";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        switch ($order){
            case "id":
            case "customer_name":
            case "resource_from":
            case "grade":
                $orderPrefix = "c.";
                $subOrder = [$orderPrefix.$order=>$direction];
                $listOrder = [$order=>$direction];
                break;
        }
        $subOrder["cc.id"] = "desc";//联系人

        //固定显示字段
        $subField = [
            "c.id",
            "c.customer_name",
            "c.resource_from",
            "c.grade",
            "c.add_man",
            "cc.contact_name",
            "cc.phone_first",
        ];

        $subQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->where($map)
            //->where($map_str)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $customerCount = $this->model
            ->table($subQuery." l")
            ->group("id")
            ->count();
        return $customerCount;
    }

    /**
     * 获取公海池中的客户数量
     * @param $uid int 员工id
     * @param $filter array 客户筛选条件
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getPoolCustomerCount($uid,$filter=null){
        //部门
        $struct_ids = getStructureIds($uid);
        //$struct_ids_str = array_column($struct_ids, 'struct_id');

        //筛选
        $map = $this->_getMapByFilter($filter,["resource_from","is_public","customer_name"]);
        $map['belongs_to'] = 2;
        $map_str = " is_public = 1 or find_in_set($uid,public_to_employee) ";
        foreach ($struct_ids as $struct_id){
            $map_str .=" or find_in_set(".$struct_id["struct_id"].",public_to_department) ";
        }

        $customerCount = $this->model
            ->table($this->table)
            ->where($map)
            ->where($map_str)
            ->count();
        return $customerCount;
    }

    /**
     * 获取我的客户
     * @param $uid int 员工id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $field array 字段筛选
     * @param $order string 排序
     * @param $direction string 排序方向
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getSelfCustomer($uid,$num=10,$page=0,$filter=null,$field=null,$order="id",$direction="desc"){
        $now_time = time();
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
        $all_flg = 0;
        if($num==0&&$page==0){
            $all_flg = 1;
        }else{
            if($page){
                $offset = ($page-1)*$num;
            }
        }

        //筛选
        $map = $this->_getMapByFilter($filter,["take_type","grade","customer_name","contact_name","comm_status","sale_chance"]);
        $map['c.belongs_to'] = 3;
        $map['c.handle_man'] = $uid;
        $having = "";
        //列筛选
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }else{
                $having = null;
            }
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $orderPrefix = "";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        $listOrder = [$order=>$direction];//聚合后排序
        switch ($order){
            case "id":
            case "customer_name":
            case "grade":
            case "take_time":
                $orderPrefix = "c.";
                $subOrder = [$orderPrefix.$order=>$direction];
                $listOrder = [$order=>$direction];
                break;
            case "contact_name":
                $orderPrefix = "cc.";
                $subOrder = [$orderPrefix."id"=>"desc"];
                $listOrder = [$order=>$direction];
                break;
            case "comm_status":
                $orderPrefix = "cn.";
                $subOrder = [
                    $orderPrefix."tend_to"=>$direction,
                    $orderPrefix."phone_correct"=>$direction,
                    $orderPrefix."profile_correct"=>$direction,
                    $orderPrefix."call_through"=>$direction,
                    $orderPrefix."is_wait"=>$direction,
                ];
                $listOrder = [
                    "tend_to"=>$direction,
                    "phone_correct"=>$direction,
                    "profile_correct"=>$direction,
                    "call_through"=>$direction,
                    "is_wait"=>$direction,
                ];
                break;
            case "remind_time":
                $orderPrefix = "cn.";
                $order = "wait_alarm_time";
                $subOrder = [
                    $orderPrefix."is_wait"=>"desc",
                    $orderPrefix.$order=>$direction,
                ];
                $listOrder = [
                    "is_wait"=>"desc",
                    $order=>$direction,
                ];
                break;
            case "last_trace_time":
                $orderPrefix = "ct.";
                $order = "create_time";
                $subOrder = [$orderPrefix.$order=>"desc"];
                $listOrder = [$order=>$direction];
                break;
            case "guess_money":
                //$orderPrefix = "sc.";
                //$idsOrder = [$orderPrefix.$order=>$direction];
                $listOrder = [$order=>"all_guess_money"];
                break;
        }
        $subOrder["sc.id"] = "desc";//商机
        $subOrder["cn.id"] = "desc";//沟通状态
        $subOrder["cc.id"] = "desc";//联系人
        //$subOrder["cr.id"] = "desc";//电话
        //$subOrder["ct.id"] = "desc";//客户跟踪

        //固定显示字段
        $subField = [
            "c.id",
            "c.customer_name",
            "c.take_type",
            "c.grade",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
            //"'沟通状态' as comm_status",
            //"'' as sale_name",
            //"0 as in_progress_guess_money",//all_guess_money
            //"0 as win_final_money",//all_final_money
            //"0 as win_payed_money",//all_payed_money
            "cc.contact_name",
            "IFNULL(cc.phone_first,c.telephone) as phone_first",
            "c.take_time",//领取时间
            "'' as contract_due_time",
            "cn.wait_alarm_time as remind_time",
            //"'所在列' as in_column",
            "sc.id as sale_id",
            "sc.sale_status",
        ];
        $listField = [
            "l.id",
            "customer_name",
            "take_type",
            "grade",
            "tend_to",
            "phone_correct",
            "profile_correct",
            "call_through",
            "is_wait",
            //"comm_status",
            "'' as sale_names",
            "0 as all_guess_money",
            "0 as all_final_money",
            "0 as all_payed_money",
            "contact_name",
            "phone_first",
            "take_time",
            "contract_due_time",
            "remind_time",
            "(case when phone_correct = 0 and profile_correct = 0 then 8 
            when tend_to = 0 then 6 
            when is_wait = 0 then 5 
            when sale_status = 0 then 7 
            when ct.id = '' or ct.id is null then 2 
            when FLOOR((unix_timestamp()-ct.create_time)/60/60/24) >".$to_halt_day_max." then 4 
            when FLOOR((unix_timestamp()-ct.create_time)/60/60/24) >3 then 1 else 3 end ) as in_column",
            "sale_status",
            "ct.id ct_id",
            "ct.create_time as last_trace_time",
            "cr.begin_time as last_call_time",
        ];
        /*
        //动态显示字段:获取途径
        if(in_array("take_type", $field)){
            $subField[] = "c.take_type";
            $listField[] = "take_type";
        }
        //动态显示字段:客户级别
        if(in_array("grade", $field)){
            $subField[] = "c.grade";
            $listField[] = "grade";
        }
        */

        $subQuery = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")//sc.employee_id = c.handle_man
            //->join($this->dbprefix.'contract_applied ca','ca.sale_id = sc.id',"LEFT")
            ->where($map)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $customerQuery = $this->model
            ->table($subQuery." l")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = l.id',"LEFT")//ct.operator_id = c.handle_man
            ->join($this->dbprefix.'call_record cr','cr.customer_id = l.id',"LEFT")
            ->group("id")
            ->order($listOrder)
            ->having($having);
        if(!$all_flg){
            $customerQuery = $customerQuery
                ->limit($offset,$num);
        }
        $customerList = $customerQuery
            ->field($listField)
            ->select();
        //var_exp($customerList,'$customerList',1);
        //var_exp($this->model->getLastSql(),'$customerListSql');
        //具体的值处理
        foreach ($customerList as &$customer){
            $customer['comm_status'] = getCommStatusByArr([
                "tend_to"=>$customer['tend_to'],
                "phone_correct"=>$customer['phone_correct'],
                "profile_correct"=>$customer['profile_correct'],
                "call_through"=>$customer['call_through'],
                "is_wait"=> $customer['is_wait'],
            ]);
            if($protect_customer_day_max){
                $customer['save_time_str'] = time_diff_day_time($protect_customer_day_max*24*60*60-$customer['take_time'],$now_time);
            }else{
                $customer['save_time_str'] = "无";
            }
            if($customer['remind_time']){
                $customer['remind_time_str'] = time_diff_day_time($customer['remind_time'],$now_time);
            }else{
                $customer['remind_time_str'] = "无";
            }
            if($customer['contract_due_time']){
                $customer['contract_due_time_str'] = time_diff_day_time($customer['contract_due_time'],$now_time);
            }else{
                $customer['contract_due_time_str'] = "无";
            }
        }
        return $customerList;
    }

    /**
     * 获取我的客户数量
     * @param $uid int 员工id
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @param $direction string 排序方向
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getSelfCustomerCount($uid,$filter=null,$order="id",$direction="desc"){
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
        $map = $this->_getMapByFilter($filter,["take_type","grade","customer_name","contact_name","comm_status","sale_chance"]);
        $map['c.belongs_to'] = 3;
        $map['c.handle_man'] = $uid;
        $having = "";
        //列筛选
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }else{
                $having = null;
            }
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $orderPrefix = "";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        switch ($order){
            case "id":
            case "customer_name":
            case "grade":
            case "take_time":
                $orderPrefix = "c.";
                $subOrder = [$orderPrefix.$order=>$direction];
                break;
            case "contact_name":
                $orderPrefix = "cc.";
                $subOrder = [$orderPrefix."id"=>"desc"];
                break;
            case "comm_status":
                $orderPrefix = "cn.";
                $subOrder = [
                    $orderPrefix."tend_to"=>$direction,
                    $orderPrefix."phone_correct"=>$direction,
                    $orderPrefix."profile_correct"=>$direction,
                    $orderPrefix."call_through"=>$direction,
                    $orderPrefix."is_wait"=>$direction,
                ];
                break;
            case "remind_time":
                $orderPrefix = "cn.";
                $order = "wait_alarm_time";
                $subOrder = [
                    $orderPrefix."is_wait"=>"desc",
                    $orderPrefix.$order=>$direction,
                ];
                break;
            case "last_trace_time":
                $orderPrefix = "ct.";
                $order = "create_time";
                $subOrder = [$orderPrefix.$order=>"desc"];
                break;
            case "guess_money":
                break;
        }
        $subOrder["sc.id"] = "desc";//商机
        $subOrder["cn.id"] = "desc";//沟通状态
        $subOrder["ct.id"] = "desc";//客户跟踪
        $subOrder["cc.id"] = "desc";//联系人

        //固定显示字段
        $subField = [
            "c.id",
            "c.customer_name",
            "c.take_type",
            "c.grade",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
            //"'沟通状态' as comm_status",
            "sc.sale_name",
            "(case when sc.sale_status<1 then 0 when sc.sale_status>4 then 0 else sc.guess_money end) as in_progress_guess_money",//all_guess_money
            "(case when sc.sale_status=5 then sc.final_money else 0 end) as win_final_money",//all_final_money
            "cc.contact_name",
            "cc.phone_first",
            "ct.create_time as last_trace_time",
            "c.take_time",//领取时间
            "'' as contract_due_time",
            "cn.wait_alarm_time as remind_time",
            //"'所在列' as in_column",
            "sc.sale_status",
            "sc.id as sale_id",
            "ct.id ct_id",
        ];
        $listField = [
            "id",
            "customer_name",
            "take_type",
            "grade",
            "tend_to",
            "phone_correct",
            "profile_correct",
            "call_through",
            "is_wait",
            //"comm_status",
            "group_concat(sale_name ORDER BY l.sale_id DESC) as sale_names",
            "SUM(in_progress_guess_money) as all_guess_money",
            "SUM(win_final_money) as all_final_money",
            "contact_name",
            "phone_first",
            "last_trace_time",
            "take_time",
            "contract_due_time",
            "remind_time",
            "(case when phone_correct = 0 and profile_correct = 0 then 8 when tend_to = 0 then 6 when is_wait = 0 then 5 when sale_status = 0 then 7 when ct_id = '' or ct_id is null then 2 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >".$to_halt_day_max." then 4 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >3 then 1 else 3 end ) as in_column",
            "sale_status",
            "ct_id",
        ];

        $subQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")//sc.employee_id = c.handle_man
            //->join($this->dbprefix.'contract_applied ca','ca.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")//ct.operator_id = c.handle_man
            ->where($map)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $customerCount = $this->model
            ->table($subQuery." l")
            ->group("id")
            ->having($having)
            ->field($listField)
            ->count();
        return $customerCount;
    }

    /**
     * 获取我的下属的客户
     * @param $uid int 员工id
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getSubordinateCustomer($uid,$num=10,$page=0,$filter=null,$field=null,$order="id",$direction="desc"){
        $now_time = time();
        //获取客户配置
        $struct_ids = getStructureIds($uid);
        //$protect_customer_days = [];
        $protect_customer_day_max = 0;//暂定员工属于两个部门且有客户,保有时间按长的来
        $to_halt_day_max = 0;//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
        foreach ($struct_ids as $struct_id){
            $structure = intval($struct_id);
            $setting_map = "find_in_set('$structure', set_to_structure)";
            $customerSettingModel = new CustomerSetting();
            $customerSetting = $customerSettingModel->getCustomerSetting(1,0,$setting_map);
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
        $map = $this->_getMapByFilter($filter,["take_type","grade","sale_chance","belongs_to","comm_status","customer_name","tracer","contact_name"]);
        $map['c.belongs_to'] = 3;
        $subordinateIdSubMap["e.id"] = $uid;
        $subordinateIdSubMap["e.is_leader"] = 1;
        $subordinateIdSubMap["ses.user_id"] = ["neq",$uid];
        $subordinateIdSubQuery = $this->model
            ->table($this->dbprefix.'employee')->alias('e')
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id',"LEFT")
            ->join($this->dbprefix.'structure_employee ses','ses.struct_id = se.struct_id',"LEFT")
            ->where($subordinateIdSubMap)
            ->group("ses.user_id")
            ->field("ses.user_id")
            ->buildSql();
        //var_exp($subordinateIdSubQuery,'$subordinateIdSubQuery',1);
        $map_str = " c.handle_man in $subordinateIdSubQuery ";
        $having = "";
        //列筛选
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }else{
                $having = null;
            }
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $orderPrefix = "";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        $listOrder = [$order=>$direction];//聚合后排序
        switch ($order){
            case "id":
            case "customer_name":
            case "grade":
            case "take_time":
                $orderPrefix = "c.";
                $subOrder = [$orderPrefix.$order=>$direction];
                $listOrder = [$order=>$direction];
                break;
            case "contact_name":
                $orderPrefix = "cc.";
                $subOrder = [$orderPrefix."id"=>"desc"];
                $listOrder = [$order=>$direction];
                break;
            case "comm_status":
                $orderPrefix = "cn.";
                $subOrder = [
                    $orderPrefix."tend_to"=>$direction,
                    $orderPrefix."phone_correct"=>$direction,
                    $orderPrefix."profile_correct"=>$direction,
                    $orderPrefix."call_through"=>$direction,
                    $orderPrefix."is_wait"=>$direction,
                ];
                $listOrder = [
                    "tend_to"=>$direction,
                    "phone_correct"=>$direction,
                    "profile_correct"=>$direction,
                    "call_through"=>$direction,
                    "is_wait"=>$direction,
                ];
                break;
            case "remind_time":
                $orderPrefix = "cn.";
                $order = "wait_alarm_time";
                $subOrder = [
                    $orderPrefix."is_wait"=>"desc",
                    $orderPrefix.$order=>$direction,
                ];
                $listOrder = [
                    "is_wait"=>"desc",
                    $order=>$direction,
                ];
                break;
            case "last_trace_time":
                $orderPrefix = "ct.";
                $order = "create_time";
                $subOrder = [$orderPrefix.$order=>"desc"];
                $listOrder = [$order=>$direction];
                break;
            case "guess_money":
                //$orderPrefix = "sc.";
                //$idsOrder = [$orderPrefix.$order=>$direction];
                $listOrder = [$order=>"all_guess_money"];
                break;
        }
        $subOrder["sc.id"] = "desc";//商机
        $subOrder["cn.id"] = "desc";//沟通状态
        $subOrder["ct.id"] = "desc";//客户跟踪
        $subOrder["cc.id"] = "desc";//联系人

        //固定显示字段
        $subField = [
            "c.id",
            "c.customer_name",
            //"c.take_type",
            //"c.grade",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
            //"'沟通状态' as comm_status",
            "sc.sale_name",
            "(case when sc.sale_status<1 then 0 when sc.sale_status>4 then 0 else sc.guess_money end) as in_progress_guess_money",//all_guess_money
            "(case when sc.sale_status=5 then sc.final_money else 0 end) as win_final_money",//all_final_money
            "cc.contact_name",
            "cc.phone_first",
            "ct.create_time as last_trace_time",
            "c.take_time",//领取时间
            "'' as contract_due_time",
            "cn.wait_alarm_time as remind_time",
            //"'所在列' as in_column",
            "sc.sale_status",
            "sc.id assale_id",
            "ct.id ct_id",
        ];
        $listField = [
            "id",
            "customer_name",
            //"take_type",
            //"grade",
            "tend_to",
            "phone_correct",
            "profile_correct",
            "call_through",
            "is_wait",
            //"comm_status",
            "group_concat(sale_name ORDER BY l.sale_id DESC) as sale_names",
            "SUM(in_progress_guess_money) as all_guess_money",
            "SUM(win_final_money) as all_final_money",
            "contact_name",
            "phone_first",
            "last_trace_time",
            "take_time",
            "contract_due_time",
            "remind_time",
            "(case when phone_correct = 0 and profile_correct = 0 then 8 when tend_to = 0 then 6 when is_wait = 0 then 5 when sale_status = 0 then 7 when ct_id = '' or ct_id is null then 2 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >".$to_halt_day_max." then 4 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >3 then 1 else 3 end ) as in_column",
            "sale_status",
            "ct_id",
        ];
        //动态显示字段:获取途径
        if(in_array("take_type", $field)){
            $subField[] = "c.take_type";
            $listField[] = "take_type";
        }
        //动态显示字段:客户级别
        if(in_array("grade", $field)){
            $subField[] = "c.grade";
            $listField[] = "grade";
        }

        $subQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")//sc.employee_id = c.handle_man
            //->join($this->dbprefix.'contract_applied ca','ca.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")//ct.operator_id = c.handle_man
            ->where($map)
            ->where($map_str)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $customerList = $this->model
            ->table($subQuery." l")
            ->group("id")
            ->order($listOrder)
            ->having($having)
            ->limit($offset,$num)
            ->field($listField)
            ->select();
        //var_exp($customerList,'$customerList',1);
        //具体的值处理
        foreach ($customerList as &$customer){
            $customer['comm_status'] = getCommStatusByArr([
                "tend_to"=>$customer['tend_to'],
                "phone_correct"=>$customer['phone_correct'],
                "profile_correct"=>$customer['profile_correct'],
                "call_through"=>$customer['call_through'],
                "is_wait"=> $customer['is_wait'],
            ]);
            if($protect_customer_day_max){//$protect_customer_day_max;//
                $customer['save_time'] = intval($protect_customer_day_max-($now_time-$customer['take_time'])/60/60/24);
            }else{
                $customer['save_time'] = "无";
            }
        }
        return $customerList;
    }

    /**
     * 获取待处理的客户
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getPendingCustomer($num=10,$page=0,$filter=null,$field=null,$order="id",$direction="desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map = $this->_getMapByFilter($filter,[]);
        $map['belongs_to'] = 4;

        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")
            ->select();
        return $customerList;
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        //客户状态
        if(in_array("belongs_to",$filter_column) && array_key_exists("belongs_to", $filter)){
            $map["c.belongs_to"] = $filter["belongs_to"];
        }
        //跟踪人
        if(in_array("tracer",$filter_column) && array_key_exists("tracer", $filter)){
            $map["te.truename"] = $filter["tracer"];
        }
        //维护人
        if(in_array("guardian",$filter_column) && array_key_exists("guardian", $filter)){
            $map["te.truename"] = $filter["guardian"];
        }
        //添加人
        if(in_array("add_man",$filter_column) && array_key_exists("add_man", $filter)){
            $map["ae.truename"] = $filter["add_man"];
        }
        //客户来源
        if(in_array("resource_from",$filter_column) && array_key_exists("resource_from", $filter)){
            $map["c.resource_from"] = $filter["resource_from"];
        }
        //获取途径
        if(in_array("take_type",$filter_column) && array_key_exists("take_type", $filter)){
            $map["c.take_type"] = $filter["take_type"];
        }
        //客户级别
        if(in_array("grade",$filter_column) && array_key_exists("grade", $filter)){
            $map["c.grade"] = $filter["grade"];
        }
        //客户名称
        if(in_array("customer_name",$filter_column) && array_key_exists("customer_name", $filter)){
            $map["c.customer_name"] = ["like","%".$filter["customer_name"]."%"];
        }
        //联系人名称
        if(in_array("contact_name",$filter_column) && array_key_exists("contact_name", $filter)){
            $map["cc.contact_name"] = ["like","%".$filter["contact_name"]."%"];
        }
        //沟通状态
        if(in_array("comm_status",$filter_column) && array_key_exists("comm_status", $filter)){
            $comm_status_arr = getCommStatusArr($filter["comm_status"]);
            foreach ($comm_status_arr as $k=>$comm_status_item){
                $map["cn.".$k] = $comm_status_item;
            }
        }
        //商机业务
        if(in_array("sale_chance",$filter_column) && array_key_exists("sale_chance", $filter)){
            $map["sc.business_id"] = $filter["sale_chance"];
        }
        //可见范围
        if(in_array("is_public",$filter_column) && array_key_exists("is_public", $filter)){
            $map["c.business_id"] = $filter["is_public"];
        }
        return $map;
    }

    /**
     * 根据员工id查询我的客户页列上的数量
     * @param $uid int 员工id
     * @param $filter array 过滤条件
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function getColumnNum($uid,$filter=null){
        $now_time = time();
        //获取客户配置
        $struct_ids = getStructureIds($uid);
        //$protect_customer_days = [];
        $protect_customer_day_max = 0;//暂定员工属于两个部门且有客户,保有时间按长的来
        $to_halt_day_max = 0;//暂定员工属于两个部门且有客户,划归停滞客户的天数按长的来
        foreach ($struct_ids as $struct_id){
            $structure = intval($struct_id);
            $setting_map = "find_in_set('$structure', set_to_structure)";
            $customerSettingModel = new CustomerSetting();
            $customerSetting = $customerSettingModel->getCustomerSetting(1,0,$setting_map);
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
        $map = $this->_getMapByFilter($filter,["take_type","grade","customer_name","contact_name","comm_status","sale_chance"]);
        $map['c.belongs_to'] = 3;
        $map['c.handle_man'] = $uid;

        //排序
        $order = "id";
        $direction = "desc";
        $orderPrefix = "c.";
        $subOrder = [$orderPrefix.$order=>$direction];//聚合前排序
        $listOrder = [$order=>$direction];//聚合后排序
        $subOrder["sc.id"] = "desc";//商机
        $subOrder["cn.id"] = "desc";//沟通状态
        $subOrder["ct.id"] = "desc";//客户跟踪

        //固定显示字段
        $subField = [
            "c.id",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
            "ct.create_time as last_trace_time",
            "c.take_time",
            "sc.sale_status",
            "ct.id ct_id",
        ];
        $listField = [
            "id",
            /*"tend_to",
            "phone_correct",
            "profile_correct",
            "call_through",
            "is_wait",
            "last_trace_time",
            "take_time",
            "sale_status",
            "ct_id",*/
            "(case when phone_correct = 0 and profile_correct = 0 then 8 when tend_to = 0 then 6 when is_wait = 0 then 5 when sale_status = 0 then 7 when ct_id = '' or ct_id is null then 2 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >".$to_halt_day_max." then 4 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >3 then 4 else 3 end ) as in_column",
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
        $subQuery = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")//sc.employee_id = c.handle_man
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")//ct.operator_id = c.handle_man
            ->where($map)
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        $customerQuery = $this->model
            ->table($subQuery." f")
            ->group("id")
            ->order($listOrder)
            ->field($listField)
            ->buildSql();
        //var_exp($customerQuery,'$customerQuery',1);
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
     * 根据员工id查询客户信息
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getCustomerByUserId($user_id){
        return $this->model->table($this->table)->where('handle_man',$user_id)->field('id')->find();
    }

    /**
     * 根据员工ids获取客户信息
     * @param $ids
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getCustomersByUserIds($ids){
        $field = [
            "e.id as userid",
            "e.truename",
            "c.id as customer_id",
        ];
        $customers = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'employee e','c.handle_man = e.id',"LEFT")
            ->field($field)
            ->where('handle_man','in',$ids)
            ->select();
        return $customers;
    }

    /**
     * 获取导出客户信息
     * @param $uid int|array 员工id
     * @param $scale int|array 客户类型
     * @param $self int 是否只查询自己的客户
     * @param $ids int|array 客户id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getExportCustomers($uid,$scale,$self,$ids){
        $map = null;
        if($self){
            $map['c.handle_man'] = $uid;
        }
        if($ids){
            $map['c.id'] = ["in",$ids];
        }
        if($scale){
            $map['c.belongs_to'] = $scale;
        }
        return $this->model->table($this->table)->alias('c')
            //->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id')
            ->where($map)
            ->field([
                "c.customer_name",
                "c.telephone",
                "c.address","
                CONCAT(c.lat,',',c.lng) as location",
                "c.field,c.website"
            ])
            ->select();
    }

    /**
     * 变更为某员工的客户
     * @param $customer_ids array 客户类型
     * @param $uid int|array 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function takeCustomers($customer_ids,$uid){
        $map['handle_man'] = 0;
        $map['belongs_to'] = ["in",[1,2]];
        $map['id'] = ["in",$customer_ids];
        $data['belongs_to'] = 3;
        $data['handle_man'] = $uid;
        return $this->model
            ->table($this->table)
            ->where($map)
            ->update($data);
    }

    /**
     * 释放客户
     * @param $customer_ids array 客户类型
     * @param $uid int|array 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function releaseCustomers($customer_ids,$uid=null){
        if($uid){
            $map['handle_man'] = $uid;
        }
        $map['belongs_to'] = ["in",[3,4]];
        $map['id'] = ["in",$customer_ids];
        $data['belongs_to'] = 2;
        $data['handle_man'] = 0;
        return $this->model
            ->table($this->table)
            ->where($map)
            ->update($data);
    }

    /**
     * 更改客户可见范围
     * @param $customer_ids array 客户类型
     * @param $is_public int 是否公开
     * @param $employees string 可见的员工id
     * @param $departments string 可见的部门id
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function changeCustomersVisibleRange($customer_ids,$is_public,$employees,$departments){
        $map['id'] = ["in",$customer_ids];
        $data['is_public'] = $is_public;
        $data['public_to_employee'] = $employees;
        $data['public_to_department'] = $departments;
        return $this->model
            ->table($this->table)
            ->where($map)
            ->update($data);
    }

    /**
     * 查询单个客户信息
     * @param $cid int 客户id
     * @return int|string
     * created by blu10ph
     */
    public function getCustomer($cid){
        $field = [
            "c.*",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
        ];
        $customer = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->where('c.id',$cid)
            ->group("c.id")
            ->field($field)
            ->find();
        if(empty($customer)){
            return null;
        }
        $customer['comm_status'] = getCommStatusByArr([
            "tend_to"=>$customer['tend_to'],
            "phone_correct"=>$customer['phone_correct'],
            "profile_correct"=>$customer['profile_correct'],
            "call_through"=>$customer['call_through'],
            "is_wait"=> $customer['is_wait'],
        ]);
        $customer['lat'] = "".number_format($customer['lat'],6,".","");
        $customer['lng'] = "".number_format($customer['lng'],6,".","");
        return $customer;
    }

    /**
     * 查询单个客户信息包含是否需要签到
     * @param $cid int 客户id
     * @return int|string
     * created by blu10ph
     */
    public function getCustomerAndHaveVisit($cid){
        $field = [
            "c.*",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait",
            "sum(CASE WHEN bfiln.item_id = 3 THEN 1 ELSE 0 END) AS need_sign_num",
            "group_concat(distinct (CASE WHEN bfiln.item_id = 3 THEN sc.id END)) AS sale_id",
        ];
        $customer = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'business_flow_item_link bfil','bfil.setting_id = sc.business_id and sc.sale_status=bfil.item_id',"LEFT")
            ->join($this->dbprefix.'business_flow_item_link bfiln','bfiln.setting_id = sc.business_id and bfiln.order_num = bfil.order_num+1 and bfiln.item_id=3',"LEFT")
            ->where('c.id',$cid)
            ->group("c.id")
            ->field($field)
            ->find();
        //->select(false);
        //var_exp($customer,'$customer',1);
        if(empty($customer)){
            return null;
        }
        $customer['comm_status'] = getCommStatusByArr([
            "tend_to"=>$customer['tend_to'],
            "phone_correct"=>$customer['phone_correct'],
            "profile_correct"=>$customer['profile_correct'],
            "call_through"=>$customer['call_through'],
            "is_wait"=> $customer['is_wait'],
        ]);
        if(!empty($customer['sale_id'])){
            $customer['sale_id'] = explode(",",$customer['sale_id']);
        }else{
            $customer['sale_id'] = [];
        }
        $customer['lat'] = "".number_format($customer['lat'],6,".","");
        $customer['lng'] = "".number_format($customer['lng'],6,".","");
        return $customer;
    }

    /**
     * 添加单个客户信息
     * @param $data
     * @return boolean|int|string
     * created by messhair
     */
    public function addCustomer($data){
        return $customer_id = $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 根据客户id修改客户信息
     * @param $customer_id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setCustomer($customer_id,$data){
        return $this->model->table($this->table)->where('id',$customer_id)->update($data);
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
    public function getCustomerRanking($start_time,$end_time,$uids,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["c.add_time"][] = ["egt",$start_time];
        $map["c.add_time"][] = ["elt",$end_time];
        $map["c.add_man"] = ["in",$uids];
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $group="c.add_man";
        $order="num desc,standard_time asc";
        $rankingList = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'employee e','c.add_man = e.id',"LEFT")
            ->where($map)
            ->group($group)
            ->order($order)
            //->limit($offset,$num)
            ->field("e.id as employee_id,e.truename,count(c.id) num,MAX(c.add_time) as standard_time,IF (count(c.id) >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($callRecordRanking,'$callRecordRanking',1);
        if($num==1&&$page==0&&$rankingList){
            $rankingList = $rankingList[0];
        }
        return $rankingList;
    }

    /**
     * 悬赏任务参与人排行榜列表
     * @param $start_time
     * @param $end_time
     * @param $uids 任务参与人id的数组集合
     * @param $task_id 任务id
     * @param int $standard
     * @param int $num
     * @param int $page
     * @param null $map
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getEmployeeRanking($start_time,$end_time,$uids,$task_id,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map['e.id']=array('in',$uids);
        $map['t.task_id']=$task_id;
        $rankingList=$this->model->table($this->dbprefix.'employee e')
            ->join($this->dbprefix.'employee_task_take t','e.id=t.take_employee','LEFT')
            ->where($map)
            ->field("e.id as employee_id,e.truename,t.whether_help,t.id as take_id")
            ->select();
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
    public function getCustomerStandard($start_time,$end_time,$uids,$standard,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["c.add_time"][] = ["egt",$start_time];
        $map["c.add_time"][] = ["elt",$end_time];
        $map["c.add_man"] = ["in",$uids];
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $order="c.add_man asc,c.add_time asc";
        $vl_order="standard desc";
        $standard_order="is_standard desc,standard_time asc,num desc";
        $subQuery = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'employee e','c.add_man = e.id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit(999999)//2147483647?
            ->field("e.id as employee_id,e.truename,c.add_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $subQuery = $this->model->table($subQuery)->alias('sl')
            ->join("(SELECT @prev := '', @n := 0, @st := 0) init",'sl.employee_id = sl.employee_id',"LEFT")
            ->order($vl_order)
            ->limit(999999)//2147483647?
            ->field("sl.*,@n := IF (sl.employee_id != @prev, '1', @n + 1)+0 AS standard ,@st := IF (sl.employee_id != @prev, '0', @st) AS stre ,@prev := sl.employee_id AS ng ,@st := IF (@n = $standard, sl.add_time, @st) as standard_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $standardList = $this->model->table($subQuery)->alias('v')
            ->group("employee_id")
            ->order($standard_order)
            //->limit($offset,$num)
            ->field("employee_id,truename,standard as num,standard_time,IF (standard >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($standardList,'$standardList',1);
        if($num==1&&$page==0&&$standardList){
            $standardList = $standardList[0];
        }
        return $standardList;
    }

    public function getMycustomerForHelpList($uid){
        $con['handle_man']=$uid;
        return $this->model->table($this->table)->where($con)->field('id,customer_name')->select();
    }
}