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

class SaleChanceSignIn extends Base{
    protected $dbprefix;
    public function __construct($corp_id){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'sale_chance_sign_in';
        parent::__construct($corp_id);
    }

    /**根据商机ID获取客户商机拜访
     * @param $sale_id int 商机id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceSignInBySaleId($sale_id){
        return $this->model->table($this->table)->alias('scsi')
            ->where('scsi.sale_id',$sale_id)
            ->find();
    }

    /**获取
     * @param $id int 客户商机拜访id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getSaleChanceSignIn($id){
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**获取最后一次上门拜访
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getLastSignInAndNum($customer_id){
        $field = ["MAX(sign_in_time) as last_sign_in_time","count(scsi.id) as sign_in_num"];
        return $this->model->table($this->table)->alias('scsi')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scsi.sale_id',"LEFT")
            ->where("scsi.sign_in_ok",1)
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
    public function addSaleChanceSignIn($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**更新
     * @param $id int 客户商机拜访id
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChanceSignIn($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**更新
     * @param $sale_id int 客户商机id
     * @param $data array 客户商机拜访数据
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function setSaleChanceSignInBySaleId($sale_id,$data){
        return $this->model->table($this->table)->where('sale_id',$sale_id)->update($data);
    }

    /**获取待上门拜访签到
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllSignInWait(){
        $map["scsi.sign_in_ok"] = 0;
        $map['bfiln.item_id'] = 3;
        $field = [
            "sc.id as sale_id",
            "sc.sale_name",
            "c.id as customer_id",
            "c.customer_name",
        ];
        $Sale_visit_list = $this->model->table($this->table)->alias('scsi')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scsi.sale_id',"LEFT")
            ->join($this->dbprefix.'customer c','sc.customer_id = c.id',"LEFT")
            ->join($this->dbprefix.'business_flow_item_link bfil','bfil.setting_id = sc.business_id and sc.sale_status=bfil.item_id',"LEFT")
            ->join($this->dbprefix.'business_flow_item_link bfiln','bfiln.setting_id = sc.business_id and bfiln.order_num = bfil.order_num+1 and bfiln.item_id=3',"LEFT")
            ->where($map)
            ->group("sc.id")
            ->field($field)
            ->select();
        return $Sale_visit_list;
    }

    /**更新签到数据
     * @param $customer_id int 客户id
     * @param $lat double 经度
     * @param $lng double 纬度
     * @param $location_str string 经纬度对应地址
     * @param $sale_id double 销售机会id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function sign_in($customer_id,$time,$lat,$lng,$location_str,$sale_id=null){
        $map = [];
        if($sale_id){
            $map["sc.id"] = $sale_id;
        }
        $map["sc.customer_id"] = $customer_id;
        //$map["sc.sale_status"] = 2;
        $data["scsi.sign_in_time"] = $time;
        $data["scsi.sign_in_location"] = $lat.",".$lng;
        $data["scsi.sign_in_place"] = $location_str;
        $data["scsi.sign_in_ok"] = 1;
        //$data["sc.sale_status"] = 3;
        return $this->model->table($this->table)->alias('scsi')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scsi.sale_id',"LEFT")
            ->where($map)
            //->fetchSql(true)
            ->update($data);
    }

    public function changeToSignInStatus($customer_id,$sale_ids=null){
        if($sale_ids){
            $map['sc.id'] = ["in",$sale_ids];
        }
        $map['sc.customer_id'] = $customer_id;
        $map['bfiln.item_id'] = 3;
        $data["sc.sale_status"] = ["exp","bfiln.item_id"];
        return $this->model->table($this->table)->alias('scsi')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scsi.sale_id',"LEFT")
            ->join($this->dbprefix.'business_flow_item_link bfil','bfil.setting_id = sc.business_id and sc.sale_status=bfil.item_id',"LEFT")
            ->join($this->dbprefix.'business_flow_item_link bfiln','bfiln.setting_id = sc.business_id and bfiln.order_num = bfil.order_num+1 and bfiln.item_id=3',"LEFT")
            ->where($map)
            //->fetchSql(true)
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
    public function getSaleChanceSignInRanking($start_time,$end_time,$uids,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["scsi.sign_in_time"][] = ["egt",$start_time];
        $map["scsi.sign_in_time"][] = ["elt",$end_time];
        $map["sc.employee_id"] = ["in",$uids];
        $map["scsi.sign_in_ok"] = 1;//拜访成功
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $group="sc.employee_id";
        $order="num desc,standard_time asc";
        $rankingList = $this->model->table($this->table)->alias('scsi')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scsi.sale_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->where($map)
            ->group($group)
            ->order($order)
            //->limit($offset,$num)
            ->field("e.id as employee_id,e.telephone,e.truename,count(scsi.id) num,MAX(scsi.sign_in_time) as standard_time,IF (count(scsi.id) >= $standard, '1', '0') as is_standard")
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
    public function getSaleChanceSignInStandard($start_time,$end_time,$uids,$standard,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["scsi.sign_in_time"][] = ["egt",$start_time];
        $map["scsi.sign_in_time"][] = ["elt",$end_time];
        $map["sc.employee_id"] = ["in",$uids];
        $map["scsi.sign_in_ok"] = 1;//拜访成功
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $order="sc.employee_id asc,scsi.sign_in_time asc";
        $vl_order="standard desc";
        $standard_order="is_standard desc,standard_time asc,num desc";
        $subQuery = $this->model->table($this->table)->alias('scsi')
            ->join($this->dbprefix.'sale_chance sc','sc.id = scsi.sale_id',"LEFT")
            ->join($this->dbprefix.'employee e','sc.employee_id = e.id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit(999999)//2147483647?
            ->field("e.id as employee_id,e.telephone,e.truename,scsi.sign_in_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $subQuery = $this->model->table($subQuery)->alias('sl')
            ->join("(SELECT @prev := '', @n := 0, @st := 0) init",'sl.employee_id = sl.employee_id',"LEFT")
            ->order($vl_order)
            ->limit(999999)//2147483647?
            ->field("sl.*,@n := IF (sl.employee_id != @prev, '1', @n + 1)+0 AS standard ,@st := IF (sl.employee_id != @prev, '0', @st) AS stre ,@prev := sl.employee_id AS ng ,@st := IF (@n = $standard, sl.sign_in_time, @st) as standard_time")
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