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
            ->join($this->dbprefix.'sale_order_bill_item sobi','sobi.bill_id = sob.id',"LEFT")
            ->where($map)
            ->order($order)
            ->group("sob.id")
            ->limit($offset,$num)
            ->field('sob.*,bs.bill_type as bill_type_name,GROUP_CONCAT( distinct `sobi`.`product_type`) as `product_type_name`')
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

    public function checkBillBySaleIdNot($sale_id,$status){
        if(empty($status)){
            return [];
        }
        $map['status'] = ['notin',$status];
        $map['sale_id'] = $sale_id;
        return $this->model->table($this->table)
            ->where($map)
            ->find();
    }

    public function checkBillBySaleId($sale_id,$status){
        if(empty($status)){
            return [];
        }
        $map['status'] = ['in',$status];
        $map['sale_id'] = $sale_id;
        return $this->model->table($this->table)
            ->where($map)
            ->find();
    }

    public function getBillBySaleId($sale_id,$status=null){
        if($status!=null){
            $map['status'] = $status;
        }
        $map['sale_id'] = $sale_id;
        return $this->model->table($this->table)
            ->where($map)
            ->find();
    }

    public function getBillById($id){
        return $this->model->table($this->table)
            ->where("id",$id)
            ->find();
    }

    public function setBill($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    public function addBill($data){
        return $this->model->table($this->table)->insertGetId($data);
    }
    public function addAllBillItem($datas){
        $field = [
            'bill_id',
            'product_type',
            'product_type_money',
        ];
        return $this->model->table($this->dbprefix."sale_order_bill_item")->field($field)->insertAll($datas);
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

    //撤回
    public function retractBySaleId($sale_id,$user_id=null){
        $data["status"] = 3;
        if($user_id){
            $map["employee_id"] = $user_id;
        }
        $map["sale_id"] = $sale_id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    //核准
    public function approved($id){
        $data["status"] = 4;
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
        $map["status"] = 0;
        return $this->model->table($this->dbprefix."contract")->where($map)->update($data);
    }

}