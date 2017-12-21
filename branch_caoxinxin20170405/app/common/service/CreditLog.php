<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18
 * Time: 11:12
 */
namespace app\common\service;
use app\common\model\Base;
use app\common\model\EmployeeScore;


class CreditLog extends Base
{
    public function __construct($corp_id=null){
        $this->table=config('database.prefix').'credit_log';
        parent::__construct($corp_id);
    }

    /**
     * @param $uid
     * @param $credit_name 积分名称
     * @param array $data
     * data数组可传入的索引值分别为 link_id(相关id) remark(备注) create_employee(添加人)
     * @return mixed
     * 增加积分公共方法
     */
    public function credit_increase($uid,$credit_name,$data=array()){
        $redata['status'] = 0;//初始
        $employee_score_model = new EmployeeScore($this->corp_id);
        $employee_score_record = $employee_score_model->getEmployeeScore($uid);

        //当前用户的经验值和积分
        if($employee_score_record){
            $pre_experience = $employee_score_record['experience'];
            $pre_score = $employee_score_record['score'];
            $pre_level = $employee_score_record['level'];
            $flag=0;//编辑
        }else{
            $flag=1;//新增
            $pre_experience = 0;
            $pre_score = 0;
            $pre_level = 1;
        }
        if($pre_experience>=config('experience.max')){
            $redata['info'] = '经验已满，请联系管理员进行设置';
            return $redata;
        }

        $map['name'] = $credit_name;
        $config_experience = $employee_score_model->getExperienceAndScore($map);//积分配置获取相应的分数和经验值
        if(!$config_experience){
            $redata['info'] = '积分配置文件没有对应积分和经验';
            return $redata;
        }
        //需要增加的经验值和积分
        $add_experience = $config_experience[$credit_name]['experience'];
        $add_score = $config_experience[$credit_name]['score'];
        $data['employee_id'] = $uid;//积分所属人
        $data['experience'] =  $add_experience;
        $data['score'] =  $add_score;
        $data['create_time'] = time();

        //增加后的数据
        $now_experience = $pre_experience+$add_experience;
        $now_score = $pre_score+$add_score;
        //当前的等级
        $now_level = getExperienceLevel($now_experience);
//        return  $now_level;
        $score_data['id'] = $uid;
        if($now_level != $pre_level){
            $level_config = $employee_score_model-> getLevelConfig($now_level);
            $score_data['title'] = $level_config[$now_level]['title'];//称号
            $score_data['level'] = $now_level;//当前等级
            $score_data['experience_min'] = $level_config[$now_level]['experience_start'];//当前等级经验开始值
            $score_data['experience_max'] = $level_config[$now_level]['experience_end'];//当前等级经验结束值
        }
        $score_data['score'] = $now_score;//当前分值
        $score_data['experience'] = $now_experience;//当前经验值
        //to do 未达到上限可以新增积分和经验
        $employee_score_model->link->startTrans();
        $result1 = $employee_score_model->setEmployeeScore($flag,$score_data);

        $result2= $employee_score_model->addCreditLog($data);
        if($result1!==false && $result2){
            $redata['status'] = true;
            $employee_score_model->link->commit();
        }else{
            $redata['status'] = false;//需要增加积分和经验 操作失败
            $employee_score_model->link->rollback();
        }
        return $redata;
    }
    /**
     * @param $uid
     * @param $credit_name(积分名称)
     * @param array $data
     * data数组可传入的索引值分别为 link_id(相关id) remark(备注) create_employee(添加人)
     * @return mixed
     * 减去积分经验公共方法
     */
    public function credit_decrease($uid,$credit_name,$data=array()){
        $redata['status'] = 0;//初始
        $employee_score_model = new EmployeeScore($this->corp_id);
        $employee_score_record = $employee_score_model->getEmployeeScore($uid);

        //当前用户的经验值和积分
        if($employee_score_record){
            $pre_experience = $employee_score_record['experience'];
            $pre_score = $employee_score_record['score'];
            $pre_level = $employee_score_record['level'];
            $flag=0;//编辑
        }else{
            $redata['info'] = '不存在要减少积分的用户';
            return $redata;
        }

        $map['name'] = $credit_name;
        $config_experience = $employee_score_model->getExperienceAndScore($map);//积分配置获取相应的分数和经验值
        if(!$config_experience){
            $redata['info'] = '积分配置文件没有对应积分和经验';
            return $redata;
        }
        //需要减少的经验值和积分
        $dif_experience = '-'.$config_experience[$credit_name]['experience'];
        $dif_score = '-'.$config_experience[$credit_name]['score'];
        $data['employee_id'] = $uid;//积分所属人
        $data['experience'] =  $dif_experience;
        $data['score'] =  $dif_score;
        $data['create_time'] = time();
        //变化后的数据
        $now_experience = $pre_experience-$dif_experience;
        $now_score = $pre_score-$dif_score;
        //当前的等级
        $now_level = getExperienceLevel($now_experience);
//        return  $now_level;
        $score_data['id'] = $uid;
        if($now_level != $pre_level){
            $level_config = $employee_score_model-> getLevelConfig($now_level);
            $score_data['title'] = $level_config[$now_level]['title'];//称号
            $score_data['level'] = $now_level;//当前等级
            $score_data['experience_min'] = $level_config[$now_level]['experience_start'];//当前等级经验开始值
            $score_data['experience_max'] = $level_config[$now_level]['experience_end'];//当前等级经验结束值
        }
        $score_data['score'] = $now_score;//当前分值
        $score_data['experience'] = $now_experience;//当前经验值
        //to do 未达到上限可以新增积分和经验
        $employee_score_model->link->startTrans();
        $result1 = $employee_score_model->setEmployeeScore($flag,$score_data);

        $result2= $employee_score_model->addCreditLog($data);
        if($result1!==false && $result2){
            $redata['status'] = true;
            $employee_score_model->link->commit();
        }else{
            $redata['status'] = false;//需要增加积分和经验 操作失败
            $employee_score_model->link->rollback();
        }
        return $redata;
    }
}