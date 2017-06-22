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
    public function __construct()
    {
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'customer';
        parent::__construct();
    }

    /**
     * 获取管理的客户
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getManagerCustomer($num=10,$page=0,$filter=null,$order="id desc"){//TODO
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map = $this->_getMapByFilter($filter,[]);
        $map['belongs_to'] = 1;


        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")
            ->select();
        return $customerList;
    }

    /**
     * 获取公海池中的客户
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getPoolCustomer($num=10,$page=0,$filter=null,$order="id desc"){//TODO
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map = $this->_getMapByFilter($filter,[]);
        $map['belongs_to'] = 2;

        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")
            ->select();
        return $customerList;
    }

    /**
     * 获取我的客户
     * @param $num int 数量
     * @param $page int 页
     * @param $uid int 员工id
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @param $direction string 排序方向
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getSelfCustomer($num=10,$page=0,$uid,$filter=null,$field=null,$order="id",$direction="desc"){
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
        $map = $this->_getMapByFilter($filter,["take_type","grade","customer_name","contact_name","comm_status","sale_chance"]);
        $map['c.belongs_to'] = 3;
        $map['c.handle_man'] = $uid;

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
            //"sc.sale_name",
            "scb.business_name as sale_biz_name",
            "(case when sc.sale_status<1 then 0 when sc.sale_status<4 then 0 else sc.guess_money end) as in_progress_guess_money",//all_guess_money
            "(case when sc.sale_status=5 then sc.final_money else 0 end) as win_final_money",//all_final_money
            "cc.contact_name",
            "cc.phone_first",
            "ct.create_time as last_trace_time",
            "c.take_time",//领取时间
            "ca.due_time as contract_due_time",
            "cn.wait_alarm_time as remind_time",
            //"'所在列' as in_column",
            "sc.sale_status",
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
            //"group_concat(sale_name) as sale_names",
            "group_concat(sale_biz_name) as sale_biz_names",
            "SUM(in_progress_guess_money) as all_guess_money",
            "SUM(win_final_money) as all_final_money",
            "contact_name",
            "phone_first",
            "last_trace_time",
            "take_time",
            "contract_due_time",
            "remind_time",
            "(case when phone_correct = 0 and profile_correct = 0 then 8 when tend_to = 0 then 6 when is_wait = 0 then 5 when sale_status = 0 then 7 when ct_id = '' or ct_id is null then 2 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >".$to_halt_day_max." then 4 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >3 then 4 else 3 end ) as in_column",
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
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")//sc.employer_id = c.handle_man
            ->join($this->dbprefix.'business scb','scb.id = sc.bussiness_id',"LEFT")
            ->join($this->dbprefix.'contract_applied ca','ca.sale_id = sc.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")//ct.operator_id = c.handle_man
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
            /*使用sql语句计算
            $customer["last_trace_day"] = intval(($now_time-$customer["last_trace_time"])/60/60/24);
            if($customer["phone_correct"]==0 && $customer["profile_correct"]==0){//号码无效或资料有误
                $customer['in_column'] = 8;//无效客户
            }elseif($customer["tend_to"]==0){//无意向
                $customer['in_column'] = 6;//无意向
            }elseif($customer["is_wait"]==0){//待沟通
                $customer['in_column'] = 5;//待定
            }elseif($customer["sale_status"]==5){//待沟通
                $customer['in_column'] = 7;//待定
            }elseif(!$customer["ct_id"]){//没有跟踪记录
                $customer['in_column'] = 2;//未跟进
            }elseif($customer["last_trace_day"]>$to_halt_day_max){//最新跟进时间,超过客户设置中的时间
                $customer['in_column'] = 4;//停滞
            }elseif($customer["last_trace_day"]>3){//最新跟进时间,超过三天
                $customer['in_column'] = 1;//停滞
            }else{//不在以上状态
                $customer['in_column'] = 3;//正常跟进
            }
            */
        }
        return $customerList;
    }

    /**
     * 获取我的下属的客户
     * @param $num int 数量
     * @param $page int 页
     * @param $uid int 员工id
     * @param $filter array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     * created by blu10ph
     */
    public function getSubordinateCustomer($num=10,$page=0,$uid,$filter=null,$order="id desc"){//TODO
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map = $this->_getMapByFilter($filter,[]);
        $uids = [$uid];//TODO 获取下属uid
        $map['belongs_to'] = 3;
        $map['handle_man'] = ["in",$uids];

        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")
            ->select();
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
    public function getPendingCustomer($num=10,$page=0,$filter=null,$order="id desc"){//TODO
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
            $map["c.customer_name"] = $filter["customer_name"];
        }
        //联系人名称
        if(in_array("contact_name",$filter_column) && array_key_exists("contact_name", $filter)){
            $map["cc.contact_name"] = $filter["contact_name"];
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
            $map["sc.bussiness_id"] = $filter["sale_chance"];
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
            "tend_to",
            "phone_correct",
            "profile_correct",
            "call_through",
            "is_wait",
            "last_trace_time",
            "take_time",
            "sale_status",
            "ct_id",
            "(case when phone_correct = 0 and profile_correct = 0 then 8 when tend_to = 0 then 6 when is_wait = 0 then 5 when sale_status = 0 then 7 when ct_id = '' or ct_id is null then 2 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >".$to_halt_day_max." then 4 when FLOOR((unix_timestamp()-last_trace_time)/60/60/24) >3 then 4 else 3 end ) as in_column",
        ];
        $countField = [
            "(case when in_column = 1 then 1 else 0 end) as column_1",
            "(case when in_column = 2 then 1 else 0 end) as column_2",
            "(case when in_column = 3 then 1 else 0 end) as column_3",
            "(case when in_column = 4 then 1 else 0 end) as column_4",
            "(case when in_column = 5 then 1 else 0 end) as column_5",
            "(case when in_column = 6 then 1 else 0 end) as column_6",
            "(case when in_column = 7 then 1 else 0 end) as column_7",
            "(case when in_column = 8 then 1 else 0 end) as column_8",
        ];
        $subQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")//sc.employer_id = c.handle_man
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
        $listCount = $this->model
            ->table($customerQuery." lc")
            ->field($countField)
            ->select();
        //var_exp($listCount,'$listCount',1);
        return $listCount;
    }

    /**
     * 根据员工id查询客户信息
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getCustomerByUserId($user_id)
    {
        return $this->model->table($this->table)->where('handle_man',$user_id)->field('id')->find();
    }

    /**
     * 根据员工ids获取客户信息
     * @param $ids
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getCustomersByUserIds($ids)
    {
        $field = [
            "e.id as userid",
            "e.truename",
            "c.id as customer_id",
        ];
        $customers = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'employer e','c.handle_man = e.id')
            ->field($field)
            ->where('handle_man','in',$ids)
            ->select();
        return $customers;
    }

    /**
     * 获取所有客户信息
     * @param $uid int|array 员工id
     * @param $scale int|array 客户类型
     * @param $self int 是否只查询自己的客户
     * @return false|\PDOStatement|string|\think\Collection
     * created by blu10ph
     */
    public function getExportCustomers($uid,$scale,$self){
        if($self){
            $map['c.handle_man'] = $uid;
        }else{
            $map['c.handle_man'] = ["in",[0,$uid]];
        }
        $map['c.belongs_to'] = $scale;
        return $this->model->table($this->table)->alias('c')
            //->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id')
            ->where($map)
            ->field("c.customer_name,c.telephone,c.address,CONCAT(c.lat,',',c.lng),c.field,c.website")
            ->select();
    }

    /**
     * 查询单个客户信息
     * @param $cid int 客户id
     * @return int|string
     * created by blu10ph
     */
    public function getCustomer($cid)
    {
        $field = [
            "c.*",
            "cn.tend_to",
            "cn.phone_correct",
            "cn.profile_correct",
            "cn.call_through",
            "cn.is_wait"
        ];
        $customer = $this->model->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->where('id',$cid)
            ->field($field)
            ->find();
        $customer['comm_status'] = getCommStatusByArr([
            "tend_to"=>$customer['tend_to'],
            "phone_correct"=>$customer['phone_correct'],
            "profile_correct"=>$customer['profile_correct'],
            "call_through"=>$customer['call_through'],
            "is_wait"=> $customer['is_wait'],
        ]);
        return $customer;
    }

    /**
     * 添加单个客户信息
     * @param $data
     * @return boolean|int|string
     * created by messhair
     */
    public function addCustomer($data)
    {
        $customer_id = 0;
        $this->link->startTrans();
        try{
            $customer_id = $this->model->table($this->table)->insertGetId($data);
            $customerNegotiate = getCommStatusArr($data["comm_status"]);
            $customerNegotiate["customer_id"] = $customer_id;
            $this->model->table($this->dbprefix.'negotiate')->insert($customerNegotiate);
            $this->link->commit();
        }catch (\Exception $ex) {
            return false;
        }
        return $customer_id;
    }

    /**
     * 根据客户id修改客户信息
     * @param $customer_id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setCustomer($customer_id,$data)
    {
        $customerNegotiate = getCommStatusArr($data["comm_status"]);
        $this->model->table($this->dbprefix.'negotiate')->where('customer_id',$customer_id)->update($customerNegotiate);
        return $this->model->table($this->table)->where('id',$customer_id)->update($data);
    }
}