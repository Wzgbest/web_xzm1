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
use app\datacount\service\DataCount as DataCountService;

class Index extends Initialize{
    protected $dataCountSrv;
    public function __construct(){
        parent::__construct();
        $this->dataCountSrv = new DataCountService($this->corp_id,$this->uid);
    }
    public function index(){
        return 'Index/index';
    }
    public function test(){
        return view();
    }
    public function target_set(){
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
                "title"=>"初步江湖",
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
        $class_num = 0;
        $this->assign("class_num",$class_num);
        $data_num = 0;
        $this->assign("data_num",$data_num);

        $type = 0;
        $struct_id = 0;
        //TODO $type 值和对应权限校验
        $uids = $this->dataCountSrv->get_uids($type,$struct_id);
        $time = 1;
        $start_time = 0;
        $end_time = 0;
        list($start_time,$end_time) = $this->dataCountSrv->get_times($time,$start_time,$end_time);
        $data_count = $this->dataCountSrv->get_data_count($uids,$start_time,$end_time);
        $day_task = $this->dataCountSrv->get_day_task($uids);
        $day_task_count = $this->dataCountSrv->get_day_task_count($day_task["data"],$data_count["data"]);
        $this->assign("task_data_count",$day_task_count);

        $employee_task = $this->dataCountSrv->get_employee_task($uids);
        foreach ($employee_task["data"] as &$employee_task_item){
            $datacount_type = 0;
            $type_name = '';
            if(isset($this->dataCountSrv->task_type_idx[$employee_task_item["target_type"]])){
                $datacount_type = $this->dataCountSrv->task_type_idx[$employee_task_item["target_type"]];
                $type_name = $this->dataCountSrv->name_title_idx[$datacount_type];
            }
            $now_num = $this->dataCountSrv->get_type_data_count($datacount_type,$uids,$employee_task_item["task_start_time"],$employee_task_item["task_end_time"]);
            $employee_task_item["now_num"] = $now_num["data"];
            $employee_task_item["type_name"] = $type_name;
            $reward_money = 0;
            switch ($employee_task_item["task_method"]){
                case 1:
                    $reward_money = $employee_task_item["reward_amount"];
                    break;
                case 2:
                    $reward_money = $employee_task_item["reward_amount"];
                    break;
                case 3:
                    $reward_money = $employee_task_item["reward_amount"];
                    break;
                case 4:
                    $reward_money = $employee_task_item["reward_amount"];
                    break;
                case 5:
                    $reward_money = $employee_task_item["reward_amount"];
                    break;
            }
            $employee_task_item["reward_money"] = $reward_money;
        }
//        var_exp($employee_task,'$employee_task');
        $this->assign("employee_task",$employee_task["data"]);

        $time = 3;
        $start_time = 0;
        $end_time = 0;
        list($start_time,$end_time) = $this->dataCountSrv->get_times($time,$start_time,$end_time);

        $list = 1;
        $sales_funnel = $this->dataCountSrv->get_sales_funnel($uids,$start_time,$end_time,$list);
        $this->assign("sales_funnel",$sales_funnel["data"]);
        $this->assign("sales_funnel_start_time",$start_time);
        $this->assign("sales_funnel_end_time",$end_time);


        $time_type = 1;
        $time_num = 4;

        $items = [
            "valid_call_num",
            "sale_chance",
            "sign_in",
            "sale_order",
        ];

        $data_overview = $this->dataCountSrv->get_data_overview($uids,$time_type,$time_num,$items);
        $this->assign("data_overview",json_encode($data_overview["data"],true));
        $this->assign("data_overview_time_type",$time_num);
        $this->assign("data_overview_time_num",$time_num);

        $this->assign("name_title_idx",$this->dataCountSrv->name_title_idx);
        return view();
    }
    public function data_count(){
        $result = ['status'=>0 ,'info'=>"获取数据关键指标时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->dataCountSrv->get_uids($type,$struct_id);

        $time = input("time",0,"int");
        $start_time = input("start_time",0,"int");
        $end_time = input("end_time",0,"int");
        list($start_time,$end_time) = $this->dataCountSrv->get_times($time,$start_time,$end_time);

        $data_count = $this->dataCountSrv->get_data_count($uids,$start_time,$end_time);
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
    public function day_task(){
        $result = ['status'=>0 ,'info'=>"获取日常任务统计时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->dataCountSrv->get_uids($type,$struct_id);

        $time = input("time",0,"int");
        $start_time = 0;
        $end_time = 0;
        list($start_time,$end_time) = $this->dataCountSrv->get_times($time,$start_time,$end_time);

        $data_count = $this->dataCountSrv->get_data_count($uids,$start_time,$end_time);
        if($data_count["status"]!=1){
            $result['status'] = $data_count["status"];
            $result['info'] = $data_count["info"];
            return json($result);
        }

        $day_task = $this->dataCountSrv->get_day_task($uids);
        if($day_task["status"]!=1){
            $result['status'] = $day_task["status"];
            $result['info'] = $day_task["info"];
            return json($result);
        }

        $day_task_data = $this->dataCountSrv->get_day_count_task($data_count["data"],$day_task["data"]);

        $result['status'] = 1;
        $result['info'] = "获取日常任务统计成功！";
        $result['data'] = $day_task_data;
        return json($result);
    }
    public function sales_funnel(){
        $result = ['status'=>0 ,'info'=>"获取数据概览时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->dataCountSrv->get_uids($type,$struct_id);

        $time = input("time",0,"int");
        $start_time = input("start_time",0,"int");
        $end_time = input("end_time",0,"int");
        list($start_time,$end_time) = $this->dataCountSrv->get_times($time,$start_time,$end_time);

        $list = input("list",0,"int");
        $sales_funnel = $this->dataCountSrv->get_sales_funnel($uids,$start_time,$end_time,$list);
        if($sales_funnel["status"]!=1){
            $result['status'] = $sales_funnel["status"];
            $result['info'] = $sales_funnel["info"];
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取数据概览成功！";
        $result['data'] = $sales_funnel["data"];
        return json($result);
    }
    public function data_overview(){
        $result = ['status'=>0 ,'info'=>"获取数据预估时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->dataCountSrv->get_uids($type,$struct_id);

        $time_type = input("time_type",1,"int");
        $time_num = input("time_num",4,"int");

        $items = [
            "valid_call_num",
            "sale_chance",
            "sign_in",
            "sale_order",
        ];

        $data_overview = $this->dataCountSrv->get_data_overview($uids,$time_type,$time_num,$items);
        if($data_overview["status"]!=1){
            $result['status'] = $data_overview["status"];
            $result['info'] = $data_overview["info"];
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取数据预估成功！";
        $result['data'] = $data_overview["data"];
        return json($result);
    }
}
