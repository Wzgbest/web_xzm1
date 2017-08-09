<?php
/**
 * Created by messhair.
 * Date: 2017/5/15
 */
namespace app\crm\model;

use app\common\model\Base;

class CustomerTrace extends Base
{
    public function __construct($corp_id = null)
    {
        $this->table = config('database.prefix').'customer_trace';
        parent::__construct($corp_id);
    }

    /**
     * 添加单个客户更改信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addSingleCustomerMessage($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 添加多个客户信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addMultipleCustomerMessage($data)
    {
        return $this->model->table($this->table)->insertAll($data);
    }

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllCustomerTraceByCustomerId($customer_id)
    {
        return $this->model->table($this->table)->alias('ct')
            ->join($this->dbprefix.'employee e','ct.operator_id = e.id',"LEFT")
            ->where('customer_id',$customer_id)
            ->field("ct.*,e.truename operator_user_name")
            ->order("ct.id desc")
            ->select();
    }

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTraceByLastOperator($customer_id,$last_operator_id,$last_time=null,$num=10){
        $last_record_map["customer_id"] = $customer_id;
        $last_record_map["create_time"] = $last_time;
        $last_record_map["operator_id"] = $last_operator_id;
        $last_record = $this->model->table($this->table)
            ->where($last_record_map)
            ->field("id")
            ->order("id asc")
            ->find();
        if(empty($last_record)){
            return [];
        }

        $time_map["customer_id"] = $customer_id;
        $time_map["id"] = ["lt",$last_record['id']];
        $id_list = $this->model->table($this->table)
            ->where($time_map)
            ->field("id")
            ->order("id asc")
            ->group("create_time,operator_id")
            ->limit($num)
            ->select();
        if(empty($id_list)){
            return [];
        }
        //var_exp($id_list,'$id_list',1);
        $map["ct.customer_id"] = $customer_id;
        $map["ct.id"][] = ["lt",$last_record['id']];
        $map["ct.id"][] = ["egt",$id_list[0]['id']];
        return $this->model->table($this->table)->alias('ct')
            ->join($this->dbprefix.'employee e','ct.operator_id = e.id',"LEFT")
            ->where($map)
            ->field("ct.*,e.truename operator_user_name")
            ->order("ct.id desc")
            ->select();
    }

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @param $last_id int 最后一条跟踪记录id
     * @param $num int 数量
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTraceByLastId($customer_id,$last_id=null,$num=10){
        $time_map = [];
        if($last_id){
            $time_map["id"] = ["lt",$last_id];
        }
        $time_map["customer_id"] = $customer_id;
        $id_list = $this->model->table($this->table)
            ->where($time_map)
            ->field("id")
            ->order("id asc")
            ->group("create_time,operator_id")
            ->limit($num)
            ->select();
        if(empty($id_list)){
            return [];
        }
        //var_exp($id_list,'$id_list',1);
        $end_id = $id_list[0]['id'];
        $map = [];
        if($last_id&&$end_id){
            $map["ct.id"][] = ["lt",$last_id];
            $map["ct.id"][] = ["egt",$end_id];
        }elseif($end_id){
            $map["ct.id"] = ["egt",$end_id];
        }
        $map["ct.customer_id"] = $customer_id;
        return $this->model->table($this->table)->alias('ct')
            ->join($this->dbprefix.'employee e','ct.operator_id = e.id',"LEFT")
            ->where($map)
            ->field("ct.*,e.truename operator_user_name")
            ->order("ct.id desc")
            ->select();
    }

    /**获取
     * @param $id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTrace($id)
    {
        return $this->model->table($this->table)->where('d',$id)->find();
    }

    /**获取次数
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTraceCount($customer_id)
    {
        return $this->model->table($this->table)
            ->where('customer_id',$customer_id)
            ->group("create_time,operator_id")
            ->count();
    }

    /**获取数量
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getCustomerTraceLineCount($customer_id)
    {
        return $this->model->table($this->table)->where('customer_id',$customer_id)->count();
    }
}