<?php
/**
 * User: blu10ph
 */
namespace workflow\db;
abstract class WorkflowDB{
    abstract public function getWorkFlowDefault($flow_id);
    abstract public function getWorkFlowDefaultByNameAndVersion($flow_name,$version);
    abstract public function addWorkFlowDefault($flow_id,$workflow);

    abstract public function getProcess($process_id);
    abstract public function setProcess($process_id,$process);
    abstract public function addProcess($process);

    abstract public function getProcessAction($process_id);
    abstract public function setProcessAction($process_action_id,$process_action);
    abstract public function addProcessAction($process_action);
}