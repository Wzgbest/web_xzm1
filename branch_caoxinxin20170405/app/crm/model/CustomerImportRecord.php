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

class CustomerImportRecord extends Base{
    protected $dbprefix;
    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'customer_import_record';
        parent::__construct($corp_id);
        $this->dbprefix = config('database.prefix');
    }

    /**
     * 获取上传记录
     * @param $id 记录id
     * @return int|string
     * @throws \think\Exception
     */
    public function getImportCustomerRecord($id)
    {
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**
     * 获取一条新的空白记录(获取batch)
     * @param $data
     * @return int|string
     */
    public function getNewImportCustomerRecord($uid){
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
     * @param $id 记录id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setImportCustomerRecord($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}