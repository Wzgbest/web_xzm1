<?php
/**
 * Created by messhair.
 * Date: 2017/5/18
 */
namespace app\common\model;

use app\common\model\Base;

class EmployerDelete extends Base
{
    public function __construct()
    {
        $this->table = config('database.prefix').'employer_delete';
        parent::__construct();
    }

    /**
     * 添加单挑删除员工信息
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