<?php
namespace app\common\model;

use app\common\model\Base;

class Umessage extends Base
{
    public function __construct($corp_id)
    {
        $this->table=config('database.prefix').'umessage';
        parent::__construct($corp_id);
    }

    /**
     * 记录操作
     * @param $data
     * @return int|string
     */
    public function addUmessage($data)
    {
        return $this->model->table($this->table)->insert($data);
    }
}