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
use app\index\service\TQCallApi;

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

    public function call(){
        $call_config = get_cache_by_tel($this->telephone,"call_config");
        if(!$call_config){
            $tq_config = config('tq');
            $call_config["appid"] = $tq_config["appid"];
            $call_config["appkey"] = $tq_config["appkey"];
            $call_config["secretkey"] = strtoupper(md5($tq_config["appid"]."*(**)*".$tq_config["appkey"]));
            $call_config["admin_uin"] = "9796221";
            $call_config["uin"] = "9796249";
            $call_config["strid"] = "sdzhcs2";
            $call_config["time"] = time();
            $tqCallApi = new TQCallApi();
            $access_token_data = $tqCallApi->get_access_token($call_config["admin_uin"],$call_config["uin"],$call_config["strid"],$call_config["time"]);
//            var_exp($access_token_data,'$access_token_data',1);
            if(isset($access_token_data["errcode"])&&$access_token_data["errcode"]==0){
                $call_config["access_token"] = $access_token_data["access_token"];
                set_cache_by_tel($this->telephone,"call_config",$call_config,$tq_config["expire"]?:null);
            }
        }
        if(!$call_config){
            $this->error("电话参数获取失败!");
        }
        $this->assign("call_config",$call_config);
        return view();
    }

    public function developing(){
        return view();
    }

    public function echo_one(){
        return '1';
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
