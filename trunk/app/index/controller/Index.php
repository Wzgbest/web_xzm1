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
use app\common\model\Meme;
use app\common\model\RoleRule;

class Index extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        $userinfo = get_userinfo();
        $this->assign("userinfo",$userinfo);
        $menus = false;//get_cache_by_tel($this->telephone,"menus");
        if(!$menus){
            $roleRuleM = new RoleRule($this->corp_id);
            $menus = $roleRuleM->getMenusByUid($this->uid);
            set_cache_by_tel($this->telephone,"menus",$menus,600);
        }
        //var_exp($menus,'$menus');
        $menu_idx = [];
        foreach ($menus as $menu){
            if($menu["type"]==0){
                $menu_idx[$menu["id"]] = $menu;
            }
        }
        foreach ($menus as $menu){
            if($menu["type"]!=0 && isset($menu_idx[$menu["pid"]])){
                $menu_idx[$menu["pid"]]["child"][] = $menu;
            }
        }
        //var_exp($menu_idx,'$menu_idx',1);
        $this->assign("menu_idx",$menu_idx);
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

    /**
     * @param $data
     * @return int|string
     * 批量添加表情
     */
    public function addMutipleMeme($data){
        $memeModel=new Meme($this->corp_id);
        $result=false;
        if($data) {
            $result=$memeModel->addMutipleMeme($data);
        }
        return $result;
    }

    /**
     * @param $data
     * @return bool|int|string
     * 添加单个表情
     */
    public function addSingleMeme($data){
        $memeModel=new Meme($this->corp_id);
        $result=false;
        if($data) {
            $result=$memeModel->addSingleMeme($data);
        }
        return $result;
    }

    /**
     * @param $data
     * @return array
     * 通过筛选条件查询表情路径
     */
    public function getMemePath($data){
        $memeModel=new Meme($this->corp_id);
        if(empty($data)){
            return [];
        }
        $result=$memeModel->getMemePath($data);
        return $result;
    }

    /**
     * @return false|\PDOStatement|string|\think\Collection
     * 全部表情
     */
    public function getAllMemeData(){
        $memeModel=new Meme($this->corp_id);
        $result=$memeModel->getAllMemeData();
        return $result;
    }
}
