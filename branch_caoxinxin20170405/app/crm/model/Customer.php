<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;
use app\systemsetting\model\CustomerSetting;
use phpDocumentor\Reflection\Types\This;

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
    public function getManagerCustomer($num=10,$page=0,$filter=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map['belongs_to'] = 1;
        //TODO $filter
        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")//TODO
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
    public function getPoolCustomer($num=10,$page=0,$filter=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map['belongs_to'] = 2;
        //TODO $filter
        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")//TODO
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
        //获取客户配置
        $now_time = time();
        $struct_ids = getStructureIds($uid);
        //$protect_customer_days = [];
        $protect_customer_day_max = 0;//暂定员工属于两个部门且有客户,保有时间按长的来
        foreach ($struct_ids as $struct_id){
            $structure = intval($struct_id);
            $setting_map = "find_in_set('$structure', set_to_structure)";
            $customerSettingModel = new CustomerSetting();
            $customerSetting = $customerSettingModel->getCustomerSetting(1,0,$setting_map);
            if(!$customerSetting){
                $customerSetting["protect_customer_day"] = 0;
            }
            //$protect_customer_days[$structure] = $customerSetting["protect_customer_day"];
            if($customerSetting["protect_customer_day"]>$protect_customer_day_max){//暂定员工属于两个部门且有客户,保有时间按长的来
                $protect_customer_day_max = $customerSetting["protect_customer_day"];
            }
        }

        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        //TODO $filter 中的筛选条件	商机
        $map = $this->_getMapByFilter($filter,["take_type","grade","customer_name","contact_name","comm_status"]);
        $map['c.belongs_to'] = 3;
        $map['c.handle_man'] = $uid;

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        //TODO 上次跟进时间	提醒时间
        $orderPrefix = "";
        $idsOrder = [$orderPrefix.$order=>$direction];//列表排序
        $subOrder = "sc.id desc,ct.id desc";//沟通状态、销售机会和用户追踪等字段,最新的条目需在聚合之前排序
        $listOrder = [$order=>$direction];//列表排序
        switch ($order){
            case "id":
            case "customer_name":
            case "grade":
            case "take_time":
                $orderPrefix = "c.";
                $idsOrder = [$orderPrefix.$order=>$direction];//列表排序
                $listOrder = [$order=>$direction];//列表排序
                break;
            case "contact_name":
                $orderPrefix = "cc.";
                $idsOrder = [$orderPrefix.$order=>$direction];//列表排序
                $listOrder = [$order=>$direction];//列表排序
                break;
            case "comm_status":
                $orderPrefix = "cn.";
                $idsOrder = [
                    $orderPrefix."tend_to"=>$direction,
                    $orderPrefix."phone_correct"=>$direction,
                    $orderPrefix."profile_correct"=>$direction,
                    $orderPrefix."call_through"=>$direction,
                    $orderPrefix."is_wait"=>$direction,
                ];//列表排序
                $listOrder = [
                    "tend_to"=>$direction,
                    "phone_correct"=>$direction,
                    "profile_correct"=>$direction,
                    "call_through"=>$direction,
                    "is_wait"=>$direction,
                ];//列表排序
                break;
            case "guess_money":
                $orderPrefix = "sc.";
                $idsOrder = [$orderPrefix.$order=>$direction];//列表排序
                $listOrder = [$order=>$direction];//列表排序
                break;
        }

        //显示字段
        //TODO $field处理 上次跟进时间	预计合同到期时间	提醒时间	所在列
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
            "sc.guess_money",
            "sc.final_money",
            "cc.contact_name",
            "cc.phone_first",
            "ct.create_time as last_trace_time",//TODO 上次跟进定义
            "c.take_time",//领取时间
            "'合同到期时间' as contract_due_time",
            "'提醒时间' as remind_time",
            //"'所在列' as in_column"//TODO 所在列后期计算
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
            "group_concat(sale_name) as sale_names",
            "guess_money",
            "final_money",
            "contact_name",
            "phone_first",
            "last_trace_time",
            "take_time",
            "contract_due_time",
            "remind_time",
            //"in_column"
        ];
        //获取途径
        if(in_array("take_type", $field)){
            $subField[] = "c.take_type";
            $listField[] = "take_type";
        }
        //客户级别
        if(in_array("grade", $field)){
            $subField[] = "c.grade";
            $listField[] = "grade";
        }

        //构建查询当前页cid的sql语句,构建查询cid范围内数据并排序的sql语句,最后合并当前数据
        $idsQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")
            ->where($map)
            ->order($idsOrder)
            ->limit($offset,$num)
            ->group("c.id")
            ->field("c.id")
            ->buildSql();
        $subQuery = $this->model
            ->table($this->table)->alias('c')
            ->join($this->dbprefix.'customer_contact cc','cc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_negotiate cn','cn.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'sale_chance sc','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'customer_trace ct','ct.customer_id = c.id',"LEFT")
            ->where("c.id in ( select t.id from ".$idsQuery." t ) ")
            ->order($subOrder)
            ->field($subField)
            ->buildSql();
        $customerList = $this->model
            ->table($subQuery." c")
            ->group("id")
            ->order($listOrder)
            ->field($listField)
            ->select();

        //具体的值处理
        //TODO 所在列
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
    public function getSubordinateCustomer($num=10,$page=0,$uid,$filter=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $uids = [$uid];//TODO 获取下属uid
        $map['belongs_to'] = 3;
        $map['handle_man'] = ["in",$uids];
        //TODO $filter
        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")//TODO
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
    public function getPendingCustomer($num=10,$page=0,$filter=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map['belongs_to'] = 4;
        //TODO $filter
        $customerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")//TODO
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
        return $map;
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
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'employer b','a.handle_man = b.id')
            ->field('b.id as userid,b.truename,a.id as customer_id')
            ->where('handle_man','in',$ids)
            ->select();
    }

    /**
     * 获取所有客户信息
     * @param $userid 员工id
     * @param $scale 客户类型，1 我的客户 2 公海池 3我的客户 4待处理
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllCustomers($userid,$scale)
    {
        return $this->model->table($this->table)
            ->field('customer_name,resource_from,telephone,add_man,add_batch,handle_man,take_time,field,grade,address,location,website,remark,belongs_to,is_public,public_to_employer,public_to_department,is_partner')
            ->where('belongs_to',$scale)
            ->where('handle_man',$userid)
            ->select();
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
            ->field('c.customer_name,c.telephone,c.address,c.location,c.field,c.website')
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
        return $this->model->table($this->table)->where('id',$cid)->find();
    }

    /**
     * 添加单个客户信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addCustomer($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
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
        return $this->model->table($this->table)->where('id',$customer_id)->update($data);
    }

    /**
     * 删除客户
     * @param $cids 客户id数组
     * @return int
     * @throws \think\Exception
     * created by blu10ph
     */
    public function delCustomer($cids)
    {
        return $this->model->table($this->table)->where('id','in',$cids)->delete();
    }
}