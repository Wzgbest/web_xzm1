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
            ->column("sum(num) all_num,sum(case when type = 1 and num > 30 then num else 0 end) tag_num","type");
        return $result_data;
    }

    public function addDatacount($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function addDatacountList($data_list){
        return $this->model->table($this->table)->insertAll($data_list);
    }
}