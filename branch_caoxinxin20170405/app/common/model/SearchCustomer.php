<?php
/**
 * Created by blu10ph.
 * Date: 2017/04/24
 */
namespace app\common\model;

use app\common\model\Base;

class SearchCustomer extends Base
{
    public function __construct($corp_id =null){
        $this->table=config('database.prefix').'customer_search';
        parent::__construct($corp_id);
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
            $offset = $page*$num;
        }
        $map["status"]=1;
        $searchCustomerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->select();
        return ['res'=>$searchCustomerList ,'error'=>"0"];
    }

    /**
     * 查找搜索的客户是否有重复,并返回结果
     * @param $id int 客户id
     * @param $order string 排序规则
     * @return array
     * @throws \think\Exception
     */
    public function findRepeat($id,$order="id desc"){
        $map["id"]=$id;
        $map["status"]=1;
        $searchCustomer = $this->model
            ->table($this->table)
            ->where($map)
            ->find();

        $find_map = " id<>:id and status = 1 and ( customer_name = :customer_name or phone = :phone ) ";
        $bind = [
            "id"=>$id,
            "status"=>1,
            "customer_name"=>$searchCustomer["customer_name"],
            "phone"=>$searchCustomer["phone"],
        ];
        $repeatList[] = $this->model
            ->table($this->table)
            ->where($find_map)
            ->bind($bind)
            ->order($order)
            ->find();
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
        $b = $this->model->table($this->table)->insert($customer);
        return ['res'=>$b ,'error'=>"0"];
    }

    /**
     * 创建搜索的客户,并返回结果
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
     * 创建搜索的客户,并返回结果
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