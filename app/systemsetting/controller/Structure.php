<?php
/**
 * Created by: messhair
 * Date: 2017/5/8
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Employee as EmployeeModel;
use app\common\model\Structure as StructureModel;
use app\common\model\StructureEmployee as StructureEmployeeModel;
use app\common\model\Role as RoleModel;
use app\huanxin\service\Api as HuanxinApi;

class Structure extends Initialize
{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    /**
     * 首页显示
     * @return \think\response\View
     * created by messhair
     */
    public function index(){
        $root_id = 0;
        $struM = new StructureModel($this->corp_id);
        $structs = $struM->getAllStructure();
        $tree = new \myvendor\Tree($structs,['id','struct_pid']);
        $res = $tree->leaf($root_id);
        $this->assign('structs',$structs);
        $this->assign('struct_tree',$res);
        $this->assign('struct_json',json_encode($res));
        $this->assign('root_id',$root_id);
        return view();
    }

    public function employee_list(){
        $struct_id = input("id",0,"int");
        if(!$struct_id){
            $this->error("参数错误!");
        }
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $filter = $this->_getCustomerFilter(["role","worknum","truename"]);
        try{
            $employeeM = new EmployeeModel($this->corp_id);
            $employee_list = $employeeM->getEmployeeByStructId($struct_id,$start_num,$num,$filter);
            //var_exp($employee_list,'$employee_list',1);
            $this->assign('listdata',$employee_list);
            $employees_count = $employeeM->countEmployeeByStructId($struct_id);
            //var_exp($employees_count,'$employees_count',1);
            $this->assign("count",$employees_count);
            $rolM = new RoleModel($this->corp_id);
            $roles = $rolM->getAllRole();
            $this->assign("roles",$roles);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($employees_count/$num);
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("start_num",$start_num);
        $this->assign("num",$num);
        $this->assign("id",$struct_id);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("filter",$filter);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
        return view();
    }

    public function not_struct_employee_list(){
        $struct_id = input("id",0,"int");
        if(!$struct_id){
            $this->error("参数错误!");
        }
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $filter = $this->_getCustomerFilter(["role","structure","tel_email"]);
        //var_exp($filter,'$filter');
        try{
            $employeeM = new EmployeeModel($this->corp_id);
            $employee_list = $employeeM->getEmployeeByNotStructId($struct_id,$start_num,$num,$filter,$order,$direction);
            //var_exp($employee_list,'$employee_list',1);
            $this->assign('listdata',$employee_list);
            $employees_count = $employeeM->countEmployeeByNotStructId($struct_id);
            //var_exp($employees_count,'$employees_count',1);
            $this->assign("count",$employees_count);
            $rolM = new RoleModel($this->corp_id);
            $roles = $rolM->getAllRole();
            $this->assign("roles",$roles);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($employees_count/$num);
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("id",$struct_id);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
        return view();
    }

    public function move_employee_page(){
        $struM = new StructureModel($this->corp_id);
        $structs = $struM->getAllStructure();
        $this->assign('structs',$structs);
        return view();
    }

    public function employee_list_transfer(){
        $root_id = 0;
        $struM = new StructureModel($this->corp_id);
        $structs = $struM->getAllStructure();
        $tree = new \myvendor\Tree($structs,['id','struct_pid']);
        $res = $tree->leaf($root_id);
//        var_exp($res,'struct_tree');die;
        $this->assign('structs',$structs);
        $this->assign('struct_tree',$res);
        $this->assign('struct_json',json_encode($res));
        $this->assign('root_id',$root_id);
        return view();
    }

    public function employee_list_transfer_structure(){
        $result = 'var cityData=';
        $result_arr = [];
        $root_id = 0;
        $struM = new StructureModel($this->corp_id);
        $structs = $struM->getAllStructure();
        $tree = new \myvendor\Tree($structs,['id','struct_pid']);
        $res = $tree->leaf($root_id);
        //var_exp($res,'$res',1);
        $result_arr = $this->get_structure_tree_arr($res);
        //var_exp($result_arr,'$result_arr');
        $result .= json_encode($result_arr,true).";";
        echo $result;
    }

    protected function get_structure_tree_arr($res,$id_field="id",$name_field="struct_name",$child_field="child"){
        $result_arr = [];
        foreach ($res as $re){
            $id = $re[$id_field];
            $name = $re[$name_field];
            $structure = [$id=>$name];
            if(isset($re[$child_field])){
                $structure["childCity"] = $this->get_structure_tree_arr($re[$child_field]);
            }
            $result_arr[] = $structure;
        }
        return $result_arr;
    }

    protected function _getCustomerFilter($filter_column){
        $filter = [];
        if(in_array("structure", $filter_column)){//直属部门
            $structure = input("structure",0,"int");
            if($structure){
                $filter["structure"] = $structure;
            }
        }
        if(in_array("role", $filter_column)){//角色
            $role = input("role",0,"int");
            if($role){
                $filter["role"] = $role;
            }
        }
        if(in_array("on_duty", $filter_column)){//状态
            $on_duty = input("on_duty",0,"int");
            if($on_duty){
                $filter["on_duty"] = $on_duty;
            }
        }
        if(in_array("worknum", $filter_column)){//工号
            $worknum = input("worknum");
            if($worknum){
                $filter["worknum"] = $worknum;
            }
        }
        if(in_array("truename", $filter_column)){//姓名
            $truename = input("truename");
            if($truename){
                $filter["truename"] = $truename;
            }
        }
        if(in_array("tel_email", $filter_column)){//邮箱或电话或姓名
            $truename = input("tel_email");
            if($truename){
                $filter["tel_email"] = $truename;
            }
        }
        return $filter;
    }

    /**
     * 添加部门
     * @return string json
     * created by blu10ph
     */
    public function add(){
        $result = ['status'=>0 ,'info'=>"添加部门时发生错误！"];
        $pid = input("pid",0,"int");
        $is_group = input("is_group",0,'int');
        $name = input("name");
        $max_deep_level = 5;
        if($pid<=0||!$name){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $struM = new StructureModel($this->corp_id);
            $check_flg = $struM->checkStructureLevelDeep($pid,$max_deep_level);
            if(!$check_flg){
                exception("部门层级达到上限");
            }
            $add_data['struct_pid'] = $pid;
            $add_data['struct_name'] = $name;
            $add_flg = $struM->addStructure($add_data);
            if(!$check_flg){
                exception("添加部门失败!");
            }
            $result['data'] = $add_flg;
            if ($is_group) {
                $huanxin = new HuanxinApi();

                $new_group = $huanxin->createGroup($this->corp_id,$add_flg,$name,$name,$this->corp_id."_".'0');
                if (!$new_group['status']) {
                    $result['error'] = $new_group['message'];
                }
            }
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "添加部门成功！";
        return json($result);
    }

    /**
     * 部门重命名
     * @param $struct_id 部门id
     * @param $new_name 新名称
     * @return array
     * created by messhair
     */
    public function renameStructure($struct_id,$new_name,$is_group)
    {   
        $info = [
                'status' => false,
                'message'=> '更改部门名称失败',
            ];
            $is_group = input('is_group',0,'int');
        try {
            $struM = new StructureModel($this->corp_id);
            $huanxin = new HuanxinApi();
            $data = [
                'struct_name'=>$new_name,
            ];
            $struct_info = $struM->getStructureInfo($struct_id);
            if ($is_group && !$struct_info['groupid']) {
                
                $new_group = $huanxin->createGroup($this->corp_id,$struct_id,$new_name,$new_name,$this->corp_id."_".'0');
                if (!$new_group['status']) {
                    exception("创建群组失败");
                }
                $group_id = $new_group['data'];
                $structemployeeM = new StructureEmployeeModel($this->corp_id);
                $employee_list = $structemployeeM->getEmployeeByStructIds($struct_id);
                if (!empty($employee_list)) {
                    foreach ($employee_list as $key => $value) {
                        $employee_name[] = $this->corp_id."_".$value['user_id'];
                    }
                    $employeeaddgroup = $huanxin->addAllUsers($group_id,$employee_name);
                    if (isset($employeeaddgroup['error'])) {
                        exception("添加群成员失败");
                    }
                }

            }
            if (!$is_group && $struct_info['groupid']) {
                $del_group = $huanxin->deleteGroup($this->corp_id,$struct_id,$struct_info['groupid']);
                if (!$del_group['status']) {
                    exception("删除群组失败");
                }
            }
            if ($is_group && $struct_info['groupid']) {
                $edit_info['groupname'] = $new_name;
                $edit_group = $huanxin->updateGroupInfo($struct_info['groupid'],$edit_info);
                if (!$edit_group['status']) {
                    exception("跟新群组信息失败");
                }

            }
            
            $b = $struM->setStructure($struct_id,$data);
            if ($b<0) {
                exception("更改部门名称失败");
            }
        } catch (\Exception $ex) {
            $info['message'] = $ex->getMessage();
            return json($info);
        }
            $info = [
                'status'=>true,
                'message'=>'更改部门名称成功',
            ];
       
        return json($info);
    }
    public function getStructureEmployeenum($struct_id){
        $info = [
            'status' => false,
            'message'=> '查询部门员工数失败',
        ];
        $employeeM = new EmployeeModel($this->corp_id);
        $in_struct_employee_num = $employeeM->getInStructEmployeenum($struct_id);
        $info = [
            'status'=>true,
            'message'=>'查询部门员工数成功',
            "data"=>$in_struct_employee_num
        ];
        return $info;
    }

    public function getStructureInfo($struct_id){
        $info = ['status'=>false,'message'=>'查询部门信息失败'];

        $structureM = new StructureModel($this->corp_id);
        $structInfo = $structureM->getStructureInfo($struct_id);
        $info = ['status'=>1,'message'=>'查询部门信息成功','data'=>$structInfo];

        return $info;
    }

    /**
     * 删除部门操作
     * @param $struct_id 部门id
     * @param $trans 是否转移员工到默认部门 1是，0否
     * @return array
     * created by messhair
     */
    public function deleteStructure($struct_id,$trans)
    {
        $struM = new StructureModel($this->corp_id);
        $st_res = $struM->getStructureInfo($struct_id);
        // var_dump($trans);die();
        if (empty($st_res)) {
            return ['status'=>false,'message'=>'选择的部门不存在'];
        }

        $st_res_all = $struM->getAllStructure();
        $ids = deep_get_ids($st_res_all,$struct_id);
        if (!empty($ids)) {
            $info = [
                    'status' =>false,
                    'message' =>'该部门下存在子部门无法删除'
                ];
                return $info;
        }
        $ids[] = $struct_id;
        //var_exp($ids,'$ids',1);
        $ids = implode(',',$ids);

        $employeeM = new StructureEmployeeModel($this->corp_id);
        $huanxin = new HuanxinApi();
        $users = $employeeM->getEmployeeByStructIds($ids);
        if (!empty($users)) {
            $info = [
                    'status' =>false,
                    'message' =>'该部门下有员工无法删除'
                ];
                return $info;
        }
        if ($trans == 1) {
            //转移员工到默认组
            if (empty($users)) {
                $employeeM->link->startTrans();
                $b = 1;
            } else {
                $user_ids = [];
                foreach ($users as $val) {
                    $user_ids[] .= $val['user_id'];
                }
                $user_ids = implode(',',$user_ids);
                $data = [
                    'struct_id' => -1,
                ];
                $employeeM->link->startTrans();
                $b = $employeeM->setStructureEmployeebyIds($user_ids,$struct_id,$data);
            }
            if (!empty($st_res['groupid'])) {
                $del_group = $huanxin->deleteGroup($this->corp_id,$struct_id,$st_res['groupid']);
            }else{
                $del_group['status'] = 1;
            }
            $d = $struM->deleteStructure($ids);
            
            if ($b>0 && $d>0 && $del_group['status']) {
                $employeeM->link->commit();
                $info = [
                    'status' =>true,
                    'message' =>'部门已删除'
                ];
            } else {
                $employeeM->link->rollback();
                $info = [
                    'status' =>false,
                    'message' =>'操作失败'
                ];
            }
        } elseif ($trans == 0) {
            //删除所有员工
            if (empty($users)) {
                $employeeM->link->startTrans();
                $b = 1;
            } else {
                $user_ids = [];
                foreach ($users as $val) {
                    $user_ids[] .= $val['user_id'];
                }
                $user_ids = implode(',',$user_ids);
                $data = [
                    'struct_id' => -1,
                ];
                $employeeM->link->startTrans();
                //TODO 删除员工
                $b = $employeeM->setStructureEmployeebyIds($user_ids,$struct_id,$data);
            }

            if (!empty($st_res['groupid'])) {
                $del_group = $huanxin->deleteGroup($this->corp_id,$struct_id,$st_res['groupid']);
            }else{
                $del_group['status'] = 1;
            }
            $d = $struM->deleteStructure($ids);
            
            if ($b>0 && $d>0 && $del_group['status']) {
                $employeeM->link->commit();
                $info = [
                    'status' =>true,
                    'message' =>'员工已删除，部门已删除'
                ];
            } else {
                $employeeM->link->rollback();
                $info = [
                    'status' =>false,
                    'message' =>'操作失败'
                ];
            }
        }
        return $info;
    }

    /**
     * 显示指定部门的员工
     * @param $struct_id 部门id
     * @return \think\response\View
     * created by messhair
     */
    public function showPointedDepartment($struct_id,$page_now_num=0,$page_row=10)
    {
        $employeeM = new EmployeeModel($this->corp_id);
        $total_num = $employeeM->countEmployeeByStructId($struct_id);
        $data = $employeeM->getEmployeeByStructId($struct_id,$page_now_num,$page_row);
        $res = ['data'=>$data,'page'=>['page_now_num'=>$page_now_num,'page_row'=>$page_row,'total_num'=>$total_num]];
        return $res;
    }

    /**
     * 批量添加员工所在部门
     * @return array
     * created by blu10ph
     */
    public function addEmployeeStructure()
    {
        $info = [
            'status'=>false,
            'message'=>'添加部门成员失败'
        ];
        $struct_id = input('struct_id',0,"int");
        $user_ids = input('user_ids',"","string");
        if(!$struct_id && !$user_ids){
            return [
                'status'=>false,
                'message'=>'参数错误'
            ];
        }
        $user_ids_arr = explode(",",$user_ids);
        $item_data = ['struct_id'=>$struct_id];
        $structEmployees = [];
        foreach ($user_ids_arr as $user_id){
            $item_data["user_id"] = $user_id;
            $structEmployees[] = $item_data;
            $group_users[] = $this->corp_id."_".$user_id;
        }
        $employeeM = new StructureEmployeeModel($this->corp_id);
        $structModel = new StructureModel($this->corp_id);
        $huanxin = new HuanxinApi();

        $struct_info = $structModel->getStructureInfo($struct_id);
        $group_id = $struct_info['groupid'];
        $employeeM->link->startTrans();
        try {
            $res = $employeeM->addMultipleStructureEmployee($structEmployees);
            if (!$res) {
                exception("添加数据表失败");
            }
            if ($group_id) {
                $res = $huanxin->addAllUsers($group_id,$group_users);
                if (isset($res['error'])) {
                    exception("添加环信群组失败");
                }
            }
            $employeeM->link->commit();
        } catch (\Exception $ex) {
            $employeeM->link->rollback();
            return $info;   
        }
       
        $info=[
            'status' =>true,
            'message' => '添加部门成员成功',
        ];
       
        return $info;
    }

    /**
     * 切换员工所在部门
     * @param $user_id 员工id
     * @param $to_group 目标部门id
     * @return array
     * created by messhair
     */
    public function changeEmployeeStructure($user_id,$group,$to_group)
    {   
        $info = [
                'status'=>false,
                'message'=>'更换部门失败或未更换部门',
            ];
        if($group==$to_group){
            return ['status'=>false,'message'=>'转移前后不能是同一部门!'];
        }
        $struM = new StructureModel($this->corp_id);
        $st_res = $struM->getStructureInfo($to_group);
        if (empty($st_res)) {
            return ['status'=>false,'message'=>'选择的部门不存在'];
        }
        $struct_info = $struM->getStructureInfo($group);
        $group_id = $struct_info['groupid'];
        $struct_to_info = $struM->getStructureInfo($to_group);
        $group_to_id = $struct_to_info['groupid'];
        $user_name = $this->corp_id."_".$user_id;
        $employeeM = new StructureEmployeeModel($this->corp_id);
        $data = [
            'struct_id' => $to_group,
        ];
        $huanxin = new HuanxinApi();
        // var_dump($group_to_id);die();
        $employeeM->link->startTrans();
        try {
            $res = $employeeM->setStructureEmployeeById($user_id,$group,$data);
            if (!$res) {
                $info['message'] = '更新更换部门数据表失败';
                exception("更新更换部门数据表失败");
            }
            
            if ($group_id) {
                $res = $huanxin->deleteOneEmployee($group_id,$user_name);
                if (isset($res['error'])) {
                    $info['message'] = "删除环信群组员工失败";
                    exception("删除环信群组员工失败");
                }
            }
            if ($group_to_id) {
                $res = $huanxin->addOneEmployee($group_to_id,$user_name);
                if (isset($res['error'])) {
                    $info['message'] = "添加环信群组员工失败";
                    exception("添加环信群组员工失败");
                }
            }
            // var_dump($res);die();
            
            $employeeM->link->commit();
        } catch (\Exception $ex) {
            $employeeM->link->rollback();
            return $info;
        }
        
            $info=[
                'status' =>true,
                'message' => '更换部门成功',
            ];
        return $info;
    }

    //批量操作员工
    /**
     * 批量操作员工
     * @return [type] [description]
     */
    public function changeEmployeesFromStructs(){
        $info = [
            'status'=>false,
            'message'=>'批量操作员工失败'
        ];
        $group = input('group',0,"int");
        $to_group = input('to_group',0,"int");
        $user_ids = input('user_ids',"","string");
        if(!$group || !$to_group || !$user_ids){
            return [
                'status'=>false,
                'message'=>'参数错误'
            ];
        }
        if ($group == $to_group) {
            return [
                'status'=>false,
                'message'=>'转移部门不能和原部门相同'
            ];
        }
        $user_ids_arr = explode(",",$user_ids);
        
        $employeeM = new StructureEmployeeModel($this->corp_id);
        $structModel = new StructureModel($this->corp_id);
        $huanxin = new HuanxinApi();

        $edit_userids = [];
        $del_userids = [];
        foreach ($user_ids_arr as $key => $value) {
            $flg = $employeeM->getOneInfo($value,$to_group);
            if (empty($flg)) {
                $edit_userids[] = $value;
                $group_users[] = $this->corp_id."_".$value;
            }else{
                $del_userids[] = $value;
            }
            $del_group_users[] = $this->corp_id."_".$value;
        }

        $struct_info = $structModel->getStructureInfo($group);
        $group_id = $struct_info['groupid'];
        $to_struct_info = $structModel->getStructureInfo($to_group);
        $to_group_id = $to_struct_info['groupid'];

        $data = ['struct_id'=>$to_group];

        $employeeM->link->startTrans();
        try {
            $res = $employeeM->setStructureEmployeebyIds($group_users,$group,$data);
            if (!$res) {
                exception("添加数据表失败");
            }
            $res = $employeeM->delStructureEmployee($group,$del_userids);
            if (!$res) {
                exception("删除数据表失败");
            }
            if ($group_id) {
                $res = $huanxin->deleteAllUsers($group_id,$del_group_users);
                if (isset($res['error'])) {
                    exception("删除环员工失败");
                }
            }
            if ($to_group_id) {
                $res = $huanxin->addAllUsers($to_group_id,$group_users);
                if (isset($res['error'])) {
                    exception("添加环信员工失败");
                }
            }
            $employeeM->link->commit();
        } catch (\Exception $ex) {
            $employeeM->link->rollback();
            return $info;   
        }
       
        $info=[
            'status' =>true,
            'message' => '批量操作部门成员成功',
        ];
       
        return $info;
    }


    /**
     * 移除员工所在部门
     * @param $user_id 员工id
     * @param $group 部门id
     * @return array
     * created by messhair
     */
    public function delEmployeeStructure($user_id,$group)
    {
        $info = [
                'status'=>false,
                'message'=>'删除部门成员失败'
            ];
        $employeeM = new StructureEmployeeModel($this->corp_id);
        $structModel = new StructureModel($this->corp_id);
        $huanxin = new HuanxinApi();

        $struct_info = $structModel->getStructureInfo($group);
        $group_id = $struct_info['groupid'];
        $user_name = $this->corp_id."_".$user_id;
        // var_dump($group_id);die();
        $data = [
            'struct_id' => $group,
        ];
        $employeeM->link->startTrans();
        try {
            $res = $employeeM->deleteMultipleStructureEmployee($user_id,$data);
            if (!$res) {
                $info['message'] = "数据表删除员工失败";
                exception("数据表删除员工失败");
            }
            if ($group_id) {
                $res = $huanxin->deleteOneEmployee($group_id,$user_name);
                if (isset($res['error'])) {
                    $info['message'] = "删除环信群组员工失败";
                    exception("删除环信群组员工失败");
                }
            }
            $employeeM->link->commit();
        } catch (\Exception $ex) {
            $employeeM->link->rollback();
            return $info;
        }
        
       
        $info=[
            'status' =>true,
            'message' => '删除部门成员成功',
        ];
      
        return $info;
    }

    /**
     * 移动部门
     * @return [type] [description]
     */
    public function changeStruct(){
        $info = [
            'status'=>true,
            'message'=>'移动部门失败',
        ];

        $struct_id = input('struct_id',0,'int');
        $pstruct_id = input('pstruct_id',0,'int');

        if (!$struct_id) {
            return [
                'status'=>false,
                'message'=>"参数错误",
            ];
        }

        $data = ['struct_pid'=>$pstruct_id];
        $struM = new StructureModel($this->corp_id);
        $max_deep_level = 5;    
        try {
            $check_flg = $struM->checkStructureLevelDeep($pstruct_id,$max_deep_level);
            if(!$check_flg){
                exception("部门层级达到上限");
            }

            $flg = $struM->setStructure($struct_id,$data);
            if (!$flg) {
                  exception("跟新部门数据表失败");
            }  
        } catch (\Exception $ex) {
            $info['message'] = $ex->getMessage();
            return $info;
        }

        $info['status'] = true;
        $info['message'] = "部门转移成功";

        return $info;

    }

    /**
     * 修改群名
     */
    public function editGroupName(){
        $result = ['status'=>0,'info'=>"修改群名失败"];

        $groupid = input('groupid','','string');
        $group_name = input('group_name','','string');
        $from_name = input('from_name','','string');
        if (!$groupid || !$group_name || !$from_name) {
            $result['info'] = "参数错误";
            return $result;
        }

        $huanxin = new HuanxinApi();
        $group_info['groupname'] = $group_name;
        $target = [];
        $target[] = $groupid;
        $a = $huanxin->updateGroupInfo($groupid,$group_info);
        // $b = $huanxin->sendMessage('chatgroups',$target,$from_name."修改了群名称");
        if (isset($a['error']) || isset($b['error'])) {
            $result['error_1'] = $a['error'];
            $result['error_2'] = $b['error'];
        }else{
            $result['status'] = 1;
            $result['info'] = "修改成功";
        }

        return json($result);
    }
}