<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\datacount\controller;

use app\common\controller\Initialize;
use app\common\model\StructureEmployee;
use app\common\model\EmployeeScore;

class Index extends Initialize{
    public function __construct(){
        parent::__construct();
    }
    public function index(){
        return 'Index/index';
    }
    public function test(){
        return view();
    }
    public function summary(){
        $userinfo = get_userinfo();
        if (empty($userinfo)) {
            $this->redirect('/login/index/index');
        }
//        var_exp($userinfo,'$userinfo');
        $this->assign("userinfo",$userinfo);
        $role_arr = explode(",",$userinfo["role"]);
//        var_exp($role_arr,'$role_arr');
        $role_last = array_pop($role_arr);
        $role_last = $role_last?:"";
        $this->assign("role",$role_last);
        $structureEmployeeModel = new StructureEmployee($this->corp_id);
        $structure = $structureEmployeeModel->findEmployeeStructure($this->uid);
//        var_exp($structure,'$structure');
        $this->assign("structure",$structure);
        //获取用户积分
        $scoreM = new EmployeeScore($this->corp_id);
        $score=$scoreM->getEmployeeScore($this->uid);
//        var_exp($score,'$score');
        if(!$score){
            $start = config('experience.start');
            $score = [
                "score"=>0,
                "experience"=>0,
                "title"=>"",
                "level"=>"1",
                "experience_min"=>"0",
                "experience_max"=>$start,
                "phone_time"=>0
            ];
        }
        //积分占比
        $per = round(($score["experience"]-$score["experience_min"])/($score["experience_max"]-$score["experience_min"])*100);
        if($per>100){
            $per = 100;
        }
//        var_exp($per,'$per');
        $this->assign("score",$score);
        $this->assign("score_per",$per);
//        $role_flg = check_auth("datacount/index/summary");
//        var_exp($role_flg,'$role_flg',1);
//        if(!$role_flg){
//            $this->error("没有权限查看数据简报!");
//        }
        return view();
    }
}
