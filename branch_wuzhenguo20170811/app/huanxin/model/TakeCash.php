<?php
/**
 * Created by messhair
 * Date: 17-3-17
 */
namespace app\huanxin\model;

use app\common\model\Base;

class TakeCash extends Base{

    public function __construct($corp_id=null){
        $this->table = config('database.prefix').'take_cash';
        parent::__construct($corp_id);
    }

    /**
     * 添加订单信息
     * @param $data
     * @return int|string
     */
    public function addOrderNumber($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 添加订单信息
     * @param $datas
     * @return int|string
     */
    public function addMutipleOrderNumber($datas){
        return $this->model->table($this->table)->insertAll($datas);
    }

    /**
     * 查询交易信息
     * @param $user
     * @return int|string
     */
    public function getOrderByPage($user,$page=1,$num=10){
        $map["userid"] = $user;
        $field = ["tc.*","et.task_name"];
        $query = $this->model->table($this->table)->alias("tc")
            ->join(config('database.prefix').'employee_task et','tc.take_type = 5 and tc.take_id = et.id and ','left')
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
        $order_list = $query
            ->field($field)
            ->select();
        return $order_list;
    }

    /**
     * 查询交易信息
     * @param $user
     * @return int|string
     */
    public function getOrderList($user,$type=1,$take_type=0,$num=10,$last_id=0){
        $map["userid"] = $user;
        if($type){
            $map["money_type"] = $type;
        }
        if($take_type){
            $map["take_type"] = $take_type;
        }
        if($last_id){
            $map["id"] = ["lt",$last_id];
        }
        $field = ["tc.*","et.task_name"];
        $order_list = $this->model->table($this->table)->alias("tc")
            ->join(config('database.prefix').'employee_task et','tc.take_type = 5 and tc.take_id = et.id','left')
            ->where($map)
            ->order("id desc")
            ->limit($num)
            ->field($field)
            ->select();
        return $order_list;
    }
}