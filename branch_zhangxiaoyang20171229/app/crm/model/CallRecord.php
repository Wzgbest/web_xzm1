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

class CallRecord extends Base{
    protected $dbprefix;
    public function __construct($corp_id = null){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'call_record';
        parent::__construct($corp_id);
    }

    /**
     * 查询通话记录
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 客户筛选条件
     * @param $order string 排序
     * @return array|false
     * @throws \think\Exception
     */
    public function getCallRecord($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $callRecordList = $this->model->table($this->table)->alias('cr')
            ->join($this->dbprefix.'customer c','cr.customer_id = c.id',"left")
            ->join($this->dbprefix.'customer_contact cc','cr.contactor_id = cc.id',"left")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("cr.*,c.customer_name,cc.contact_name,'' as head_img_url")//TODO field list
            ->select();
        //var_exp($callRecordList,'$callRecordList',1);
        if($num==1&&$page==0&&$callRecordList){
            $callRecordList = $callRecordList[0];
        }
        return $callRecordList;
    }


    /**
     * 查询通话记录量排名
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
    public function getCallRecordRanking($start_time,$end_time,$uids,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["cr.begin_time"][] = ["egt",$start_time];
        $map["cr.begin_time"][] = ["elt",$end_time];
        $map["cr.userid"] = ["in",$uids];
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $group="cr.userid";
        $order="num desc,standard_time asc";
        $callRecordRanking = $this->model->table($this->table)->alias('cr')
            ->join($this->dbprefix.'employee e','cr.userid = e.id',"LEFT")
            ->where($map)
            ->group($group)
            ->order($order)
            //->limit($offset,$num)
            ->field("e.id as employee_id,e.telephone,e.truename,count(cr.id) num,MAX(cr.begin_time) as standard_time,IF (count(cr.id) >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($callRecordRanking,'$callRecordRanking',1);
        if($num==1&&$page==0&&$callRecordRanking){
            $callRecordRanking = $callRecordRanking[0];
        }
        return $callRecordRanking;
    }


    /**
     * 查询通话记录量达标次序
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
    public function getCallRecordStandard($start_time,$end_time,$uids,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $map["cr.begin_time"][] = ["egt",$start_time];
        $map["cr.begin_time"][] = ["elt",$end_time];
        $map["cr.userid"] = ["in",$uids];
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $order="cr.userid asc,cr.id asc";
        $sl_order="standard*1 desc";
        $standard_order="is_standard desc,standard_time asc,num desc";
        $subQuery = $this->model->table($this->table)->alias('cr')
            ->join($this->dbprefix.'employee e','cr.userid = e.id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit(999999)//2147483647?
            ->field("e.id as employee_id,e.telephone,e.truename,cr.begin_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $subQuery = $this->model->table($subQuery)->alias('cl')
            ->join("(SELECT @prev := '', @n := 0, @st := 0) init",'cl.employee_id = cl.employee_id',"LEFT")
            ->order($sl_order)
            ->limit(999999)//2147483647?
            ->field("cl.*,@n := IF (cl.employee_id != @prev, '1', @n + 1)+0 AS standard ,@st := IF (cl.employee_id != @prev, '0', @st) AS stre ,@prev := cl.employee_id AS ng ,@st := IF (@n = $standard, cl.begin_time, @st) as standard_time")
            ->buildSql();
        //var_exp($subQuery,'$subQuery',1);
        $callRecordRanking = $this->model->table($subQuery)->alias('v')
            ->group("employee_id")
            ->order($standard_order)
            //->limit($offset,$num)
            ->field("employee_id,telephone,truename,standard as num,standard_time,IF (standard >= $standard, '1', '0') as is_standard")
            ->select();
        //var_exp($callRecordRanking,'$callRecordRanking',1);
        if($num==1&&$page==0&&$callRecordRanking){
            $callRecordRanking = $callRecordRanking[0];
        }
        return $callRecordRanking;
    }

    /**
     * 查询最后通话时间和通话数量
     * @param $customer_id int 客户id
     * @return array|false
     * @throws \think\Exception
     */
    public function getLastCallRecordAndNum($customer_id){
        $field = [
            "MAX(begin_time) as last_call_time",
            "sum(case when cr.call_direction=1 then 1 else 0 end) as call_out_num",
            "sum(case when cr.call_direction=2 then 1 else 0 end) as call_in_num"
        ];
        return $this->model->table($this->table)->alias('cr')
            ->where("cr.customer_id",$customer_id)
            ->group("cr.customer_id")
            ->field($field)
            //->fetchSql(true)
            ->find();
    }
    /**
     * 查询通话记录
     * @param $phoneRecId string TQ通话唯一id
     * @return array|false
     * @throws \think\Exception
     */
    public function getCallRecordByPhoneRecId($phoneRecId){
        $map["phonerecid"] = $phoneRecId;
        $callRecordList = $this->model->table($this->table)->alias('cr')
            ->where($map)
            ->field("cr.*")//TODO field list
            ->select();
        //var_exp($callRecordList,'$callRecordList',1);
        return $callRecordList;
    }

    public function addCallRecord($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function addCallRecordList($data_list){
        return $this->model->table($this->table)->insertAll($data_list);
    }
}