<?php
namespace app\crm\controller;

class Contract{
    public function index(){
        return view();
    }
    public function contract_apply(){
        return view();
    }
    public function apply(){
        $result = ['status'=>0 ,'info'=>"申请合同时发生错误！"];
        $result['status']=1;
        $result['info']='申请合同开发中!';
        return $result;
    }
    public function retract(){
        $result = ['status'=>0 ,'info'=>"撤回合同申请时发生错误！"];
        $result['status']=1;
        $result['info']='撤回合同申请开发中!';
        return $result;
    }
    public function approved(){
        $result = ['status'=>0 ,'info'=>"通过合同申请时发生错误！"];
        $result['status']=1;
        $result['info']='通过合同申请开发中!';
        return $result;
    }
    public function rejected(){
        $result = ['status'=>0 ,'info'=>"驳回合同申请时发生错误！"];
        $result['status']=1;
        $result['info']='驳回合同申请开发中!';
        return $result;
    }
}