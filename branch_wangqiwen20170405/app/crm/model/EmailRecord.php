<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;

class EmailRecord extends Base
{
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'email_record';
        parent::__construct($corp_id);
    }

    /**
     * 按分类查找邮件
     * @param $userid 员工id
     * @param $type 邮件类型 0草稿箱，1发件箱，-1回收站，-2彻底删除
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllEmailRecordsByUserid($userid,$type)
    {
        return $this->model->table($this->table)
            ->where('from_userid',$userid)
            ->where('status',$type)
            ->select();
    }

    public function addEmail($data)
    {
        return $this->model->table($this->table)->insert($data);
    }
}