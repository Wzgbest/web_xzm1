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
     * @param $map array 筛选条件
     * @param $order string 排序
     * @return int|string
     * @throws \think\Exception
     */
    public function getImportEmployeeRecord($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $importEmployeeRecordList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('*')//TODO field list
            ->select();
        if($num==1&&$page==0&&$importEmployeeRecordList){
            $importEmployeeRecordList = $importEmployeeRecordList[0];
        }
        return $importEmployeeRecordList;
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