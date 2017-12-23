<?php

namespace app\datacount\service;

use \myvendor\TimeTools;
use app\datacount\model\Datacount as DatacountModel;
use app\task\model\EmployeeTask;
use app\task\model\DayTask;

class DataCount{
    protected $uid;
    protected $corp_id;
    public function __construct($corp_id,$uid){
        $this->corp_id = $corp_id;
        $this->uid = $uid;
    }
    public $name_title_idx = [
        "customer"=>"新增客户",
        "all_call_time"=>"总通话时长",
        "valid_call_time"=>"有效通话时长",
        "all_call_num"=>"总通话数",
        "valid_call_num"=>"有效通话数",
        "sale_chance"=>"商机",
        "sign_in"=>"拜访",
        "sale_order"=>"成单",
        "order_money"=>"成单金额",
        "contact"=>"联系人",
        "tend_to"=>"意向客户",
        "sale_status"=>"阶段变化",
    ];
    public $type_name_idx = [
        "1"=>["num"=>"all_call_num","sum_num"=>"all_call_time","tag_num"=>"valid_call_num","tag_sum"=>"valid_call_time"],
        "2"=>["num"=>"sale_chance","sum_num"=>"sale_status"],
        "3"=>["num"=>"sale_order","sum_num"=>"order_money"],
        "5"=>["sum_num"=>"sign_in"],
        "6"=>["sum_num"=>"customer"],
        "7"=>["sum_num"=>"contact"],
        "8"=>["sum_num"=>"tend_to"],
    ];
    public $name_type_field_idx = [
        "all_call_num"=>["1","num"],
        "all_call_time"=>["1","sum_num"],
        "valid_call_num"=>["1","tag_num"],
        "valid_call_time"=>["1","tag_sum"],
        "sale_chance"=>["2","num"],
        "sale_status"=>["2","tag_num"],
        "sale_order"=>["3","num"],
        "order_money"=>["3","sum_num"],
        "sign_in"=>["5","sum_num"],
        "customer"=>["6","sum_num"],
        "contact"=>["7","sum_num"],
        "tend_to"=>["8","sum_num"],
    ];
    public $task_type_idx = [
        "1"=>"valid_call_num",
        "2"=>"sale_chance",
        "3"=>"order_money",
        "4"=>"sale_order",
        "5"=>"sign_in",
        "6"=>"customer",
        "8"=>"all_call_num",
        "9"=>"sale_status",
        "10"=>"contact",
        "11"=>"tend_to",
        "12"=>"all_call_time",
        "13"=>"valid_call_time",
    ];
    public function get_data_count($uids,$start_time,$end_time){
        $result_data = [
            "customer"=>0,
            "all_call_num"=>0,
            "valid_call_num"=>0,
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

        $datacountM = new DatacountModel();
        $datacount = $datacountM->getDataCount($uids,$start_time,$end_time);
//        var_exp($datacount,'$datacount',1);
        if(isset($datacount["1"])){
            $result_data["all_call_time"] = $datacount["1"]["sum_num"]?:0;
            $result_data["valid_call_time"] = $datacount["1"]["tag_sum"]?:0;
            $result_data["all_call_num"] = $datacount["1"]["num"]?:0;
            $result_data["valid_call_num"] = $datacount["1"]["tag_num"]?:0;
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
    public function get_employee_data_count($uids,$start_time,$end_time){
        $result_data = [];
        $result = ['status'=>0 ,'info'=>"获取数据关键指标时发生错误！","data"=>$result_data];
        if(
            empty($uids)
            ||($start_time<=0&&$end_time<=0)
            || $start_time>=$end_time
        ){
            return $result;
        }

        $user_data = [
            "customer"=>0,
            "all_call_num"=>0,
            "valid_call_num"=>0,
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
        $datacountM = new DatacountModel();
        $datacountList = $datacountM->getEmployeeDataCount($uids,$start_time,$end_time);
//        var_exp($datacountList,'$datacountList',1);
        foreach ($datacountList as $datacount){
            if(!isset($result_data[$datacount["uid"]])){
                $result_data[$datacount["uid"]] = $user_data;
            }
            if($datacount["type"]=="1"){
                $result_data[$datacount["uid"]]["all_call_time"] = $datacount["sum_num"]?:0;
                $result_data[$datacount["uid"]]["valid_call_time"] = $datacount["tag_sum"]?:0;
                $result_data[$datacount["uid"]]["all_call_num"] = $datacount["num"]?:0;
                $result_data[$datacount["uid"]]["valid_call_num"] = $datacount["tag_num"]?:0;
            }
            if($datacount["type"]=="2"){
                $result_data[$datacount["uid"]]["sale_chance"] = $datacount["num"];
                $result_data[$datacount["uid"]]["sale_status"] = $datacount["sum_num"];
            }
            if($datacount["type"]=="3"){
                $result_data[$datacount["uid"]]["sale_order"] = $datacount["num"];
                $result_data[$datacount["uid"]]["order_money"] = $datacount["sum_num"];
            }
            if($datacount["type"]=="5"){
                $result_data[$datacount["uid"]]["sign_in"] = $datacount["sum_num"];
            }
            if($datacount["type"]=="6"){
                $result_data[$datacount["uid"]]["customer"] = $datacount["sum_num"];
            }
            if($datacount["type"]=="7"){
                $result_data[$datacount["uid"]]["contact"] = $datacount["sum_num"];
            }
            if($datacount["type"]=="8"){
                $result_data[$datacount["uid"]]["tend_to"] = $datacount["sum_num"];
            }
        }

        $result['status'] = 1;
        $result['info'] = "获取数据关键指标成功！";
        $result['data'] = $result_data;
        return $result;
    }
    public function get_type_data_count($type,$uids,$start_time,$end_time){
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
        $datacountM = new DatacountModel();
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
    public function get_day_count_task($data_count,$day_task){
        $day_task_data = [];
        foreach ($data_count as $key=>$data_count_item){
            $day_task_data[$key]["num"] = $data_count_item;
            $target = 0;
            if(isset($day_task[$key])){
                $target = $day_task[$key];
            }
            $day_task_data[$key]["target"] = $target;
            if($data_count_item>$target){
                $day_task_data[$key]["per"] = 1;
            }elseif($target>0){
                $day_task_data[$key]["per"] = $data_count_item/$target;
            }
        }
        return $day_task_data;
    }
    public function get_day_task_count($day_task,$data_count){
        $day_task_data = [];
        foreach ($day_task as $key=>$day_task_item){
            $day_task_data[$key]["target"] = $day_task_item;
            $num = 0;
            if(isset($data_count[$key])){
                $num = $data_count[$key];
            }
            $day_task_data[$key]["num"] = $num;
            if($num>$day_task_item){
                $day_task_data[$key]["per"] = 1;
            }elseif($day_task_item>0){
                $day_task_data[$key]["per"] = $num/$day_task_item;
            }
        }
        return $day_task_data;
    }
    public function get_day_task($uids){
        $result_data = [
            "customer"=>0,
            "all_call_time"=>0,
            "valid_call_time"=>0,
            "all_call_num"=>0,
            "valid_call_num"=>0,
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
        ){
            return $result;
        }

        $dayTaskM = new DayTask($this->corp_id);
        $employeeTaskList = $dayTaskM->getAllDayTaskByEmployeeIds($uids);

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
    public function get_employee_task($uids){
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
    public function get_sales_funnel($uids,$start_time,$end_time,$list){
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
                    "all_call_num",
                    "valid_call_num",
                    "tend_to",
                    "sign_in",
                    "sale_order",
                ];
                break;
        }
        if(empty($items)){
            return $result;
        }

        $data_count = $this->get_data_count($uids,$start_time,$end_time);
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
    public function get_data_overview($uids,$time_type,$time_num,$items){
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

        $datacountM = new DatacountModel();
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
    public function get_uids($type,$struct_id){
        $uids = [];
        switch ($type){
            case 0:
                $uids[] = $this->uid;
                break;
            case 1:
                $uids[] = $this->uid;
                //TODO 获取对应部门
                break;
        }
        return $uids;
    }
    public function get_structs($type,$struct_id){
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
    public function get_times($time,$start_time,$end_time){
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