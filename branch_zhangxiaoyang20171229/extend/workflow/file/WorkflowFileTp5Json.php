<?php
/**
 * User: blu10ph
 */
namespace workflow\file;
use think\File;
class WorkflowFileTp5Json extends WorkflowFile{
    protected $file;
    protected $file_content;
    protected $workflow_item;
    public function __construct(){}
    public function loadFile($file_path){
        $this->file = new File($file_path);
        while (!$this->file->eof()) {
            $this->file_content .= $this->file->current();
            $this->file->next();
        }
        $this->file = null;
        $this->initByJson();
    }
    protected function initByJson(){
//        var_exp($this->file_content,'$this->file_content');
        $json_obj = json_decode($this->file_content,true);
//        var_exp($json_obj,'$json_obj');
        if(!isset($json_obj["process"])){
            var_exp("流程文件读取失败",'initByJson');
            return;
        }

        $flow_item_list = [];
        $item_type_enum_idx = [
            "startEvent"=>0,
            "sequenceFlow"=>1,
            "userTask"=>2,
            "serviceTask"=>3,
            "endEvent"=>4,
            "exclusiveGateway"=>5,
            "parallelGateWay"=>6,
            "receiveTask"=>7
        ];
//        var_exp($item_type_enum_idx,'$item_type_enum_idx');
        foreach ($json_obj["item"] as $item){
            $item["type_id"] = $item_type_enum_idx[$item["type"]];
            $flow_item_tmp = $this->getItem($item);
            $flow_item_list[] = $flow_item_tmp;
        }

//        var_exp($flow_item_list,'$flow_item_list');
        $this->workflow_item = $flow_item_list;
    }
    protected function getValue($arr,$name,$def=""){
        return isset($arr[$name])?$arr[$name]:$def;
    }
    protected function getItem($flow_item_attributes){
//        var_exp($flow_item_attributes,'$flow_item_attributes');
        $flow_item["id"] = $flow_item_attributes["id"];
        $flow_item["flow_item_title"] = $this->getValue($flow_item_attributes,"name");
        $flow_item["flow_item_type"] = $flow_item_attributes["type_id"];
        $flow_item["flow_item_form_read_only"] = $this->getValue($flow_item_attributes,"read_only");
        $flow_item["flow_item_previous_id"] = $this->getValue($flow_item_attributes,"previous_item");
        $flow_item["flow_item_next_id"] = $this->getValue($flow_item_attributes,"next_item");
        $flow_item["flow_item_next_condition"] = $this->getValue($flow_item_attributes,"condition");
        $flow_item["flow_item_next_delegate"] = $this->getValue($flow_item_attributes,"delegate");
        $flow_item["flow_item_auditor"] = $this->getValue($flow_item_attributes,"auditor");
        $flow_item["flow_item_auditor_type"] = $this->getValue($flow_item_attributes,"auditor_type");
//        var_exp($flow_item,'$flow_item');
        return $flow_item;
    }
    public function getWorkFlowItem(){
        return $this->workflow_item;
    }
}