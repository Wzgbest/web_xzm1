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
     * @param $corp_id string 公司名代号，非id
     */
    public function __construct($corp_id=null){
        $this->table=config('database.prefix').'employee_score';
        parent::__construct($corp_id);
    }

    /**
     * 获取用户积分信息
     * @param $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getEmployeeScore($userid){
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'employee b','a.id = b.id')
            ->where('b.id',$userid)
            ->find();
    }

    /**
     * 获取积分配置
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getExperienceConfig(){
        $experienceConfig = $this->model->table(config('database.prefix').'employee_experience_config')->alias('esc')
            ->column("experience,title","id");
        return $experienceConfig;
    }

    /**
     * 获取积分配置
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getPhoneLevel(){
        $experienceConfig = $this->model->table(config('database.prefix').'employee_phone_level')->alias('epl')
            ->column("phone_time,title","id");
        return $experienceConfig;
    }
}