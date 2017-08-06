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
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $field = [
            'sob.*',
            'e.truename as operator_name',
            'bs.bill_type as bill_type_name',
            'GROUP_CONCAT( distinct `sobi`.`product_type`) as `product_type_name`',
            "(case when sob.status = 0 then 1 
                when sob.status = 2 then 3 
                when sob.status = 3 then 4 
                when sob.status = 6 then 5 
                when sob.status = 6 then 5 
                else 2 end ) as in_column",
        ];

        $billList = $this->model->table($this->table)->alias('sob')
            ->join($this->dbprefix.'employee e','sob.operator = e.id',"LEFT")
            ->join($this->dbprefix.'bill_setting bs','bs.id = sob.bill_type',"LEFT")
            ->join($this->dbprefix.'sale_order_bill_item sobi','sobi.bill_id = sob.id',"LEFT")
            ->where($map)
            ->group("sob.id")
            ->order($order)
            ->having($having)
            ->limit($offset,$num)
            ->field($field)
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
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

        $field = [
            "(case when sob.status = 0 then 1 
                when sob.status = 2 then 3 
                when sob.status = 3 then 4 
                when sob.status = 6 then 5 
                when sob.status = 6 then 5 
                else 2 end ) as in_column",
        ];

        $billCount= $this->model->table($this->table)->alias('sob')
            ->where($map)
            ->field($field)
            ->group("sob.id")
            ->having($having)
            ->count();
        return $billCount;
    }

    /**
     * 查询列上的数量
     * @param $uid int 员工id
     * @param $filter array 过滤条件
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function getColumnNum($uid,$filter=null){

        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        $field = [
            "(case when sob.status = 0 then 1 
                when sob.status = 2 then 3 
                when sob.status = 3 then 4 
                when sob.status = 6 then 5 
                when sob.status = 6 then 5 
                else 2 end ) as in_column",
        ];
        $getCountField = [
            "(case when in_column = 1 then 1 else 0 end) as `1`",
            "(case when in_column = 2 then 1 else 0 end) as `2`",
            "(case when in_column = 3 then 1 else 0 end) as `3`",
            "(case when in_column = 4 then 1 else 0 end) as `4`",
            "(case when in_column = 5 then 1 else 0 end) as `5`",
        ];
        $countField = [
            "count(*) as `0`",
            "sum(`1`) as `1`",
            "sum(`2`) as `2`",
            "sum(`3`) as `3`",
            "sum(`4`) as `4`",
            "sum(`5`) as `5`",
        ];

        $customerQuery = $this->model->table($this->table)->alias('sob')
            ->where($map)
            ->group("sob.id")
            ->field($field)
            ->buildSql();
        //var_exp($contractAppliedList,'$contractAppliedList',1);
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
        return $listCount;
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
    public function getVerificationBill($num=10,$page=0,$filter=null,$field=null,$order="sob.id",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,[]);
        $map["sob.status"] = ["neq","3"];
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $field = [
            'sob.*',
            'e.truename as operator_name',
            'bs.bill_type as bill_type_name',
            'GROUP_CONCAT( distinct `sobi`.`product_type`) as `product_type_name`',
            "(case when sob.status = 0 then 1 
                when sob.status = 4 then 2 
                when sob.status = 5 then 3 
                when sob.status = 2 then 4 
                when sob.status = 9 then 5 
                else 6 end ) as in_column",
        ];

        $billList = $this->model->table($this->table)->alias('sob')
            ->join($this->dbprefix.'employee e','sob.operator = e.id',"LEFT")
            ->join($this->dbprefix.'bill_setting bs','bs.id = sob.bill_type',"LEFT")
            ->join($this->dbprefix.'sale_order_bill_item sobi','sobi.bill_id = sob.id',"LEFT")
            ->where($map)
            ->group("sob.id")
            ->order($order)
            ->having($having)
            ->limit($offset,$num)
            ->field($field)
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
    public function getVerificationBillCount($filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,[]);
        $map["sob.status"] = ["neq","3"];
        $having = null;
        if(array_key_exists("in_column", $filter)){
            $in_column = $filter["in_column"];
            if($in_column>0){
                $having = " in_column = $in_column ";
            }
        }

        $field = [
            "(case when sob.status = 0 then 1 
                when sob.status = 4 then 2 
                when sob.status = 5 then 3 
                when sob.status = 2 then 4 
                when sob.status = 9 then 5 
                else 6 end ) as in_column",
        ];

        $billCount= $this->model->table($this->table)->alias('sob')
            ->where($map)
            ->field($field)
            ->group("sob.id")
            ->having($having)
            ->count();
        return $billCount;
    }

    /**
     * 查询列上的数量
     * @param $uid int 员工id
     * @param $filter array 过滤条件
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function getVerificationColumnNum($uid,$filter=null){

        //筛选
        $map = $this->_getMapByFilter($filter,[]);
        $map["sob.status"] = ["neq","3"];

        $field = [
            "(case when sob.status = 0 then 1 
                when sob.status = 4 then 2 
                when sob.status = 5 then 3 
                when sob.status = 2 then 4 
                when sob.status = 9 then 5 
                else 6 end ) as in_column",
        ];
        $getCountField = [
            "(case when in_column = 1 then 1 else 0 end) as `1`",
            "(case when in_column = 2 then 1 else 0 end) as `2`",
            "(case when in_column = 3 then 1 else 0 end) as `3`",
            "(case when in_column = 4 then 1 else 0 end) as `4`",
            "(case when in_column = 5 then 1 else 0 end) as `5`",
        ];
        $countField = [
            "count(*) as `0`",
            "sum(`1`) as `1`",
            "sum(`2`) as `2`",
            "sum(`3`) as `3`",
            "sum(`4`) as `4`",
            "sum(`5`) as `5`",
        ];

        $customerQuery = $this->model->table($this->table)->alias('sob')
            ->where($map)
            ->group("sob.id")
            ->field($field)
            ->buildSql();
        //var_exp($contractAppliedList,'$contractAppliedList',1);
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
        return $listCount;
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
            ->order("id desc")
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

    public function setBill($id,$data,$map=null){
        return $this->model->table($this->table)->where('id',$id)->where($map)->update($data);
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
            $map["operator"] = $user_id;
        }
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    //撤回
    public function retractBySaleId($sale_id,$user_id=null){
        $data["status"] = 3;
        if($user_id){
            $map["operator"] = $user_id;
        }
        $map["sale_id"] = $sale_id;
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