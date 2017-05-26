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

class EmployerImportFail extends Base{
    protected $dbprefix;
    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'employer_import_fail';
        parent::__construct($corp_id);
        $this->dbprefix = config('database.prefix');
    }

    /**
     * 按批次查询导入失败的员工信息
     * @param $batch 批次
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getEmployerByBatch($batch)
    {
        return $this->model->table($this->table)
            ->where('batch',$batch)
            ->select();
    }

    /**
     * 添加导入失败的记录
     * @param $data
     * @return int|string
     */
    public function addImportEmployerFail($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 添加多条导入失败的记录
     * @param array $data
     * @return int|string
     */
    public function addMutipleImportEmployerFail($data){
        return $this->model->table($this->table)->insertAll($data);
    }
}