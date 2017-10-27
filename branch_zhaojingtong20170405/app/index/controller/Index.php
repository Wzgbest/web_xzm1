<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\index\controller;

use app\common\controller\Initialize;
use think\Db;
use think\Controller;
use app\common\model\Employee;
use app\common\model\StructureEmployee;

class Index extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        $userinfo = get_userinfo();
        if (empty($userinfo)) {
            $this->redirect('/login/index/index');
        }
        $this->assign("userinfo",$userinfo);
        return view();
    }

    public function map(){
        return view();
    }

    public function select_window(){
        $structureEmployeeModel = new StructureEmployee($this->corp_id);
        $structures = $structureEmployeeModel->getAllStructureAndEmployee();
        $structure_employee = [];
        $structure_list = [];
        foreach ($structures as &$structure){
            $structure["employee_ids_arr"] = explode(",",$structure["employee_ids"]);
            $structure_employee[$structure["id"]] = explode(",",$structure["employee_ids"]);
            $structure_list[$structure["id"]] = ["pid"=>$structure["struct_pid"],"name"=>$structure["struct_name"]];
        }
        $employM = new Employee($this->corp_id);
        $friendsInfos = $employM->getAllUsers();
        $employees = [];
        $employee_name = [];
        foreach ($friendsInfos as $friendsInfo){
            $employees[$friendsInfo["id"]] = $friendsInfo;
            $employee_name[$friendsInfo["id"]] = $friendsInfo["nickname"];
        }
        $this->assign("structures",$structures);
        $this->assign("employees",$employees);
        $this->assign("structure_employee",json_encode($structure_employee,true));
        $this->assign("structure_list",json_encode($structure_list,true));
        $this->assign("employee_name",json_encode($employee_name,true));
        return view();
    }

    public function developing(){
        return view();
    }
}
