<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\datacount\controller;

use app\common\controller\Initialize;
use app\common\model\StructureEmployee;
use app\common\model\EmployeeScore;
use app\datacount\model\Datacount;
use \myvendor\TimeTools;

class Index extends Initialize{
    public function __construct(){
        parent::__construct();
    }
    public function index(){
        return 'Index/index';
    }
    public function test(){
        return view();
    }
    public function summary(){
        $userinfo = get_userinfo();
        if (empty($userinfo)) {
            $this->redirect('/login/index/index');
        }
//        var_exp($userinfo,'$userinfo');
        $this->assign("userinfo",$userinfo);
        $role_arr = explode(",",$userinfo["role"]);
//        var_exp($role_arr,'$role_arr');
        $role_last = array_pop($role_arr);
        $role_last = $role_last?:"";
        $this->assign("role",$role_last);
        $structureEmployeeModel = new StructureEmployee($this->corp_id);
        $structure = $structureEmployeeModel->findEmployeeStructure($this->uid);
//        var_exp($structure,'$structure');
        $this->assign("structure",$structure);
        //获取用户积分
        $scoreM = new EmployeeScore($this->corp_id);
        $score=$scoreM->getEmployeeScore($this->uid);
//        var_exp($score,'$score');
        if(!$score){
            $start = config('experience.start');
            $score = [
                "score"=>0,
                "experience"=>0,
                "title"=>"",
                "level"=>"1",
                "experience_min"=>"0",
                "experience_max"=>$start,
                "phone_time"=>0
            ];
        }
        //积分占比
        $per = round(($score["experience"]-$score["experience_min"])/($score["experience_max"]-$score["experience_min"])*100);
        if($per>100){
            $per = 100;
        }
//        var_exp($per,'$per');
        $this->assign("score",$score);
        $this->assign("score_per",$per);

        $type = 0;
        $time = 0;
        $data_count = $this->_get_data_count($type,$time);
        if($data_count["status"]!=1){
            $result['status'] = $data_count["status"];
            $result['info'] = $data_count["info"];
            return json($result);
        }
        $this->assign("data_count",$data_count["data"]);

        $start_time = 0;
        $end_time = 0;
        $data_overview = $this->_get_data_overview($start_time,$end_time);
        if($data_overview["status"]!=1){
            $result['status'] = $data_overview["status"];
            $result['info'] = $data_overview["info"];
            return json($result);
        }
        $this->assign("data_overview",$data_overview["data"]);

        $class_num = 0;
        $this->assign("class_num",$class_num);

        $data_num = 0;
        $this->assign("data_num",$data_num);
        return view();
    }
    public function data_count(){
        $result = ['status'=>0 ,'info'=>"获取数据关键指标时发生错误！"];
        $type = input("type",0,"int");
        $time = input("time",0,"int");
        //TODO $type 值和对应权限校验
        $data_count = $this->_get_data_count($type,$time);
        if($data_count["status"]!=1){
            $result['status'] = $data_count["status"];
            $result['info'] = $data_count["info"];
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取数据关键指标成功！";
        $result['data'] = $data_count["data"];
        return json($result);
    }
    public function _get_data_count($type,$time){
        $result = ['status'=>0 ,'info'=>"获取数据关键指标时发生错误！"];
        $result_data = [];
        $uids = [];
        switch ($type){
            case 0:
                $uids[] = $this->uid;
                break;
            case 1:
                $uids[] = $this->uid;
                break;
        }
        $start_time = 0;
        $end_time = 0;
        $timetools = new TimeTools();
        $time_arr=[0,0];
        switch ($time){
            case 0:
                $time_arr = $timetools->today();
                break;
            case -1:
                $time_arr = $timetools->yesterday();
                break;
            case 2:
                $time_arr = $timetools->week();
                break;
            case 3:
                $time_arr = $timetools->month();
                break;
            case 4:
                $time_arr = $timetools->season();
                break;
            case 5:
                $time_arr = $timetools->year();
                break;
        }
        if(isset($time_arr[0])){
            $start_time = $time_arr[0];
        }
        if(isset($time_arr[1])){
            $end_time = $time_arr[1];
        }

        $result_data = [
            "new_customer"=>0,
            "all_call_time"=>0,
            "valid_call_time"=>0,
            "new_sale_chance"=>0,
            "new_sign_in"=>0,
            "new_sale_order"=>0,
            "new_order_money"=>0,
        ];
        if(empty($uids)){
            return $result_data;
        }

        $datacountM = new Datacount();
        $datacount = $datacountM->getDataCount($uids,$start_time,$end_time);
//        var_exp($datacount,'$datacount',1);
        if(isset($datacount["1"])){
            $result_data["all_call_time"] = $datacount["1"]["all_num"]?:0;
            $result_data["valid_call_time"] = $datacount["1"]["tag_num"]?:0;
        }
        if(isset($datacount["2"])){
            $result_data["new_sale_chance"] = $datacount["2"]["all_num"];
        }
        if(isset($datacount["3"])){
            $result_data["new_sale_chance"] = $datacount["3"]["all_num"];
        }
        if(isset($datacount["4"])){
            $result_data["new_sale_order"] = $datacount["4"]["all_num"];
        }
        if(isset($datacount["5"])){
            $result_data["new_sign_in"] = $datacount["5"]["all_num"];
        }
        if(isset($datacount["6"])){
            $result_data["new_customer"] = $datacount["6"]["all_num"];
        }

        $result['status'] = 1;
        $result['info'] = "获取数据关键指标成功！";
        $result['data'] = $result_data;
        return $result;
    }
    public function data_overview(){
        $result = ['status'=>0 ,'info'=>"获取数据概览时发生错误！"];
        $start_time = input("start_time",0,"int");
        $end_time = input("end_time",0,"int");
        //TODO $type 值和对应权限校验
        $data_overview = $this->_get_data_overview($start_time,$end_time);
        if($data_overview["status"]!=1){
            $result['status'] = $data_overview["status"];
            $result['info'] = $data_overview["info"];
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取数据概览成功！";
        $result['data'] = $data_overview["data"];
        return json($result);
    }
    public function _get_data_overview($start_time,$end_time){
        $result = ['status'=>0 ,'info'=>"获取数据概览时发生错误！"];
        $result_data = [];



        $result['status'] = 1;
        $result['info'] = "获取数据概览成功！";
        $result['data'] = $result_data;
        return $result;
    }
}
