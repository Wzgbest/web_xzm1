<?php
/**
 * User: blu10ph
 */
namespace workflow;
use workflow\db\WorkflowDB;
class WorkflowManage{
    protected $DBillage;
    protected $flow_id = 0;
    protected $form = array();
    protected $node = array();
    protected $next_idx = array();
    protected $previous_idx = array();
    public function __construct(WorkflowDB $DBillage){
        $this->DBillage = $DBillage;
    }
    public function init($flow_id){
        $this->flow_id = $flow_id;
        $this->form = $this->DBillage->getWorkFlowForm($flow_id);
        $this->node = $this->DBillage->getWorkFlowItem($flow_id);
        $this->next_idx = [];
        $this->previous_idx = [];
    }
    function initByXml($xml){
        var_exp($xml,'$xml');
        $xmlstr = preg_replace('/\sxmlns="(.*?)"/', ' _xmlns="${1}"', $xml);
        $xmlstr = preg_replace('/<(\/)?(\w+):(\w+)/', '<${1}${2}_${3}', $xmlstr);
        $xmlstr = preg_replace('/(\w+):(\w+)="(.*?)"/', '${1}_${2}="${3}"', $xmlstr);
//        var_exp($xmlstr,'$xmlstr');
        $xml_obj = simplexml_load_string($xmlstr,null,LIBXML_NOCDATA);
//        var_exp($xml_obj,'$xml_obj');
        $json_str = json_encode($xml_obj,true);
//        var_exp($json_str,'$json_str');
        $json_obj = json_decode($json_str,true);
//        var_exp($json_obj,'$json_obj');

        if(!isset($json_obj["process"]["@attributes"])){
            return;
        }

        var_exp($json_obj["process"]["@attributes"]["name"],'$json_obj["process"]["@attributes"]["name"]');

        $index = [];
        $next_idx = [];
        $previous_idx = [];
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
        var_exp($item_type_enum,'$item_type_enum');
        foreach ($item_type_enum as $item_type_idx=>$item_type){
            if(!isset($json_obj["process"][$item_type])){
                continue;
            }
            if(isset($json_obj["process"][$item_type]["@attributes"])){
                $flow_item = $json_obj["process"][$item_type]["@attributes"];
                $flow_item["type"] = $item_type;
                $index[$flow_item["id"]] = $flow_item;
                if($item_type_idx==1){
                    $next_idx[$flow_item["id"]] = $flow_item["targetRef"];
                    $previous_idx[$flow_item["id"]] = $flow_item["sourceRef"];
                }
            }else{
                foreach ($json_obj["process"][$item_type] as $flow_item){
                    $flow_item["@attributes"]["type"] = $item_type;
                    $index[$flow_item["@attributes"]["id"]] = $flow_item["@attributes"];
                    if($item_type_idx==1){
                        $next_idx[$flow_item["@attributes"]["id"]] = $flow_item["@attributes"]["targetRef"];
                        $previous_idx[$flow_item["@attributes"]["id"]] = $flow_item["@attributes"]["sourceRef"];
                    }
                }
            }
        }

        var_exp($index,'$index');
        var_exp($next_idx,'$next_idx');
        var_exp($previous_idx,'$previous_idx');

        $this->node = $next_idx;
    }
}