<?php
/**
 * Created by messhair
 * Date: 17-3-17
 */
namespace app\huanxin\model;

use app\common\model\Base;

class TakeCash extends Base
{

    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'take_cash';
        parent::__construct($corp_id);
    }

    /**
     * 添加订单信息
     * @param $data
     * @return int|string
     */
    public function addOrderNumber($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    /**
     * 查询交易信息
     * @param $user
     * @return int|string
     */
    public function getOrderByPage($user,$page=1,$num=10){
        $map["userid"] = $user;
        $query = $this->model->table($this->table)
            ->where($map)
            ->order("id desc");
        if($num){
            //分页
            $offset = 0;
            if($page){
                $offset = ($page-1)*$num;
            }
            $query->limit($offset,$num);
        }
        $order_list = $query->select();
        return $order_list;
    }

    /**
     * 查询交易信息
     * @param $user
     * @return int|string
     */
    public function getOrderList($user,$num=10,$last_id=0){
        $map["userid"] = $user;
        if($last_id){
            $map["id"] = ["lt",$last_id];
        }
        $order_list = $this->model->table($this->table)
            ->where($map)
            ->order("id desc")
            ->limit($num)
            ->select();
        return $order_list;
    }
}