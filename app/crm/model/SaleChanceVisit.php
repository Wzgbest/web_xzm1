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

class SaleChanceVisit extends Base{
    protected $dbprefix;
    public function __construct($corp_id){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'sale_chance_visit';
        parent::__construct($corp_id);
    }

    /**根据商机ID获取客户商机拜访
     * @param $sale_id int 商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceVisitBySaleId($sale_id)
    {
        return $this->model->table($this->table)->alias('scv')
            ->where('scv.sale_id',$sale_id)
            ->find();
    }

    /**获取
     * @param $id int 客户商机拜访id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceVisit($id)
    {
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**获取最后一次成功拜访
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getLastVisitAndNum($customer_id)
    {
        $field = ["MAX(visit_time) as last_visit_time","count(scv.id) as visit_num"];
        return $this->model->table($this->table)->alias('scv')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scv.sale_id',"LEFT")
            ->where("scv.visit_ok",1)
            ->where("sc.customer_id",$customer_id)
            ->group("sc.customer_id")
            ->field($field)
            ->find();
    }

    /**添加
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function addSaleChanceVisit($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**更新
     * @param $id int 客户商机拜访id
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChanceVisit($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**更新
     * @param $sale_id int 客户商机id
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChanceVisitBySaleId($sale_id,$data)
    {
        return $this->model->table($this->table)->where('sale_id',$sale_id)->update($data);
    }

    /**更新签到数据
     * @param $customer_id int 客户id
     * @param $lat double 经度
     * @param $lng double 纬度
     * @param $sale_id double 销售机会id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function sign_in($customer_id,$lat,$lng,$sale_id=null){
        if($sale_id){
            $map["sc.id"] = $sale_id;
        }
        $data["scv.sign_in_location"] = $lat.",".$lng;
        $data["scv.visit_ok"] = 1;
        $data["sc.sale_status"] = 3;
        $map["sc.customer_id"] = $customer_id;
        $map["sc.sale_status"] = 2;
        return $this->model->table($this->table)->alias('scv')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scv.sale_id',"LEFT")
            ->where($map)
            ->update($data);
    }


    /**
     * 查询商机数排名
     * @param $start_time int 开始时间
     * @param $end_time int 结束时间
     * @param $uids array 员工id数组
     * @param $standard int 达标数量
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 客户筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getSaleChanceVisitRanking($start_time,$end_time,$uids,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["scv.create_time"][] = ["egt",$start_time];
        $map["scv.create_time"][] = ["elt",$end_time];
        $map["sc.employee_id"] = ["in",$uids];
        $map["scv.visit_ok"] = 1;//拜访成功
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $group="sc.employee_id";
        $order="num desc,standard_time asc";
        $rankingList = $this->model->table($this->table)->alias('scv')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scv.sale_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->where($map)
            ->group($group)
            ->order($order)
            //->limit($offset,$num)
            ->field("e.id as employee_id,e.telephone,e.truename,count(scv.id) num,MAX(scv.create_time) as standard_time,IF (count(scv.id) >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($rankingList,'$rankingList',1);
        if($num==1&&$page==0&&$rankingList){
            $rankingList = $rankingList[0];
        }
        return $rankingList;
    }


    /**
     * 查询商机数达标
     * @param $start_time int 开始时间
     * @param $end_time int 结束时间
     * @param $uids array 员工id数组
     * @param $standard int 标准
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 客户筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getSaleChanceVisitStandard($start_time,$end_time,$uids,$standard,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["scv.create_time"][] = ["egt",$start_time];
        $map["scv.create_time"][] = ["elt",$end_time];
        $map["sc.employee_id"] = ["in",$uids];
        $map["scv.visit_ok"] = 1;//拜访成功
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $order="sc.employee_id asc,scv.create_time asc";
        $vl_order="standard desc";
        $standard_order="is_standard desc,standard_time asc,num desc";
        $subQuery = $this->model->table($this->table)->alias('scv')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scv.sale_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit(999999)//2147483647?
            ->field("e.id as employee_id,e.telephone,e.truename,scv.create_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $subQuery = $this->model->table($subQuery)->alias('sl')
            ->join("(SELECT @prev := '', @n := 0, @st := 0) init",'sl.employee_id = sl.employee_id',"LEFT")
            ->order($vl_order)
            ->limit(999999)//2147483647?
            ->field("sl.*,@n := IF (sl.employee_id != @prev, '1', @n + 1)+0 AS standard ,@st := IF (sl.employee_id != @prev, '0', @st) AS stre ,@prev := sl.employee_id AS ng ,@st := IF (@n = $standard, sl.create_time, @st) as standard_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $standardList = $this->model->table($subQuery)->alias('v')
            ->group("employee_id")
            ->order($standard_order)
            //->limit($offset,$num)
            ->field("employee_id,telephone,truename,standard as num,standard_time,IF (standard >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($standardList,'$standardList',1);
        if($num==1&&$page==0&&$standardList){
            $standardList = $standardList[0];
        }
        return $standardList;
    }
}