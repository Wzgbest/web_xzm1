<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\datacount\model;

use app\common\model\Base;

class Datacount extends Base{
    protected $dbprefix;
    public function __construct($corp_id = null){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'datacount';
        parent::__construct($corp_id);
    }

    public function getDataCount($uids,$start_time,$end_time){
        if(empty($uids)){
            return [];
        }
        $map["uid"] = ["in",$uids];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $group = "type";
        $result_data = $this->model->table($this->table)->alias('d')
            ->where($map)
            ->group($group)
//            ->fetchSql(true)
            ->column("count(id) num,sum(num) sum_num,sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num,sum(case when type = 1 and num > 30 then num else 0 end) tag_sum","type");
        return $result_data;
    }

    public function getEmployeeDataCount($uids,$start_time,$end_time){
        if(empty($uids)){
            return [];
        }
        $map["uid"] = ["in",$uids];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $group = "uid,type";
        $result_data = $this->model->table($this->table)->alias('d')
            ->where($map)
            ->group($group)
//            ->fetchSql(true)
            ->field("uid,type,count(id) num,sum(num) sum_num,sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num,sum(case when type = 1 and num > 30 then num else 0 end) tag_sum")
            ->select();
        return $result_data;
    }

    public function getTypeDataCount($type,$uids,$start_time,$end_time){
        if(empty($uids)){
            return [];
        }
        $map["type"] = ["eq",$type];
        $map["uid"] = ["in",$uids];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $group = "type";
        $result_data = $this->model->table($this->table)->alias('d')
            ->where($map)
            ->group($group)
//            ->fetchSql(true)
            ->column("count(id) num,sum(num) sum_num,sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num,sum(case when type = 1 and num > 30 then num else 0 end) tag_sum","type");
        return $result_data;
    }

    public function getTypeEmployeeDataCount($type,$uids,$start_time,$end_time,$order="num"){
        if(empty($uids)){
            return [];
        }
        $map["type"] = ["eq",$type];
        $map["uid"] = ["in",$uids];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $group="uid";
        $field = [
            "type",
            "uid flg",
            "e.truename flg_name",
            "e.userpic flg_img",
            "count(d.id) num",
            "sum(num) sum_num",
            "sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num",
            "sum(case when type = 1 and num > 30 then num else 0 end) tag_sum"
        ];
        $result_data = $this->model->table($this->table)->alias('d')
            ->join($this->dbprefix.'employee e','d.uid = e.id',"left")
            ->where($map)
            ->group($group)
//            ->fetchSql(true)
            ->field($field)
            ->order($order)
            ->select();
        return $result_data;
    }

    public function getTypeStructDataCount($type,$structs,$start_time,$end_time,$order="num"){
        if(empty($structs)){
            return [];
        }
        $map["type"] = ["eq",$type];
        $map["struct_id"] = ["in",$structs];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $group="struct_id";
        $field = [
            "type",
            "struct_id flg",
            "s.struct_name flg_name",
            "'' flg_img",
            "count(d.id) num",
            "sum(num) sum_num",
            "sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num",
            "sum(case when type = 1 and num > 30 then num else 0 end) tag_sum"
        ];
        $result_data = $this->model->table($this->table)->alias('d')
            ->join($this->dbprefix."structure_employee se","se.user_id = d.uid","left")
            ->join($this->dbprefix.'structure s','se.struct_id = s.id',"left")
            ->where($map)
            ->group($group)
//            ->fetchSql(true)
            ->field($field)
            ->order($order)
            ->select();
        return $result_data;
    }

    public function getDatacountMonth($uids,$start_time,$end_time){
        if(empty($uids)){
            return [];
        }
        $map["uid"] = ["in",$uids];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $mouth = "FROM_UNIXTIME(time,'%Y-%m')";
        $group = "type,".$mouth;
        $order = "time asc";
        $result_data = $this->model->table($this->table)->alias('d')
            ->where($map)
            ->group($group)
            ->order($order)
//            ->fetchSql(true)
            ->column($mouth." `group_flg`,count(id) num,sum(num) sum_num,sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num,sum(case when type = 1 and num > 30 then num else 0 end) tag_sum","type");
        return $result_data;
    }

    public function getDatacountSeason($uids,$start_time,$end_time){
        if(empty($uids)){
            return [];
        }
        $map["uid"] = ["in",$uids];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $season = "CONCAT(FROM_UNIXTIME(time,'%Y'),'Q',QUARTER(FROM_UNIXTIME(time)))";
        $group = "type,".$season;
        $order = "time asc";
        $result_data = $this->model->table($this->table)->alias('d')
            ->where($map)
            ->group($group)
            ->order($order)
//            ->fetchSql(true)
            ->column($season." `group_flg`,count(id) num,sum(num) sum_num,sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num,sum(case when type = 1 and num > 30 then num else 0 end) tag_sum","type");
        return $result_data;
    }

    public function getDatacountYear($uids,$start_time,$end_time){
        if(empty($uids)){
            return [];
        }
        $map["uid"] = ["in",$uids];
        $map["time"] = [
            ["egt",$start_time],
            ["elt",$end_time]
        ];
        $year = "FROM_UNIXTIME(time,'%Y')";
        $group = "type,".$year;
        $order = "time asc";
        $result_data = $this->model->table($this->table)->alias('d')
            ->where($map)
            ->group($group)
            ->order($order)
//            ->fetchSql(true)
            ->column($year." `group_flg`,count(id) num,sum(num) sum_num,sum(case when type = 1 and num > 30 then 1 else 0 end) tag_num,sum(case when type = 1 and num > 30 then num else 0 end) tag_sum","type");
        return $result_data;
    }

    public function getDatacountByLinkIdAndType($type,$link_id,$lock=false){
        $map["type"] = $type;
        $map["link_id"] = $link_id;
        return $this->model->table($this->table)
            ->where($map)
            ->lock($lock)
            ->find();
    }

    public function getDatacountByLinkIdAndTypeCount($type,$link_id,$lock=false){
        $map["type"] = $type;
        $map["link_id"] = $link_id;
        return $this->model->table($this->table)
            ->where($map)
            ->lock($lock)
            ->count();
    }

    public function addDatacount($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function addDatacountList($data_list){
        return $this->model->table($this->table)->insertAll($data_list);
    }

    public function delDatacountByLinkIdAndType($type,$link_id){
        $map["type"] = $type;
        $map["link_id"] = $link_id;
        return $this->model->table($this->table)
            ->where($map)
            ->delete();
    }
}