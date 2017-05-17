<?php
/**
 * Created by: messhair
 * Date: 2017/5/5
 */
namespace app\common\model;

use app\common\model\Base;

class RuleEmployer extends Base
{
    public function __construct($corp_id=null)
    {
        $this->table=config('database.prefix').'rule_employer';
        parent::__construct($corp_id);
    }

    /**
     * 根据员工id查询其权限
     * @param $userid 员工id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRulesByEmployer($userid)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'rule b','a.rule_id = b.id')
            ->field('a.user_id,a.rule_id,b.rule_name,b.rule_title,b.status')
            ->where('a.user_id',$userid)
            ->select();
    }

    /**
     * 根据权限id查询员工列表
     * @param $rule_id 权限id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getEmployersByRule($rule_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'employer b','a.user_id = b.id')
            ->field('a.rule_id,a.user_id,b.truename,b.structid,b.telephone,b.gender,b.age,b.email,b.qqnum,b.wechat,b.worknum,b.is_leader,b.on_duty')
            ->where('a.rule_id',$rule_id)->select();
    }
}