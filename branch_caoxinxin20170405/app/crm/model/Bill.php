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

class Bill extends Base{
    protected $dbprefix;
    public function __construct($corp_id){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix') . 'sale_order_bill';
        parent::__construct($corp_id);
    }

    /**
     * 查询发票申请
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 发票筛选条件
     * @param $field array 发票列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getBill($num=10,$page=0,$filter=null,$field=null,$order="sob.id",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $billList = $this->model->table($this->table)->alias('sob')
            ->join($this->dbprefix.'bill_setting bs','bs.id = sob.bill_type',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('sob.*,bs.bill_type as bill_type_name')
            ->select();
        //var_exp($billList,'$billList',1);
        if($num==1&&$page==0&&$billList){
            $billList = $billList[0];
        }
        return $billList;
    }
    /**
     * 查询发票数量
     * @param $filter array 发票筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getBillCount($filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        $billCount= $this->model->table($this->table)->alias('sob')
            ->where($map)
            ->count();
        return $billCount;
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        return $map;
    }

    public function getBillItem($ids){
        if(empty($ids)){
            return [];
        }
        $field = [
            "sobi.*",
        ];
        return $this->model->table($this->table)->alias('sob')
            ->join($this->dbprefix.'sale_order_bill_item sobi','sobi.bill_id = sob.id',"LEFT")
            ->where('sob.id',"in",$ids)
            ->field($field)
            ->select();
    }

    public function addBill($data){
        return $this->model->table($this->table)->insertGetId($data);
    }
    public function addAllBillItem($datas){
        $field = [
            'bill_id',
            'product_type',
            'bill_money',
        ];
        return $this->model->table($this->dbprefix."sale_order_bill_item")->field($field)->insertAll($datas);
    }

    //撤回
    public function retract($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 3;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    //核准
    public function approved($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 1;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    //驳回
    public function rejected($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 2;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    //作废
    public function invalid($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 6;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }

    //已领取
    public function received($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 5;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }

}