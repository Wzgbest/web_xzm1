<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\common\model;

use app\common\model\Base;
use \myvendor\TimeTools;

class Integral extends Base
{
    public function __construct($corp_id = null)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'credit_config';
        parent::__construct($corp_id);
    }

    /**
     * 检测是否超出经验次数   
     * @param  [type] $name 经验标识
     * @param  [type] $uid  用户id
     * @return [type]       [description]
     */
    public function checkIntegralNum($name,$uid){
        $info = ['status'=>false,'message'=>"不可以增加"];

        if (!$name || !$uid) {
            $info['message'] = "标识或用户id为空";
            return $info;
        }

        $map['name'] = $name;
        $creditConfig = $this->model->table($this->table)->where($map)->find();
        if ($creditConfig['max_num']) {
            $time_arr = $this->_get_times($creditConfig['way'],0,0);
            $log_map['employee_id'] = $uid;
            $log_map['create_time'] = ['between',$time_arr];
            if ($creditConfig['share']) {
                $shareConfig = $this->model->table($this->table)->where("id",$creditConfig['share'])->find();
                $log_map['credit_name'] = ['in',[$creditConfig['name'],$shareConfig['name']]];
            }else{
                $log_map['credit_name'] = $creditConfig['name'];
            }
            $num = $this->model->table($this->dbprefix."credit_log")->where($log_map)->count();
            if ($num>=$creditConfig['max_num']) {
                $info['message'] = "已经达到最大次数";
                return $info;
            }
        }else{
            $info['message'] = "最大次数为0，请设置";
            return $info;
        }

        $info['message'] = "可以添加";
        $info['status'] = true;
        $info['data'] = $num;

        return $info;
    }
    //判断是否是一天
    protected function _get_times($time,$start_time,$end_time){
        if($time&&($start_time<=0&&$end_time<=0)){
            $timetools = new TimeTools();
            $time_arr=[0,0];
            switch ($time){
                case 1:
                    $time_arr = $timetools->today();
                    break;
                case 2:
                    $time_arr = $timetools->month();
                    break;
                case 3:
                    $time_arr = $timetools->year();
                    break;
            }
            if(isset($time_arr[0])){
                $start_time = $time_arr[0];
            }
            if(isset($time_arr[1])){
                $end_time = $time_arr[1];
            }
        }
        return [$start_time,$end_time];
    }
}