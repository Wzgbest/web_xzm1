<?php
/**
 * Created by PhpStorm.
 * User: erin
 * Date: 2017/11/6
 */
namespace app\systemsetting\controller;
use app\systemsetting\model\Rule as RuleModel;

use app\common\controller\Initialize;
class Rule extends Initialize{
    public function __construct(){
        parent::__construct();
        $this->_RuleModel = new RuleModel($this->corp_id);
    }
    public function index(){
        $firstLevelData=$this->_RuleModel->getFirstLevelData();

        $root_id = 0;
        $rules = $this->_RuleModel->getAllRuleList($map=array('status'=>1),$field='*',$order="sort",$direction="asc");
        $tree = new \myvendor\Tree($rules,['id','pid']);
        $res = $tree->leaf($root_id);

        $this->assign('rule_tree',$res);
        $this->assign('firstLevelData',$firstLevelData);
        return view();
    }
    public function addRule(){
        $redata['status']=false;
        $redata['message']='操作失败';
        $data=input('post.');
        if($data){
            $result=$this->_RuleModel->addRule($data);
            if($result){
                $redata['status']=true;
                $redata['message']='操作成功';
                return json($redata);
            }
            return json($redata);
        }
        return json($redata);
    }
    public function edit(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }

        $firstLevelData=$this->_RuleModel->getFirstLevelData();

        $map['id']=$id;
        $info = $this->_RuleModel->getRuleInfo($map);

        $this->assign("id",$id);
        $this->assign("fr",input('fr'));
        $this->assign('firstLevelData',$firstLevelData);
        $this->assign("info",$info);
        return view();
    }
    public function editRule(){
        $redata['status']=false;
        $redata['message']='操作失败';
        $data=input('post.');
        if($data){
            $result=$this->_RuleModel->editRule($data);
            if($result!==false){
                $redata['status']=true;
                $redata['message']='操作成功';
                return json($redata);
            }
            return json($redata);
        }
        return json($redata);
    }
    public function delRule(){
        $redata['status']=false;
        $redata['message']='操作失败';
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }
        $map['id']=$id;
        $data['status']=0;
        $result=$this->_RuleModel->delRule($map,$data);
        if($result){
            $redata['status']=true;
            $redata['message']='操作成功';
            return json($redata);
        }
        return json($redata);

    }
}