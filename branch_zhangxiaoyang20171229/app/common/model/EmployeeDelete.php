<?php
/**
 * Created by messhair.
 * Date: 2017/5/18
 */
namespace app\common\model;

use app\common\model\Base;

class EmployeeDelete extends Base
{
    public function __construct($corp_id=null){
        $this->table = config('database.prefix').'employee_delete';
        parent::__construct($corp_id);
    }

    /**
     * 添加单条删除员工信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addSingleBackupInfo($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    /**
     * 添加多条删除员工信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addMultipleBackupInfo($data)
    {
        return $this->model->table($this->table)->insertAll($data);
    }
}