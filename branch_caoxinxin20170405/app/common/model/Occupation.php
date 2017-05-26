<?php
/**
 * Created by messhair.
 * Date: 2017/2/14
 */
namespace app\common\model;

use app\common\model\Base;

class Occupation extends Base
{
    /**
     * @param $corp_id 公司名代号，非id
     * created by messhair
     */
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'occupation';
        parent::__construct($corp_id);
    }

    /**
     * 根据用户id字段查询职位
     * @param $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getOccupation($userid)
    {
        return $this->model->table($this->table)->alias('a')->join('guguo_employer b','a.id = b.occupation')->where('b.id',$userid)->find();
    }

    /**
     * 返回公司所有职位
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllOccupations()
    {
        return $this->model->table($this->table)->select();
    }
}