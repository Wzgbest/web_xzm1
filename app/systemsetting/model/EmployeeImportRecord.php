<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

namespace app\systemsetting\model;

use app\common\model\Base;

class EmployeeImportRecord extends Base{
    protected $dbprefix;
    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'employee_import_record';
        parent::__construct($corp_id);
        $this->dbprefix = config('database.prefix');
    }

    /**
     * 获取上传记录列表
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 筛选条件
     * @param $order string 排序
     * @return int|string
     * @throws \think\Exception
     */
    public function getImportEmployeeRecord($num=10,$page=0,$filter=null,$order="eir.id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map = $this->_getMapByFilter($filter,["id","start_time","end_time","batch","operator"]);
        $importEmployeeRecordList = $this->model->table($this->table)->alias('eir')
            ->join($this->dbprefix.'employee e','eir.operator = e.id','left')
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('eir.*,e.truename')//TODO field list
            ->select();
        if($num==1&&$page==0&&$importEmployeeRecordList){
            $importEmployeeRecordList = $importEmployeeRecordList[0];
        }
        return $importEmployeeRecordList;
    }

    /**
     * 获取上传记录数量
     * @param $filter array 筛选条件
     * @return int|string
     * @throws \think\Exception
     */
    public function getImportEmployeeRecordCount($filter=null){
        $map = $this->_getMapByFilter($filter,["start_time","end_time","batch","operator"]);
        $importEmployeeRecordCount = $this->model->table($this->table)->alias('eir')
            ->join($this->dbprefix.'employee e','eir.operator = e.id','left')
            ->where($map)
            ->count();
        return $importEmployeeRecordCount;
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        //id
        if(in_array("id",$filter_column) && array_key_exists("id", $filter)){
            $map["eir.id"] = $filter["id"];
        }
        //ids
        if(in_array("ids",$filter_column) && array_key_exists("ids", $filter)){
            $map["eir.id"] = ["in",$filter["ids"]];
        }
        //开始时间
        if(in_array("start_time",$filter_column) && array_key_exists("start_time", $filter)){
            $map["eir.create_time"] = ["egt",$filter["start_time"]];
        }
        //结束时间
        if(in_array("end_time",$filter_column) && array_key_exists("end_time", $filter)){
            $map["eir.create_time"] = ["elt",$filter["end_time"]];
        }
        //批次
        if(in_array("batch",$filter_column) && array_key_exists("batch", $filter)){
            $map["eir.batch"] = ["like","%".$filter["batch"]."%"];
        }
        //导入人
        if(in_array("operator",$filter_column) && array_key_exists("operator", $filter)){
            $map["e.truename"] = ["like","%".$filter["operator"]."%"];
        }
        return $map;
    }

    /**
     * 获取一条新的空白记录(获取batch)
     * @param $uid int 操作者用户id
     * @return int|string
     */
    public function getNewImportEmployeeRecord($uid){
        $data['create_time'] = time();
        $data['operator'] = $uid;
        $data['import_result'] = 0;
        $data['success_num'] = 0;
        $data['fail_num'] = 0;
        $now_year_month  = date('Ym');
        $batch = 1;
        $this->model->startTrans();
        $last_record = $this->model->table($this->table)->order("id desc")->lock(true)->field(array("id,batch"))->find();
        if($last_record){
            $last_record_batch = $last_record['batch'];
            $Last_year_month = substr($last_record_batch,0,6);
            if($Last_year_month == $now_year_month){
                $batch = intval(substr($last_record_batch,7,4))+1;
            }
        }
        $data['batch'] = $now_year_month.str_pad($batch,4,"0",STR_PAD_LEFT);
        $record_id = $this->model->table($this->table)->insertGetId($data);
        if(!$record_id){
            $this->model->rollback();
            return false;
        }
        $this->model->commit();
        $data['id'] = $record_id;
        return $data;
    }

    /**
     * 更新上传记录
     * @param $id int 记录id
     * @param $data array 数据
     * @return int|string
     * @throws \think\Exception
     */
    public function setImportEmployeeRecord($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}