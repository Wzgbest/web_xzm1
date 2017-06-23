<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\common\model;

use app\common\model\Base;
use think\Db;

class SearchCustomer extends Base
{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'customer_search';
        parent::__construct($corp_id);
    }

    /**
     * 获取搜索的客户
     * @param $uid int 用户id
     * @param $map array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function getAllSearchCustomer($uid,$map=null,$order="id desc"){
        $map["create_user"] = $uid;
        $map["status"]=1;
        $searchCustomerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->field(array("id","customer_name","phone","contact_name","industry","com_adds","website"))
            ->select();
        return ['res'=>$searchCustomerList ,'error'=>"0"];
    }

    /**
     * 获取搜索的客户
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 客户筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function getSearchCustomer($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $searchCustomerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")//TODO
            ->select();
        return ['res'=>$searchCustomerList ,'error'=>"0"];
    }

    /**
     * 查找搜索的客户是否有重复,并返回结果
     * @param $id int 客户id
     * @param $uid int 用户id
     * @return array
     * @throws \think\Exception
     */
    public function findRepeat($id,$uid){
        $find_map["id"]=$id;
        $find_map["create_user"] = $uid;
        $find_map["status"]=1;
        $searchCustomer = $this->model
            ->table($this->table)
            ->where($find_map)
            ->field("*")//TODO
            ->find();

        $searchSubMap = " id<>:id and status = 1 and ( customer_name = :customer_name or phone = :phone ) ";
        $searchSubBind = [
            "id"=>$id,
            "status"=>1,
            "customer_name"=>$searchCustomer["customer_name"],
            "phone"=>$searchCustomer["phone"],
        ];
        $searchSubOrder="id desc";
        $searchSubSql = $this->model
            ->table($this->table)
            ->where($searchSubMap)
            ->bind($searchSubBind)
            ->order($searchSubOrder)
            ->limit(1)
            ->field("'sc' as tn,id,0 as s_id")
            ->buildSql();
        $customerSubMap = " customer_name = :customer_name or telephone = :phone ";
        $customerSubBind = [
            "customer_name"=>$searchCustomer["customer_name"],
            "phone"=>$searchCustomer["phone"],
        ];
        $customerSubOrder="id desc";
        $customerSubSql = $this->model
            ->table($this->dbprefix.'customer')
            ->where($customerSubMap)
            ->bind($customerSubBind)
            ->order($customerSubOrder)
            ->limit(1)
            ->field("'c' as tn,id,0 as s_id")
            ->buildSql();
        $customerContactSubMap = " contact_name = :customer_name and ( phone_first = :phone or phone_second = :phone or phone_third = :phone ) ";
        $customerContactSubBind = [
            "customer_name"=>$searchCustomer["customer_name"],
            "phone"=>$searchCustomer["phone"],
        ];
        $customerContactSubOrder="id desc";
        $customerContactSubSql = $this->model
            ->table($this->dbprefix.'customer_contact')
            ->where($customerContactSubMap)
            ->bind($customerContactSubBind)
            ->order($customerContactSubOrder)
            ->limit(1)
            ->field("'cc' as tn,customer_id as id,id as s_id")
            ->buildSql();
        $querySql = $searchSubSql." UNION ".$customerSubSql." UNION ".$customerContactSubSql;
        //var_exp($querySql,'$querySql',1);
        $queryList = $this->link->query($querySql);
        //var_exp($repeatList,'$repeatList',1);
        $repeatList = [];
        if($queryList){
            $first = $queryList[0];
            $table = $first["tn"];
            $tableArr = ["sc"=>"search_customer","c"=>"customer","cc"=>"customer_contact",];
            $tableName = $tableArr[$table];
            //TODO 获取详细信息
        }
        return ['res'=>$repeatList ,'error'=>"0"];
    }

    /**
     * 创建搜索的客户,并返回结果
     * @param $customer array 客户信息
     * @return array
     * @throws \think\Exception
     */
    public function createSearchCustomer($customer){
        $customer_name = $customer['customer_name'];
        $phone = $customer['phone'];
        if(empty($customer_name)||empty($phone)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少客户名称或电话！"];
        }
        $b = $this->model->table($this->table)->insertGetId($customer);
        return ['res'=>$b ,'error'=>"0"];
    }

    /**
     * 更新搜索的客户,并返回结果
     * @param $customer array 客户信息
     * @param $map array 客户筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function updateSearchCustomer($customer,$map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少更新目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->data($customer)->save();
        return ['res'=>$b ,'error'=>"0"];
    }

    /**
     * 删除搜索的客户,并返回结果
     * @param $map array 客户筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delSearchCustomer($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return ['res'=>$b ,'error'=>"0"];
    }
}