<?php
/**
 * User: blu10ph
 */
namespace workflow;
use workflow\db\WorkflowDB;
use workflow\file\WorkflowFile;
class WorkflowEngine{
    protected $file;
    protected $DBModel;
    protected $workflow_default = [];
    protected $node = [];
    protected $sequence_flow = [];
    protected $start_node_id = 0;
    protected $next_idx = [];
    protected $previous_idx = [];
    protected $process = [];
    protected $processAction = [];
    protected $processData = [];
    protected $condition_pattern = "/(\\$)([a-zA-Z\\._]+)(==|<=|>=|<|>|!=)(\\'(\\S+)\\'|(\\d+))/";
    public function __construct(WorkflowDB $DBModel=null,WorkflowFile $file=null){
        $this->DBModel = $DBModel;
        $this->file = $file;
    }
    public function setDBModel(WorkflowDB $DBModel){
        $this->DBModel = $DBModel;
    }
    public function setFile(WorkflowFile $file){
        $this->file = $file;
    }
    public function init($flow_id){
        $result = ['status'=>0 ,'info'=>"加载流程时发生错误！"];
        if(!empty($this->workflow_default)){
            $result['info'] = "已加载流程！";
            return $result;
        }
        $this->workflow_default = $this->DBModel->getWorkFlowDefault($flow_id);
//        var_exp($this->workflow_default,'$this->workflow_default');
        if(!$this->workflow_default){
            $result['info'] = "未找到流程定义！";
            return $result;
        }

        $this->file->loadFile($this->workflow_default["workflow_file"]);

        $flow_item_list = $this->file->getWorkFlowItem();
//        //var_exp($flow_item_list,'$flow_item_list');
        foreach ($flow_item_list as $flow_item){
            if($flow_item["flow_item_type"]==1){
                $this->sequence_flow[$flow_item["id"]] = $flow_item;
                $this->previous_idx[$flow_item["id"]] = $flow_item["flow_item_previous_id"];
                $this->next_idx[$flow_item["id"]] = $flow_item["flow_item_next_id"];
            }else{
                $this->node[$flow_item["id"]] = $flow_item;
            }
            if($this->start_node_id==0 && $flow_item["flow_item_type"]==0){
                $this->start_node_id = $flow_item["id"];
            }
        }
//        //var_exp($this->node,'$this->node');
        //var_exp($this->start_node_id,'$this->start_node_id');
        $previous_idx = $this->previous_idx;
        $next_idx = $this->next_idx;
        foreach ($this->previous_idx as $node_id=>$previous_id){
            $next_idx[$previous_id][] = $node_id;
        }
        foreach ($this->next_idx as $node_id=>$next_id){
            $previous_idx[$next_id][] = $node_id;
        }
        $this->previous_idx = $previous_idx;
        $this->next_idx = $next_idx;
//        //var_exp($this->previous_idx,'$this->previous_idx');
//        //var_exp($this->next_idx,'$this->next_idx');

        $result['status'] = 1;
        $result['info'] = "加载流程成功！";
        return $result;
    }
    public function getWorkflowDefault(){
        return $this->workflow_default;
    }
    public function loadProcess($process_id){
        $result = ['status'=>0 ,'info'=>"加载进程时发生错误！"];
        if(!empty($this->process)){
            $result['info'] = "已加载进程！";
            return $result;
        }
        $this->process = $this->DBModel->getProcess($process_id);
        if(!$this->process){
            $result['info'] = "未找到进程！";
            return $result;
        }
        //var_exp($this->process,'$this->process');

        if(empty($this->workflow_default)||$this->workflow_default["id"]!=$this->process["process_flow_id"]){
            $init_result = $this->init($this->process["process_flow_id"]);
            if($init_result["status"]!=1){
                $result['status'] = $init_result["status"];
                $result['info'] = $init_result["info"];
                return $result;
            }
        }

        $this->processAction = $this->DBModel->getProcessAction($process_id);
//        //var_exp($this->processAction,'$this->processAction');

        $result['status'] = 1;
        $result['info'] = "加载进程成功！";
        return $result;
    }
    public function getCreateNextItem(){
        $nextNodeSequenceFlowArr = $this->getNextNodeSequenceFlows($this->start_node_id);
        //var_exp($nextNodeSequenceFlowArr, '$nextNodeSequenceFlowArr');
        $nextNodeSequenceFlowId = $nextNodeSequenceFlowArr[0];
        //var_exp($nextNodeSequenceFlowId, '$nextNodeSequenceFlowId');
        $nextNodeId = $this->getNextNodeId($nextNodeSequenceFlowId);
        $nextNode = $this->getNode($nextNodeId);
        //var_exp($nextNode, '$nextNode');
        $item_arr[] = ["title"=>$nextNode["flow_item_title"],"name"=>$nextNode["id"],"value"=>$nextNode["flow_item_title"]];
        return $item_arr;
    }
    public function createProcess($process_title,$create_user,$input){
        $result = ['status'=>0 ,'info'=>"新建进程时发生错误！"];
        if(!empty($this->process)){
            $result['info'] = "已加载进程！";
            return $result;
        }
        $time = time();
        //var_exp($input, 'createProcess:$input');

        $firstNodeSequenceFlowArr = $this->getNextNodeSequenceFlows($this->start_node_id);
//        //var_exp($firstNodeSequenceFlowArr, '$firstNodeSequenceFlowArr');
        $firstNodeSequenceFlowId = $firstNodeSequenceFlowArr[0];
//        //var_exp($firstNodeSequenceFlowId, '$firstNodeSequenceFlowId');
        $firstNodeId = $this->getNextNodeId($firstNodeSequenceFlowId);
        $firstNode = $this->getNode($firstNodeId);
//        //var_exp($firstNode, 'createProcess:$firstNode');

        $nextNodeSequenceFlowArr = $this->getNextNodeSequenceFlows($firstNodeId);
//        //var_exp($nextNodeSequenceFlowArr, '$nextNodeSequenceFlowArr');
        $nextNodeSequenceFlowId = $nextNodeSequenceFlowArr[0];
//        //var_exp($nextNodeSequenceFlowId, '$nextNodeSequenceFlowId');
        $nextNodeId = $this->getNextNodeId($nextNodeSequenceFlowId);
        $nextNode = $this->getNode($nextNodeId);
//        //var_exp($nextNode, 'createProcess:$nextNode');

        $process_action_list = [];

        $process_action["action_flow_item_id"] = $firstNode['id'];
        $process_action["action_content"] = $this->getValue4Input($firstNode["id"],$input);
        $process_action["action_remark"] = $this->getValue4Input("remark",$input);
        $process_action["action_circle"] = 1;
        $process_action["action_auditor_type"] = $firstNode['flow_item_auditor_type'];
        $process_action["action_auditor"] = $firstNode['flow_item_auditor'];
        $process_action["action_user"] = $this->getValue4Input("uid",$input,0);
        $process_action["create_time"] = $time;
        $process_action["update_time"] = $time;
        $process_action_list[] = $process_action;

        $process_action["action_flow_item_id"] = $nextNode['id'];
        $process_action["action_content"] = '';
        $process_action["action_remark"] = '';
        $process_action["action_circle"] = 1;
        $process_action["action_auditor_type"] = $nextNode['flow_item_auditor_type'];
        $process_action["action_auditor"] = $nextNode['flow_item_auditor'];
        $process_action["action_user"] = 0;
        $process_action["create_time"] = $time;
        $process_action["update_time"] = $time;
        $process_action_list[] = $process_action;

        $process["process_title"] = $process_title;
        $process["process_flow_id"] = $this->workflow_default["id"];
        $process["process_flow_name"] = $this->workflow_default["workflow_name"];
        $process["process_flow_item_now"] = $nextNode['id'];
        $process["process_circle"] = 1;
        $process["process_status"] = 2;
        $process["create_user"] = $create_user;
        $process["create_time"] = $time;
        $process["update_time"] = $time;
        try{
            $this->DBModel->startTrans();
            //var_exp($process, '$process');
            $process["id"] = $this->DBModel->addProcess($process);
            //var_exp($process["id"], '$process["id"]');
            if(!$process["id"]){
                $result['info'] = "保存进程出现错误！";
                exception('保存进程出现错误!');
            }
            foreach ($process_action_list as &$process_action){
                $process_action["process_id"] = $process["id"];
            }
            //var_exp($process_action_list,'$process_action_list');
            $process_data_num = $this->DBModel->addProcessActionList($process_action_list);
            //var_exp($process_data_num,'$process_data_num');
            if(!$process_data_num){
                $result['info'] = "保存进程过程出现错误！";
                exception('保存进程过程出现错误!');
            }

            $this->DBModel->commit();
        }catch (\Exception $ex){
            $this->DBModel->rollback();
            return $result;
        }
        $this->process = $process;
        //var_exp($this->process,'$this->process');
//        foreach ($process_action_list as $process_action){
//            $this->addAction($process_action);
//        }
        $this->processAction = $this->DBModel->getProcessAction($this->process["id"]);
//        //var_exp($this->processAction,'$this->processAction');
        $result['status'] = 1;
        $result['info'] = "新建进程成功！";
        $result['data'] = $process["id"];
        return $result;
    }
    public function getProcess(){
        return $this->process;
    }
    public function getProcessAction(){
        return $this->processAction;
    }
    public function getNextItem(){
        $nowNode = $this->getNowNode();
        $nextNodeSequenceFlowArr = $this->getNextNodeSequenceFlows($nowNode["id"]);
        //var_exp($nextNodeSequenceFlowArr, '$nextNodeSequenceFlowArr');
        $item_arr = [];
        if(count($nextNodeSequenceFlowArr)==1){
            $sequence_flow_id = $nextNodeSequenceFlowArr[0];
            //var_exp($sequence_flow_id, '$sequence_flow_id');
            $sequenceFlow = $this->getSequenceFlow($sequence_flow_id);
            //var_exp($sequenceFlow, 'getNextItem:$sequenceFlow');
            $nextNodeId = $this->getNextNodeId($sequenceFlow["id"]);
            $nextNode = $this->getNode($nextNodeId);
            $title = $sequenceFlow["flow_item_title"]?:$nextNode["flow_item_title"];
            $item_arr[] = ["title"=>$title,"name"=>$sequenceFlow["id"],"value"=>$title];
        }else{
            foreach ($nextNodeSequenceFlowArr as $sequence_flow_id) {
                $sequenceFlow = $this->getSequenceFlow($sequence_flow_id);
                //var_exp($sequenceFlow, 'getNextItem:$sequenceFlow');
                $condition = $sequenceFlow["flow_item_next_condition"];
                preg_match($this->condition_pattern, $condition, $match);
                $count = count($match);
                if($count>4){
                    $item_arr[] = ["title"=>$match[$count-1],"name"=>$match[2],"value"=>$match[$count-1]];
                }
            }
        }
        return $item_arr;
    }
    function run($input){
        $result = ['status'=>0 ,'info'=>"执行流程进程时发生错误！"];
        if(empty($this->process)){
            $result['info'] = "未加载进程！";
            return $result;
        }
//        //var_exp($this->process,'$this->process');
        if(!in_array($this->process["process_status"],[1,2])){
            $result['info'] = "进程不能运行！";
            return $result;
        }
        $can_wait_flg = 0;
        $run_flg = 1;
        do{
            $lastAction = $this->getLastAction();
            //var_exp($lastAction,'$lastAction');
            $nowNode = $this->getNode($lastAction['action_flow_item_id']);
//            var_exp($nowNode,'$nowNode');
            $nextNodeSequenceFlowId = 0;
            $nextNodeId = 0;
            Switch($nowNode['flow_item_type']){
                Case 2: // userTask 人工决策
                    if($can_wait_flg==1){
                        $run_flg = 0;
                        break;
                    }
                    $can_wait_flg = 1;
                Case 3: // serviceTask 自动处理
                Case 5: // exclusiveGateway 排他网关
                Case 0: // startEvent 开始
                    $nextNodeSequenceFlowArr = $this->getNextNodeSequenceFlows($nowNode["id"]);
//                    var_exp($nextNodeSequenceFlowArr, '$nextNodeSequenceFlowArr');

                    $nextSequenceFlow = [];
                    if(count($nextNodeSequenceFlowArr)==1){
                        $nextNodeSequenceFlowId = $nextNodeSequenceFlowArr[0];
                        $nextSequenceFlow = $this->getSequenceFlow($nextNodeSequenceFlowId);
                    }else{
                        foreach ($nextNodeSequenceFlowArr as $sequence_flow_id) {
                            $nextSequenceFlow = $this->getSequenceFlow($sequence_flow_id);
//                            var_exp($nextSequenceFlow,'$nextSequenceFlow');
                            if ($this->checkRule($nextSequenceFlow["flow_item_next_condition"], $input)) {
                                $nextNodeSequenceFlowId = $sequence_flow_id;
                                break;
                            }
                        }
                    }
                    //var_exp($nextNodeSequenceFlowId, '$nextNodeSequenceFlowId');
                    $nextNodeId = $this->getNextNodeId($nextNodeSequenceFlowId);
//                    //var_exp($nextNodeId, '$nextNodeId');
                    if (!$nextNodeId) {
                        $result['info'] = "未找到下一流程节点!";
                        return $result;
                    }
                    //var_exp($nextNodeId, '$nextNodeId');
                    $run2_result = $this->run2Node($nextNodeId, $input, $nextSequenceFlow);
                    //var_exp($run2_result, '$run2_result');
                    if ($run2_result["status"] != 1) {
                        $result['status'] = $run2_result["status"];
                        $result['info'] = $run2_result["info"];
                        return $result;
                    }
                    break;
                Case 4: // endEvent 结束
                    $run_flg = 0;
                    //var_exp("流程结束!", 'endEvent');
                    break;
                default:
                    //var_exp($nowNode['flow_item_type'],'switch:default',1);
                    break;
            }
            //var_exp($nextNodeId,'out_switch:$nextNodeId');
            $nextNode = $this->getNode($nextNodeId);
            $result['data'] = $nextNode;
        }while ($run_flg);
        $result['status'] = 1;
        $result['info'] = "执行流程进程成功！";
        return $result;
    }
    function run2Node($node_id,$input=[],$sequenceFlow,$circle=0,$status=0){
        $result = ['status'=>0 ,'info'=>"执行流程进程到节点时发生错误！"];

//        var_exp($sequenceFlow,'$sequenceFlow');
        $lastAction = $this->getLastAction();
        $now_action_id = $lastAction["id"];
//        var_exp($now_action_id,'$now_action_id');
        $nowNode = $this->getNode($lastAction["action_flow_item_id"]);
//        var_exp($nowNode,'$nowNode');
        $nextNode = $this->getNode($node_id);
//        var_exp($nextNode,'$nextNode');
        $time = time();

        $content = false;
        $match = $this->getRule($sequenceFlow["flow_item_next_condition"]);
        if(isset($match[2])){
            $content = $match[2];
        }
        $content = $this->getValue4Input($content?:'content',$input);
        $remark = $this->getValue4Input("remark",$input);
        if($nowNode["flow_item_type"]==2){
            if(empty($content)){
                $content = $sequenceFlow["flow_item_title"];
            }
            if(empty($content)){
                $content = $nowNode["flow_item_title"];
            }
        }elseif($nowNode["flow_item_type"]==3){
            $content = "系统自动流转";
            $remark = $nowNode["flow_item_title"];
        }elseif($nowNode["flow_item_type"]==5){
            $content = "系统自动处理";
//            $remark = $match[2].$match[3].$match[4];
            if(empty($sequenceFlow["flow_item_next_condition"])){
                $remark = $sequenceFlow["flow_item_title"];
            }elseif(isset($match[2])){
                $remark = $match[2]."=".$content.",下一步:".$nextNode["flow_item_title"];
            }else{
                $remark = "下一步:".$nextNode["flow_item_title"];
            }
        }
        $update_action["action_user"] = $this->getValue4Input("uid",$input,0);
        $update_action["action_content"] = $content;
        $update_action["action_remark"] = $remark;
        $update_action["update_time"] = $time;

        $tmp_action["process_id"] = $this->process["id"];
        $tmp_action["action_flow_item_id"] = $node_id;
        $tmp_action["action_content"] = '';
        $tmp_action["action_remark"] = '';
        $tmp_action["action_circle"] = $circle?:$this->process["process_circle"];
        $tmp_action["action_auditor_type"] = $nextNode['flow_item_auditor_type']?:0;
        $tmp_action["action_auditor"] = $nextNode['flow_item_auditor'];
        $tmp_action["action_user"] = 0;
        $tmp_action["create_time"] = $time;
        $tmp_action["update_time"] = $time;
//        var_exp($tmp_action,'$tmp_action');

        $tmp_process["process_flow_item_now"] = $node_id;
        $tmp_process["update_time"] = $time;
        if($circle){
            $tmp_process["process_circle"] = $circle;
        }
        if($status){
            $tmp_process["process_status"] = $status;
        }
//        var_exp($tmp_process,'$tmp_process');

        try{
            $this->DBModel->startTrans();
//            var_exp($this->process,'$this->process');
            $process_flg = $this->DBModel->setProcess($this->process["id"],$tmp_process);
//            var_exp($process_flg,'$process_flg');
            if(!$process_flg){
                $result['info'] = "更新进程出现错误！";
                exception('更新进程出现错误!');
            }

//            var_exp($update_action,'$update_action');
            $process_action_update_num = $this->DBModel->setProcessAction($now_action_id,$update_action);
//            var_exp($process_action_update_num,'$process_action_update_num');
            if(!$process_action_update_num){
                $result['info'] = "更新进程过程出现错误！";
                exception('更新进程过程出现错误!');
            }

//            var_exp($tmp_action,'$tmp_action');
            $process_action_add_flg = $this->DBModel->addProcessAction($tmp_action);
//            var_exp($process_action_add_flg,'$process_action_add_flg');
            if(!$process_action_add_flg){
                $result['info'] = "添加进程过程出现错误！";
                exception('添加进程过程出现错误!');
            }
            $tmp_action["id"] = $process_action_add_flg;

            if($nowNode["flow_item_type"]==3){
                $delegate_result["status"] = 0;
                $callback = false;
                $param_arr = [];
                if(method_exists($this,$nowNode["flow_item_next_delegate"])){
                    $callback = [$this,$nowNode["flow_item_next_delegate"]];
                }elseif(function_exists($nowNode["flow_item_next_delegate"])){
                    $callback = $nowNode["flow_item_next_delegate"];
                }
                if($callback){
                    $delegate_result = call_user_func_array($callback,$param_arr);
//                    var_exp($delegate_result,'$delegate_result');
                    if(!($delegate_result||$delegate_result["status"]==1)){
                        if(isset($delegate_result["status"])){
                            $result['status'] = $delegate_result["status"];
                            $result['info'] = $delegate_result["info"];
                        }else{
                            $result['info'] = "调用系统方法出现错误！";
                        }
                        exception('调用系统方法出现错误!');
                    }
                }
            }
            $this->DBModel->commit();
        }catch (\Exception $ex){
            $this->DBModel->rollback();
//            $result["info"] = $ex->getMessage();
            return $result;
        }
        foreach ($tmp_process as $k=>$v){
            $this->process[$k] = $v;
        }
        foreach ($update_action as $k=>$v){
            $this->processAction[0][$k] = $v;
        }
        $this->addAction($tmp_action);

        $result['status'] = 1;
        $result['info'] = "执行流程进程到节点成功！";
        return $result;
    }
    function updateFailStatusService(){
        $result = ['status'=>0 ,'info'=>"更新流程进程状态出现错误！"];
        $tmp_process["process_status"] = 4;
        $process_flg = $this->DBModel->setProcess($this->process["id"],$tmp_process);
        if(!$process_flg){
            return $result;
        }
        $this->process["process_status"] = 4;
        $result['status'] = 1;
        $result['info'] = "更新流程进程状态成功！";
        return $result;
    }
    function updateSuccessStatusService(){
        $result = ['status'=>0 ,'info'=>"更新流程进程状态出现错误！"];
        $tmp_process["process_status"] = 3;
        $process_flg = $this->DBModel->setProcess($this->process["id"],$tmp_process);
        if(!$process_flg){
            return $result;
        }
        $this->process["process_status"] = 3;
        $result['status'] = 1;
        $result['info'] = "更新流程进程状态成功！";
        return $result;
    }
    function getValue4Input($name,$input=[],$default=''){
        $value = $default;
        if(isset($input[$name])){
            $value = $input[$name];
        }
        return $value;
    }
    function getRule($rule){
//        $rule = '$transition==6';
        $match = [];
        $match_tmp = [];
//        var_exp($rule,'$rule');
        preg_match($this->condition_pattern, $rule, $match_tmp);
        $count = count($match_tmp);
//        var_exp($count,'$count');
        if($count<5){
            return $match;
        }
        $match[0] = $match_tmp[0];
        $match[1] = $match_tmp[1];
        $match[2] = $match_tmp[2];
        $match[3] = $match_tmp[3];
        $match[4] = $match_tmp[$count-1];
        return $match;
    }
    function checkRule($rule,$input){
        $flg = false;
//        var_exp($input,'$input');
        $match = $this->getRule($rule);
//        var_exp($match,'$match');
        if(empty($match)){
            return false;
        }
        if($match[1]!="$"){
            return false;
        }
        if(!isset($input[$match[2]])){
            return false;
        }
        switch ($match[3]){
            case "==":
                $flg = $input[$match[2]] == $match[4];
                break;
            case "<=":
                $flg = $input[$match[2]] <= $match[4];
                break;
            case ">=":
                $flg = $input[$match[2]] >= $match[4];
                break;
            case "<":
                $flg = $input[$match[2]] < $match[4];
                break;
            case ">":
                $flg = $input[$match[2]] > $match[4];
                break;
            case "!=":
                $flg = $input[$match[2]] != $match[4];
                break;
        }
//        var_exp($flg,'$flg');
        return $flg;
    }
    function getNextNodeSequenceFlows($node_id=0){
        $nextNode = [];
        if(!$node_id){
            $node = $this->getNowNode();
            $node_id = $node["id"];
        }
//        //var_exp($node_id,'getNextNode:$node_id');
        if(isset($this->next_idx[$node_id])){
            $nextNode = $this->next_idx[$node_id];
        }
//        //var_exp($nextNode,'getNextNode:$nextNode');
        return $nextNode;
    }
    function getNode($node_id){
        if(isset($this->node[$node_id])){
            return $this->node[$node_id];
        }else{
            return null;
        }
    }
    function getSequenceFlow($sequenceFlow_id){
        if(isset($this->sequence_flow[$sequenceFlow_id])){
            return $this->sequence_flow[$sequenceFlow_id];
        }else{
            return null;
        }
    }
    function getNextNodeId($sequenceFlow_id){
        $nextNodeId = 0;
//        //var_exp($sequenceFlow_id,'getNextNodeId:$sequenceFlow_id');
        if(isset($this->sequence_flow[$sequenceFlow_id])){
            $nextNodeId = $this->sequence_flow[$sequenceFlow_id]["flow_item_next_id"];
        }
//        //var_exp($nextNodeId,'getNextNodeId:$nextNodeId');
        return $nextNodeId;
    }
    function getNowNode(){
        $nowNode = null;
        $lastAction = $this->getLastAction();
        $nowNode = $this->getNode($lastAction['action_flow_item_id']);
        return $nowNode;
    }
    function getLastAction(){
        $lastAction = null;
        $count = count($this->processAction);
        if($count>0){
            $lastAction = $this->processAction[0];
        }
        return $lastAction;
    }
    function addAction($action){
        $count = count($this->processAction);
        if($count>0){
            array_unshift($this->processAction,$action);
        }else{
            $this->processAction[] = $action;
        }
    }
}