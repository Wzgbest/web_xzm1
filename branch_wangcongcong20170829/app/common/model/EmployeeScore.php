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

    /**
     * @param $level
     * @return array
     * 通过等级获取等级经验的区间（开始值和结束值）
     */
    public function getLevelConfig($level){
        $experienceConfig = $this->model->table(config('database.prefix').'employee_experience_config')->alias('eec')->
        where('id',$level)->column("experience as experience_end,experience_start,title",'id');
        return $experienceConfig;
    }

    /**
     * @param $map
     * @return array
     * 获取积分项目相应的积分和经验值
     */
    public function getExperienceAndScore($map){
        $experienceConfig = $this->model->table(config('database.prefix').'credit_config')->alias('cl')->
            where($map)->column("experience,score",'name');
        return $experienceConfig;
    }

    /**
     * @param $data
     * @return int|string
     * 积分变更记录
     */
    public function addCreditLog($data){
        return $this->model->table(config('database.prefix').'credit_log')->insertGetId($data);
    }

    /**
     * @param $con
     * @param $data
     * @return int|string
     * 员工积分变更
     */
    public function  setEmployeeScore($flag,$data){
        if($flag == 1){
            //新增
            return $this->model->table($this->table)->insertGetId($data);

        }else{
            //编辑
            return $this->model->table($this->table)
                ->where($data['id'])
                //->fetchSql(true)
                ->update($data);
        }

    }
}