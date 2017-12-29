<?php
/**
 * User: blu10ph
 */
namespace workflow\file;
use think\File;
class WorkflowFileTp5 extends WorkflowFile{
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
        $this->initByXml();
    }
    protected function initByXml(){
//        var_exp($this->file_content,'$this->file_content');
        $xmlstr = preg_replace('/\sxmlns="(.*?)"/', ' _xmlns="${1}"', $this->file_content);
        $xmlstr = preg_replace('/<(\/)?(\w+):(\w+)/', '<${1}${2}_${3}', $xmlstr);
        $xmlstr = preg_replace('/(\w+):(\w+)="(.*?)"/', '${1}_${2}="${3}"', $xmlstr);
//        var_exp($xmlstr,'$xmlstr');
        $xml_obj = simplexml_load_string($xmlstr,null,LIBXML_NOCDATA);
//        var_exp($xml_obj,'$xml_obj');
        $json_str = json_encode($xml_obj,true);
//        var_exp($json_str,'$json_str');
        $json_obj = json_decode($json_str,true);
        //unset($json_obj["bpmndi_BPMNDiagram"]);
//        var_exp($json_obj,'$json_obj');

        if(!isset($json_obj["@attributes"])){
            var_exp("!!",'!!');
            return;
        }

//        var_exp($json_obj["@attributes"]["name"],'$json_obj["@attributes"]["name"]');

        $flow_item_list = [];
//        $index = [];
//        $next_idx = [];
//        $previous_idx = [];
        $item_type_enum = [
            0=>"startEvent",
            1=>"sequenceFlow",
            2=>"userTask",
            3=>"serviceTask",
            4=>"endEvent",
            5=>"exclusiveGateway",
            6=>"parallelGateWay",
            7=>"receiveTask"
        ];
//        var_exp($item_type_enum,'$item_type_enum');
        foreach ($item_type_enum as $item_type_idx=>$item_type){
            if(!isset($json_obj[$item_type])){
                continue;
            }
            if(isset($json_obj[$item_type]["@attributes"])){
                $flow_item_attributes = $json_obj[$item_type]["@attributes"];
                $flow_item_attributes["type"] = $item_type;
                $flow_item_attributes["type_id"] = $item_type_idx;
//                $index[$flow_item_attributes["id"]] = $flow_item_attributes;
                $flow_item_tmp = $this->getItem($flow_item_attributes);
                $flow_item_list[] = $flow_item_tmp;
//                if($item_type_idx==1){
//                    $next_idx[$flow_item_attributes["id"]] = $flow_item_attributes["targetRef"];
//                    $previous_idx[$flow_item_attributes["id"]] = $flow_item_attributes["sourceRef"];
//                }
            }else{
                foreach ($json_obj[$item_type] as $flow_item){
                    $flow_item_attributes = $flow_item["@attributes"];
                    $flow_item_attributes["type"] = $item_type;
                    $flow_item_attributes["type_id"] = $item_type_idx;
//                    $index[$flow_item_attributes["id"]] = $flow_item_attributes;
                    $flow_item_tmp = $this->getItem($flow_item_attributes);
                    $flow_item_list[] = $flow_item_tmp;
//                    if($item_type_idx==1){
//                        $next_idx[$flow_item_attributes["id"]] = $flow_item_attributes["targetRef"];
//                        $previous_idx[$flow_item_attributes["id"]] = $flow_item_attributes["sourceRef"];
//                    }
                }
            }
        }

//        var_exp($flow_item_list,'$flow_item_list');
//        var_exp($index,'$index');
//        var_exp($next_idx,'$next_idx');
//        var_exp($previous_idx,'$previous_idx');
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