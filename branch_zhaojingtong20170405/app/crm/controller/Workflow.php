<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use think\File;

class Workflow extends Initialize{
    protected $_activityBusinessFlowItem = [1,2,4];
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function work_flow_test(){
        $file_path = "./../test.bpmn";
        $realpath = realpath($file_path);
        //var_exp($realpath,'$realpath');
        $xmlfile = new File($realpath);
        $xml = "";
        while (!$xmlfile->eof()) {
            $xml .= $xmlfile->current();
            $xmlfile->next();
        }
        $xmlfile = null;

//        $workflowManage = new \workflow\WorkflowManage(new \workflow\db\WorkflowDBTp5($this->corp_id));
//        $workflowManage->initByXml($xml);
        $File = new \workflow\file\WorkflowFileTp5();
        $File->loadFile($file_path);
    }
    public function work_flow_list(){
    }
    public function create_page(){
        $flow_id = input("flow_id",0,"int");
        if(!$flow_id){
            $this->error("参数错误!");
        }
        $DBModel = new \workflow\db\WorkflowDBTp5($this->corp_id);
        $File = new \workflow\file\WorkflowFileTp5();
        $workflowEngine = new \workflow\WorkflowEngine($DBModel,$File);
        $init_result = $workflowEngine->init($flow_id);
        //var_exp($init_result,'$init_result');
        $next_node_form_item = $workflowEngine->getCreateNextItem();
        $this->assign("flow_id",$flow_id);
        $this->assign("next_node_form_item",$next_node_form_item);
        return view();
    }
//    public function work_flow_create_page(){
//        $flow_id = input("flow_id",0,"int");
//        if(!$flow_id){
//            $this->error("参数错误!");
//        }
//        $DBModel = new \workflow\db\WorkflowDBTp5($this->corp_id);
//        $workflowForm = new \workflow\WorkflowForm($DBModel);
//            $File = new \workflow\file\WorkflowFileTp5();
//            $workflowEngine = new \workflow\WorkflowEngine($DBModel,$File);
//        $init_result = $workflowEngine->init($flow_id);
//        $workflow = $workflowEngine->getWorkflow();
//        var_exp($init_result,'$init_result');
//        $flow_create_form_item = $workflowEngine->getCreateFormItem();
//        $this->assign("flow_id",$flow_id);
//        $this->assign("form_item",$flow_create_form_item);
//        return view();
//    }
    public function work_flow_create(){
        $result = ['status'=>0 ,'info'=>"新建进程时发生错误！"];
        $flow_id = input("flow_id",0,"int");
        $process_title = input("process_title","","string");
        if(!$flow_id||empty($process_title)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $input["uid"] = $this->uid;
        $input["remark"] = input("remark",'',"string");
        $input["type"] = input("type",0,"int");
        $input["num"] = input("num",0,"int");
        if(!$input["type"]||!$input["num"]){
            $result['info'] = "表单参数错误！";
            return json($result);
        }
        //var_exp(input("post."),'post.');
        $DBModel = new \workflow\db\WorkflowDBTp5($this->corp_id);
        $File = new \workflow\file\WorkflowFileTp5();
        $workflowEngine = new \workflow\WorkflowEngine($DBModel,$File);
        $init_result = $workflowEngine->init($flow_id);
//        var_exp($init_result,'$init_result');
        if($init_result["status"]!=1){
            $result['status'] = $init_result["status"];
            $result['info'] = $init_result["info"];
            return json($result);
        }
        $next_node_form_item = $workflowEngine->getCreateNextItem();
        foreach ($next_node_form_item as $item){
            $input[$item["name"]] = input($item["name"],'',"string");
        }
        $create_result = $workflowEngine->createProcess($process_title,$this->uid,$input);
//      var_exp($create_result,'$create_result');
        if($create_result["status"]!=1){
            $result['status'] = $create_result["status"];
            $result['info'] = $create_result["info"];
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = $create_result["info"];
        $result['data'] = $create_result["data"];
        return json($result);
    }
    public function work_flow_process_list(){
    }
    public function process_info(){
        $process_id = input("process_id",0,"int");
        if(!$process_id){
            $this->error("参数错误！");
        }
        $DBModel = new \workflow\db\WorkflowDBTp5($this->corp_id);
        $File = new \workflow\file\WorkflowFileTp5();
        $workflowEngine = new \workflow\WorkflowEngine($DBModel,$File);
        $load_result = $workflowEngine->loadProcess($process_id);
//        var_exp($load_result,'$load_result');
        if($load_result["status"]!=1){
            $this->error($load_result["info"]);
        }
        $read_only = 0;
        $process = $workflowEngine->getProcess();
        $now_node = $workflowEngine->getNowNode();
        if($process["process_status"]!=2){
            $read_only = 1;
        }elseif($now_node["flow_item_form_read_only"]){
            $read_only = 1;
        }
        $now_form_data = ["type"=>2,"num"=>1111];
        $process_action = $workflowEngine->getProcessAction();
        unset($process_action[0]);
        $next_node_form_item = $workflowEngine->getNextItem();
        $this->assign("process_id",$process_id);
        $this->assign("process",$process);
        $this->assign("now_node",$now_node);
        $this->assign("read_only",$read_only);
        $this->assign("now_form_data",$now_form_data);
        $this->assign("process_action",$process_action);
        $this->assign("next_node_form_item",$next_node_form_item);
        return view();
    }
//    public function work_flow_process_info(){
//        $result = ['status'=>0 ,'info'=>"新建进程时发生错误！"];
//        $process_id = input("process_id",0,"int");
//        if(!$process_id){
//            $result['info'] = "参数错误！";
//            return json($result);
//        }
//            $DBModel = new \workflow\db\WorkflowDBTp5($this->corp_id);
//            $File = new \workflow\file\WorkflowFileTp5();
//            $workflowEngine = new \workflow\WorkflowEngine($DBModel,$File);
//        $load_result = $workflowEngine->loadProcess($process_id);
////        var_exp($load_result,'$load_result');
//        if($load_result["status"]!=1){
//            $result['status'] = $load_result["status"];
//            $result['info'] = $load_result["info"];
//            return json($result);
//        }
//        $process = $workflowEngine->getProcess();
//        $now_form_item = $workflowEngine->getNowNodeFormItem();
//        $now_form_data = $workflowEngine->getProcessData([]);
//        $next_node_form_item = $workflowEngine->getNextNodeFormItem();
//        $this->assign("process_id",$process_id);
//        $this->assign("process",$process);
//        $this->assign("form_item",$now_form_item);
//        $this->assign("now_form_data",$now_form_data);
//        $this->assign("next_node_form_item",$next_node_form_item);
//        return view();
//    }
    public function work_flow_process_run(){
        $result = ['status'=>0 ,'info'=>"新建进程时发生错误！"];
        $process_id = input("process_id",0,"int");
        if(!$process_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $DBModel = new \workflow\db\WorkflowDBTp5($this->corp_id);
        $File = new \workflow\file\WorkflowFileTp5();
        $workflowEngine = new \workflow\WorkflowEngine($DBModel,$File);
        $load_result = $workflowEngine->loadProcess($process_id);
//        var_exp($load_result,'$load_result');
        if($load_result["status"]!=1){
            $result['status'] = $load_result["status"];
            $result['info'] = $load_result["info"];
            return json($result);
        }
        $process = $workflowEngine->getProcess();
//        var_exp($process,'$process');
        if($process["process_status"]==3){
            $result['info'] = "进程已运行结束！";
            return json($result);
        }
        $input["uid"] = $this->uid;
        $input["remark"] = input("remark",'',"string");
        $now_node = $workflowEngine->getNowNode();
        if(!$now_node["flow_item_form_read_only"]){
            $input["type"] = input("type",0,"int");
            $input["num"] = input("num",0,"int");
            if(!$input["type"]||!$input["num"]){
                $result['info'] = "表单参数错误！";
                return json($result);
            }
        }else{
            $input["type"] = 2;
            $input["num"] = 1111;
        }
        $next_node_form_item = $workflowEngine->getNextItem();
        foreach ($next_node_form_item as $next_item){
            $input[$next_item["name"]] = input($next_item["name"],"","string");
        }
//        var_exp($input,'$input');
        $run_result = $workflowEngine->run($input);
        if($run_result["status"]!=1){
            $result['status'] = $run_result["status"];
            $result['info'] = $run_result["info"];
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = $run_result["info"];
        $result['data'] = $run_result["data"];
        return json($result);
    }
}