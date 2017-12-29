<?php
/**
 * User: blu10ph
 */
namespace workflow\db;
use think\Db;
Class WorkflowDBTp5 extends WorkflowDB {
    //默认配置
    protected $_config = array(
        'FLOW_DEFAULT' => 'workflow_default',               // 流程定义
        'FLOW_PROCESS' => 'workflow_process',       // 流程进程
        'FLOW_ACTION' => 'workflow_process_action', // 流程过程
    );
    protected $model;
    protected $link;
    public function __construct($corp_id){
        $prefix = config('db_config1.prefix');
        foreach ($this->_config as &$config_item){
            $config_item = $prefix . $config_item;
        }
        if (config('WORKFLOW_CONFIG')) {
            //可设置配置项 AUTH_CONFIG, 此配置项为数组。
            $this->_config = array_merge($this->_config, config('WORKFLOW_CONFIG'));
        }
        config('db_config1.database', config('db_common_prefix') . $corp_id);
        $this->model = Db::connect('db_config1');
        $this->link =$this->model->getConnection();
    }
    public function startTrans(){
        $this->link->startTrans();
    }
    public function commit(){
        $this->link->commit();
    }
    public function rollback(){
        $this->link->rollback();
    }

    public function getWorkFlowDefault($flow_id){
        $map["wf.id"] = $flow_id;
        $field = [
            'wf.id',
            'wf.workflow_version',
            'wf.workflow_name',
            'wf.workflow_title',
            'wf.workflow_file',
            'wf.is_publish',
            'wf.is_show',
            'wf.create_time',
            'wf.update_time',
        ];
        return $this->model->table($this->_config['FLOW_DEFAULT'])->alias('wf')
            ->where($map)
            ->field($field)
            ->find();
    }
    public function getWorkFlowDefaultByNameAndVersion($flow_name,$version){}
    public function addWorkFlowDefault($flow_id,$workflow){}

    public function getProcess($process_id){
        $map["wfp.id"] = $process_id;
        $field = [
            'wfp.id',
            'wfp.process_title',
            'wfp.process_flow_id',
            'wfp.process_flow_name',
            'wfp.process_flow_item_now',
            'wfp.process_circle',
            'wfp.process_status',
        ];
        return $this->model->table($this->_config['FLOW_PROCESS'])->alias('wfp')
            ->where($map)
            ->field($field)
            ->find();
    }
    public function setProcess($process_id,$process){
        return $this->model->table($this->_config['FLOW_PROCESS'])
            ->where('id',$process_id)
            ->update($process);
    }
    public function addProcess($process){
        return $this->model->table($this->_config['FLOW_PROCESS'])
            ->insertGetId($process);
    }

    public function getProcessAction($process_id){
        $map["wfpa.process_id"] = $process_id;
        $field = [
            'wfpa.id',
            'wfpa.process_id',
            'wfpa.action_flow_item_id',
            'wfpa.action_content',
            'wfpa.action_remark',
            'wfpa.action_circle',
            'wfpa.action_auditor_type',
            'wfpa.action_auditor',
            'wfpa.action_user',
            'wfpa.create_time',
            'wfpa.update_time',
        ];
        $order = "wfpa.id desc";
        return $this->model->table($this->_config['FLOW_ACTION'])->alias('wfpa')
            ->where($map)
            ->field($field)
            ->order($order)
            ->select();
    }
    public function setProcessAction($process_action_id,$process_action){
        return $this->model->table($this->_config['FLOW_ACTION'])
            ->where('id',$process_action_id)
            ->update($process_action);
    }
    public function addProcessActionList($process_action_list){
        return $this->model->table($this->_config['FLOW_ACTION'])
            ->insertAll($process_action_list);
    }
    public function addProcessAction($process_action_list){
        return $this->model->table($this->_config['FLOW_ACTION'])
            ->insertGetId($process_action_list);
    }
}