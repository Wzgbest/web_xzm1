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

class Contract extends Base
{
    protected $dbprefix;
    public function __construct($corp_id)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'contract_applied';
        parent::__construct($corp_id);
    }

    public function addContract($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function addAllContract($datas)
    {
        $field = [
            'employee_id',
            'contract_type',
            'contract_num',
            'contract_apply_1',
            'contract_apply_2',
            'contract_apply_3',
            'contract_apply_4',
            'contract_apply_5',
            'contract_apply_6',
            'contract_apply_status',
            'update_time',
            'create_time',
            'status',
        ];
        return $this->model->table($this->table)->field($field)->insertAll($datas);
    }

    public function getContract($id)
    {
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    public function setContract($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}