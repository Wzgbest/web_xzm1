<?php
/**
 * Created by messhair.
 * Date: 2017/2/14
 */
namespace app\common\model;

use app\common\model\Base;

class EmployeeScore extends Base
{
    /**
     * @param $corp_id  公司名代号，非id
     */
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'employee_score';
        parent::__construct($corp_id);
    }

    /**
     * 获取用户积分信息
     * @param $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getEmployeeScore($userid)
    {
        return $this->model->table($this->table)
            ->alias('a')
            ->join(config('database.prefix').'employee b','a.id = b.id')
            ->where('b.id',$userid)
            ->find();
    }

    /**
     * 获取积分低于$score的占比
     * @param $score
     * @return float
     * created by messhair
     */
    public function getScoreListPer($score)
    {
        $count = $this->model->table($this->table)
            ->alias('a')
            ->join(config('database.prefix').'employee b', 'a.id = b.id', 'RIGHT')
            ->count('b.id');
        $lcount = $this->model->table($this->table)
            ->alias('a')
            ->join(config('database.prefix').'employee b', 'a.id = b.id', 'RIGHT')
            ->where('a.score','<',$score)
            ->count('b.id');
        return $lcount/$count;
    }
}