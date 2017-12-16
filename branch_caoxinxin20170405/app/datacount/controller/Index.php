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
use app\task\model\EmployeeTask;
use \myvendor\TimeTools;

class Index extends Initialize{
    protected $name_title_idx = [
        "customer"=>"新增客户",
        "all_call_time"=>"总通话",
        "valid_call_time"=>"有效通话",
        "sale_chance"=>"商机",
        "sign_in"=>"拜访",
        "sale_order"=>"成单",
        "order_money"=>"成单金额",
        "contact"=>"联系人",
        "tend_to"=>"意向客户",
        "sale_status"=>"阶段变化",
    ];
    protected $type_name_idx = [
        "1"=>["sum_num"=>"all_call_time","tag_num"=>"valid_call_time"],
        "2"=>["num"=>"sale_chance","sum_num"=>"sale_status"],
        "3"=>["num"=>"sale_order","sum_num"=>"order_money"],
        "5"=>["sum_num"=>"sign_in"],
        "6"=>["sum_num"=>"customer"],
        "7"=>["sum_num"=>"contact"],
        "8"=>["sum_num"=>"tend_to"],
    ];
    protected $name_type_field_idx = [
        "all_call_time"=>["1","sum_num"],
        "valid_call_time"=>["1","tag_num"],
        "sale_chance"=>["2","num"],
        "sale_status"=>["2","tag_num"],
        "sale_order"=>["3","num"],
        "order_money"=>["3","sum_num"],
        "sign_in"=>["5","sum_num"],
        "customer"=>["6","sum_num"],
        "contact"=>["7","sum_num"],
        "tend_to"=>["8","sum_num"],
    ];
    protected $task_type_idx = [
        "1"=>"valid_call_time",
        "2"=>"sale_chance",
        "3"=>"order_money",
        "4"=>"sale_order",
        "5"=>"sign_in",
        "6"=>"customer",
        "8"=>"all_call_time",
        "9"=>"sale_status",
        "10"=>"contact",
        "11"=>"tend_to",
    ];
    public function __construct(){
        parent::__construct();
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
        $class_num = 0;
        $this->assign("class_num",$class_num);
        $data_num = 0;
        $this->assign("data_num",$data_num);

        $type = 0;
        $struct_id = 0;
        //TODO $type 值和对应权限校验
        $uids = $this->_get_uids($type,$struct_id);
        $time = 1;
        $start_time = 0;
        $end_time = 0;
        list($start_time,$end_time) = $this->_get_times($time,$start_time,$end_time);
        $data_count = $this->_get_data_count($uids,$start_time,$end_time);
        $day_task = $this->_get_day_task($uids,$start_time,$end_time);
        $day_task_count = $this->_get_day_task_count($day_task["data"],$data_count["data"]);
        $this->assign("task_data_count",$day_task_count);

        $employee_task = $this->_get_employee_task($uids);
        foreach ($employee_task["data"] as &$employee_task_item){
            $datacount_type = 0;
            $type_name = '';
            if(isset($this->task_type_idx[$employee_task_item["target_type"]])){
                $datacount_type = $this->task_type_idx[$employee_task_item["target_type"]];
                $type_name = $this->name_title_idx[$datacount_type];
            }
            $now_num = $this->_get_type_data_count($datacount_type,$uids,$start_time,$end_time);
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
        list($start_time,$end_time) = $this->_get_times($time,$start_time,$end_time);

        $list = 1;
        $sales_funnel = $this->_get_sales_funnel($uids,$start_time,$end_time,$list);
        $this->assign("sales_funnel",$sales_funnel["data"]);
        $this->assign("sales_funnel_start_time",$start_time);
        $this->assign("sales_funnel_end_time",$end_time);


        $time_type = 1;
        $time_num = 4;

        $items = [
            "valid_call_time",
            "sale_chance",
            "sign_in",
            "sale_order",
        ];

        $data_overview = $this->_get_data_overview($uids,$time_type,$time_num,$items);
        $this->assign("data_overview",json_encode($data_overview["data"],true));
        $this->assign("data_overview_time_type",$time_num);
        $this->assign("data_overview_time_num",$time_num);

        $this->assign("name_title_idx",$this->name_title_idx);
        return view();
    }
    public function data_count(){
        $result = ['status'=>0 ,'info'=>"获取数据关键指标时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->_get_uids($type,$struct_id);

        $time = input("time",0,"int");
        $start_time = input("start_time",0,"int");
        $end_time = input("end_time",0,"int");
        list($start_time,$end_time) = $this->_get_times($time,$start_time,$end_time);

        $data_count = $this->_get_data_count($uids,$start_time,$end_time);
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
    protected function _get_data_count($uids,$start_time,$end_time){
        $result_data = [
            "customer"=>0,
            "all_call_time"=>0,
            "valid_call_time"=>0,
            "sale_chance"=>0,
            "sign_in"=>0,
            "sale_order"=>0,
            "order_money"=>0,
            "contact"=>0,
            "tend_to"=>0,
            "sale_status"=>0,
        ];
        $result = ['status'=>0 ,'info'=>"获取数据关键指标时发生错误！","data"=>$result_data];
        if(
            empty($uids)
            ||($start_time<=0&&$end_time<=0)
            || $start_time>=$end_time
        ){
            return $result;
        }

        $datacountM = new Datacount();
        $datacount = $datacountM->getDataCount($uids,$start_time,$end_time);
//        var_exp($datacount,'$datacount',1);
        if(isset($datacount["1"])){
            $result_data["all_call_time"] = $datacount["1"]["sum_num"]?:0;
            $result_data["valid_call_time"] = $datacount["1"]["tag_num"]?:0;
        }
        if(isset($datacount["2"])){
            $result_data["sale_chance"] = $datacount["2"]["num"];
            $result_data["sale_status"] = $datacount["2"]["sum_num"];
        }
        if(isset($datacount["3"])){
            $result_data["sale_order"] = $datacount["3"]["num"];
            $result_data["order_money"] = $datacount["3"]["sum_num"];
        }
        if(isset($datacount["5"])){
            $result_data["sign_in"] = $datacount["5"]["sum_num"];
        }
        if(isset($datacount["6"])){
            $result_data["customer"] = $datacount["6"]["sum_num"];
        }
        if(isset($datacount["7"])){
            $result_data["contact"] = $datacount["7"]["sum_num"];
        }
        if(isset($datacount["8"])){
            $result_data["tend_to"] = $datacount["8"]["sum_num"];
        }

        $result['status'] = 1;
        $result['info'] = "获取数据关键指标成功！";
        $result['data'] = $result_data;
        return $result;
    }
    protected function _get_type_data_count($type,$uids,$start_time,$end_time){
        $result_data = 0;
        $result = ['status'=>0 ,'info'=>"获取数据关键指标时发生错误！","data"=>$result_data];
        if(
            !isset($this->name_type_field_idx[$type])
            ||empty($uids)
            ||($start_time<=0&&$end_time<=0)
            || $start_time>=$end_time
        ){
            return $result;
        }
        $field = $this->name_type_field_idx[$type];
        $type_id = $field[0];
        $datacountM = new Datacount();
        $datacount = $datacountM->getTypeDataCount($type_id,$uids,$start_time,$end_time);
//        var_exp($datacount,'$datacount',1);
        if(isset($datacount[$type_id])){
            $result_data = $datacount[$type_id][$field[1]];
        }

        $result['status'] = 1;
        $result['info'] = "获取数据关键指标成功！";
        $result['data'] = $result_data;
        return $result;
    }
    public function day_task(){
        $result = ['status'=>0 ,'info'=>"获取日常任务统计时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->_get_uids($type,$struct_id);

        $time = input("time",0,"int");
        $start_time = 0;
        $end_time = 0;
        list($start_time,$end_time) = $this->_get_times($time,$start_time,$end_time);

        $data_count = $this->_get_data_count($uids,$start_time,$end_time);
        if($data_count["status"]!=1){
            $result['status'] = $data_count["status"];
            $result['info'] = $data_count["info"];
            return json($result);
        }

        $day_task = $this->_get_day_task($uids,$start_time,$end_time);
        if($day_task["status"]!=1){
            $result['status'] = $day_task["status"];
            $result['info'] = $day_task["info"];
            return json($result);
        }

        $day_task_data = $this->_get_day_count_task($data_count["data"],$day_task["data"]);

        $result['status'] = 1;
        $result['info'] = "获取日常任务统计成功！";
        $result['data'] = $day_task_data;
        return json($result);
    }
    protected function _get_day_count_task($data_count,$day_task){
        $day_task_data = [];
        foreach ($data_count as $key=>$data_count_item){
            $day_task_data[$key]["num"] = $data_count_item;
            $target = 0;
            if(isset($day_task[$key])){
                $target = $day_task[$key];
            }
            $day_task_data[$key]["target"] = $target;
        }
        return $day_task_data;
    }
    protected function _get_day_task_count($day_task,$data_count){
        $day_task_data = [];
        foreach ($day_task as $key=>$day_task_item){
            $day_task_data[$key]["target"] = $day_task_item;
            $num = 0;
            if(isset($data_count[$key])){
                $num = $data_count[$key];
            }
            $day_task_data[$key]["num"] = $num;
        }
        return $day_task_data;
    }
    protected function _get_day_task($uids,$start_time,$end_time,$is_task=1){
        $result_data = [
            "customer"=>0,
            "all_call_time"=>0,
            "valid_call_time"=>0,
            "sale_chance"=>0,
            "sign_in"=>0,
            "sale_order"=>0,
            "order_money"=>0,
            "contact"=>0,
            "tend_to"=>0,
            "sale_status"=>0,
        ];
        $result = ['status'=>0 ,'info'=>"获取日常任务目标时发生错误！","data"=>$result_data];
        if(
            empty($uids)
            ||($start_time<=0&&$end_time<=0)
            || $start_time>=$end_time
        ){
            return $result;
        }
        $task_type = ($is_task?1:2)+2;

        $employeeTaskM = new EmployeeTask($this->corp_id);
        $employeeTaskList = $employeeTaskM->getAllDayTaskByEmployeeIds($task_type,$uids,$start_time,$end_time);

        foreach ($employeeTaskList as $employeeTask){
            if(isset($this->task_type_idx[$employeeTask["target_type"]])){
                $result_data[$this->task_type_idx[$employeeTask["target_type"]]]=$employeeTask["target_num"];
            }
        }

        $result['status'] = 1;
        $result['info'] = "获取日常任务目标成功！";
        $result['data'] = $result_data;
        return $result;
    }
    protected function _get_employee_task($uids){
        $employeeTaskList = [];
        $result = ['status'=>0 ,'info'=>"获取员工任务时发生错误！","data"=>$employeeTaskList];
        $time = time();

        $employeeTaskM = new EmployeeTask();
        $employeeTaskList = $employeeTaskM->getAllEmployeeTaskByEmployeeIds($uids,$time);

        $result['status'] = 1;
        $result['info'] = "获取日常任务目标成功！";
        $result['data'] = $employeeTaskList;
        return $result;
    }
    public function sales_funnel(){
        $result = ['status'=>0 ,'info'=>"获取数据概览时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->_get_uids($type,$struct_id);

        $time = input("time",0,"int");
        $start_time = input("start_time",0,"int");
        $end_time = input("end_time",0,"int");
        list($start_time,$end_time) = $this->_get_times($time,$start_time,$end_time);

        $list = input("list",0,"int");
        $sales_funnel = $this->_get_sales_funnel($uids,$start_time,$end_time,$list);
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
    protected function _get_sales_funnel($uids,$start_time,$end_time,$list){
        $result_data = [];
        $result = ['status'=>0 ,'info'=>"获取数据概览时发生错误！","data"=>$result_data];
        if(
            empty($uids)
            ||($start_time<=0&&$end_time<=0)
            || $start_time>=$end_time
        ){
            return $result;
        }
        $items = [];
        switch ($list){
            case 1:
                $items = [
                    "customer",
                    "tend_to",
                    "sale_chance",
                    "sign_in",
                    "sale_order",
                ];
                break;
            case 2:
                $items = [
                    "all_call_time",
                    "valid_call_time",
                    "tend_to",
                    "sign_in",
                    "sale_order",
                ];
                break;
        }
        if(empty($items)){
            return $result;
        }

        $data_count = $this->_get_data_count($uids,$start_time,$end_time);
        if($data_count["status"]!=1){
            return $result;
        }

        $item_num = count($items);
        $previous_num = 0;
        for($i=0;$i<$item_num;$i++){
            $item = $items[$i];
            if(!isset($data_count["data"][$item])){
                return $result;
            }
            $now_num = $data_count["data"][$item];
            if($i==0){
                $previous_num = $now_num;
            }
            $result_data[$i+1] = ["name"=>$item,"num"=>$now_num,"target"=>$previous_num];
            $previous_num = $now_num;
        }

        $result['status'] = 1;
        $result['info'] = "获取数据概览成功！";
        $result['data'] = $result_data;
        return $result;
    }
    public function data_overview(){
        $result = ['status'=>0 ,'info'=>"获取数据预估时发生错误！"];
        $type = input("type",0,"int");
        $struct_id = input("struct_id",0,"int");
        //TODO $type 值和对应权限校验
        $uids = $this->_get_uids($type,$struct_id);

        $time_type = input("time_type",1,"int");
        $time_num = input("time_num",4,"int");

        $items = [
            "valid_call_time",
            "sale_chance",
            "sign_in",
            "sale_order",
        ];

        $data_overview = $this->_get_data_overview($uids,$time_type,$time_num,$items);
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
    public function _get_data_overview($uids,$time_type,$time_num,$items){
        $result_data = [];
        $result = ['status'=>0 ,'info'=>"获取数据预估时发生错误！","data"=>$result_data];
        $start_time = 0;
        $end_time = 0;
        $timetools = new TimeTools();
        switch($time_type){
            case 1:
                list($start_time,$end_time) = $timetools->lastMonths($time_num);
                break;
            case 2:
                list($start_time,$end_time) = $timetools->lastSeasons($time_num);
                break;
            case 3:
                list($start_time,$end_time) = $timetools->lastYears($time_num);
                break;
        }
        if(
            empty($uids)
            ||($start_time<=0&&$end_time<=0)
            || $start_time>=$end_time
        ){
            return $result;
        }

        $datacountM = new Datacount();
        $data_count = [];
        switch($time_type){
            case 1:
                $data_count = $datacountM->getDatacountMonth($uids,$start_time,$end_time);
                break;
            case 2:
                $data_count = $datacountM->getDatacountSeason($uids,$start_time,$end_time);
                break;
            case 3:
                $data_count = $datacountM->getDatacountYear($uids,$start_time,$end_time);
                break;
        }
        $result_data_tmp = [];
        foreach ($data_count as $item){
            if(isset($this->type_name_idx[$item["type"]])){
                $fields = $this->type_name_idx[$item["type"]];
                foreach ($fields as $field=>$name){
                    $result_data_tmp[$name][$item["group_flg"]] = $item[$field];
                }
            }
        }
//        var_exp($result_data_tmp,'$result_data_tmp');

        foreach ($items as $item){
            $all = 0;
            for ($i=0;$i<$time_num;$i++){
                $group_flg = "";
                switch($time_type){
                    case 1:
                        if($i+1==$time_num){
                            $group_flg = date("Y-m");
                        }else{
                            $time = time();
                            $d = strtotime("-".($time_num-1-$i)." Months",strtotime(date('Y',$time)."-".date('m',$time)."-01"));
                            $group_flg = date("Y-m",$d);
                        }
                        break;
                    case 2:
                        if($i+1==$time_num){
                            $season = ceil((date('n'))/3);//当月是第几季度
                            $group_flg = date("Y-").$season;
                        }else{
                            $num = ($time_num-1-$i);
                            $season = ceil((date('n'))/3);//当月是第几季度
                            $year = date('Y')-ceil(($num-$season)/4);
                            $season = 4-(($num-$season)%4);
                            $group_flg = $year.'Q'.$season;
                        }
                        break;
                    case 3:
                        if($i+1==$time_num){
                            $group_flg = date("Y");
                        }else{
                            $time = time();
                            $d = strtotime("-".($time_num-1-$i)." Years",strtotime(date('Y',$time)."-".date('m',$time)."-01"));
                            $group_flg = date("Y",$d);
                        }
                        break;
                }
                if(isset($result_data_tmp[$item][$group_flg])){
                    $result_data[$item][$i+1] = $result_data_tmp[$item][$group_flg];
                }else{
                    $result_data[$item][$i+1] = 0;
                }
                $all += $result_data[$item][$i+1];
            }
            $result_data[$item][$time_num+1] = round($all/$time_num);
        }

        $result['status'] = 1;
        $result['info'] = "获取数据预估成功！";
        $result['data'] = $result_data;
        return $result;
    }
    protected function _get_uids($type,$struct_id){
        $uids = [];
        switch ($type){
            case 0:
                $uids[] = $this->uid;
                break;
            case 1:
                $uids[] = $this->uid;
                //TODO 获取对应用户
                break;
        }
        return $uids;
    }
    protected function _get_times($time,$start_time,$end_time){
        if($time&&($start_time<=0&&$end_time<=0)){
            $timetools = new TimeTools();
            $time_arr=[0,0];
            switch ($time){
                case -1:
                    $time_arr = $timetools->yesterday();
                    break;
                case 1:
                    $time_arr = $timetools->today();
                    break;
                case -2:
                    $time_arr = $timetools->lastWeek();
                    break;
                case 2:
                    $time_arr = $timetools->week();
                    break;
                case -3:
                    $time_arr = $timetools->lastMonth();
                    break;
                case 3:
                    $time_arr = $timetools->month();
                    break;
                case -4:
                    $time_arr = $timetools->lastSeason();
                    break;
                case 4:
                    $time_arr = $timetools->season();
                    break;
                case -5:
                    $time_arr = $timetools->lastYear();
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
        }
        return [$start_time,$end_time];
    }
}
