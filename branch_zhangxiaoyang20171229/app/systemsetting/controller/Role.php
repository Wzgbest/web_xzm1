<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Employee;
use app\common\model\Role as RoleModel;
use app\common\model\Rule as RuleModel;
use app\common\model\RoleRule as RoleRuleModel;
use think\Request;
use app\systemsetting\controller\Employee as EmployeeController;
use app\common\model\Employee as EmployeeModel;
use app\common\model\Structure as StructureModel;
use app\common\model\RoleEmployee;
use app\systemsetting\model\BillSetting as BillSettingModel;
use app\systemsetting\model\BusinessFlowItemLink as BusinessFlowItemLinkModel;
use app\systemsetting\model\ContractSetting as ContractSettingModel;

class Role extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        $this->rule_map = [];
        $this->rule_map["systemsetting/role/role_manage"] = "systemsetting/role/index/select";
        $this->rule_map["systemsetting/role/employee_list"] = "systemsetting/role/index/select";
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    /**
     * 首页显示
     * @return \think\response\View
     * created by messhair
     */
    public function index()
    {
        $roleM = new RoleModel($this->corp_id);
        $roles = $roleM->getAllRole();
        $this->assign('roles',$roles);
        return view();
    }

    public function role_manage(){
        $role_id = input("id",0,"int");
        if(!$role_id){
            $this->error("参数错误!");
        }
        $roleM = new RoleModel($this->corp_id);
        $role = $roleM->getRoleInfo($role_id);
        $this->assign('role',$role);
        $hav_struct = $role["hav_struct"];
        $hav_struct_arr = explode(",",$role["hav_struct"]);
        $struM = new StructureModel($this->corp_id);
        $struct_names = $struM->getStructureName($hav_struct_arr);
        $struct_names_str = implode(",",$struct_names);
        $this->assign('struct_names_str',$struct_names_str);
        $hav_struct_args = "struct_ids[]=".implode("&struct_ids[]=",$hav_struct_arr);
        $this->assign('hav_struct_args',$hav_struct_args);
        $roleRuleM = new RoleRuleModel($this->corp_id);
        $role_rules = $roleRuleM->getRulesByRole($role_id);
//        var_exp($role_rules,'$role_rules');
        $this->assign('role_rules',$role_rules);
        $role_rule_ids = array_column($role_rules,"id");
//        var_exp($role_rule_ids,'$role_rule_ids');
        $this->assign('role_rule_ids',$role_rule_ids);

        $ruleM = new RuleModel($this->corp_id);
        $all_rules = $ruleM->getAllRules();
//        var_exp($all_rules,'$all_rules');
        $this->assign('all_rules',$all_rules);

        $root_id = 0;
        $tree = new \myvendor\Tree($all_rules);
        $rule_tree = $tree->leaf($root_id);
//        var_exp($rule_tree,'$rule_tree');
        $this->assign('rule_tree',$rule_tree);
        $rule_list = [];
        $rule_sub_list = [];
        foreach ($rule_tree as $rule){
            $child_num = 0;
            if(isset($rule["child"])){
                $child_num = count($rule["child"]);
            }
            $rule["child_num"] = $child_num;
            $rule_list[] = $rule;
            if(isset($rule["child"])){
                foreach ($rule["child"] as $rule_c){
                    $sub_child_num = 0;
                    if(isset($rule_c["child"])){
                        $sub_child_num = count($rule_c["child"]);
                    }
                    $rule_c["child_num"] = $sub_child_num;
                    $rule_list[] = $rule_c;
                    $rule_sub_list[$rule_c["id"]] = isset($rule_c["child"])?$rule_c["child"]:false;
                }
            }
        }
//        var_exp($rule_list,'$rule_list');
//        var_exp($rule_sub_list,'$rule_sub_list');
        $this->assign('rule_list',$rule_list);
        $this->assign('rule_sub_list',$rule_sub_list);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }

    public function employee_list(){
        $role_id = input("id",0,"int");
        if(!$role_id){
            $this->error("参数错误!");
        }
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $filter = $this->_getCustomerFilter(["structure","worknum","truename"]);
        try{
            $employeeM = new Employee($this->corp_id);
            $employee_list = $employeeM->getEmployeeByRole($role_id,$start_num,$num,$filter);
            //var_exp($employee_list,'$employee_list',1);
            $this->assign('listdata',$employee_list);
            $employees_count = $employeeM->getEmployeeCountByRole($role_id);
            //var_exp($employees_count,'$employees_count',1);
            $this->assign("count",$employees_count);
            $struM = new StructureModel($this->corp_id);
            $structs = $struM->getAllStructure();
            $this->assign("structs",$structs);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($employees_count/$num);
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("id",$role_id);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }
    public function employee_show(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }
        $this->assign("id",$id);
        $employeeM = new EmployeeModel($this->corp_id);
        $employee = $employeeM->getEmployeeByUserid($id);
        $employee["role_id"] = explode(",",$employee["role_id"]);
        //var_exp($employee,'$employee',1);
        $this->assign("employee",$employee);
        $struM = new StructureModel($this->corp_id);
        $structs = $struM->getAllStructure();
        $this->assign("structs",$structs);
        $rolM = new RoleModel($this->corp_id);
        $roles = $rolM->getAllRole();
        $this->assign("roles",$roles);
        return view();
    }

    public function not_role_employee_list(){
        $role_id = input("id",0,"int");
        if(!$role_id){
            $this->error("参数错误!");
        }
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $filter = $this->_getCustomerFilter(["structure","tel_email"]);
        //var_exp($filter,'$filter');
        try{
            $employeeM = new Employee();
            $employee_list = $employeeM->getNotRoleEmployeeByRole($role_id,$start_num,$num,$filter,$order,$direction);
            //var_exp($employee_list,'$employee_list',1);
            $this->assign('listdata',$employee_list);
            $employees_count = $employeeM->getNotRoleEmployeeCountByRole($role_id,$filter);
            //var_exp($employees_count,'$employees_count',1);
            $this->assign("count",$employees_count);
            $struM = new StructureModel($this->corp_id);
            $structs = $struM->getAllStructure();
            $this->assign("structs",$structs);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($employees_count/$num);
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("id",$role_id);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
        return view();
    }

    protected function _getCustomerFilter($filter_column){
        $filter = [];
        if(in_array("structure", $filter_column)){//直属部门
            $structure = input("structure",0,"int");
            if($structure){
                $filter["structure"] = $structure;
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
     * 角色列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function showRoles(){
        $rol = new RoleModel($this->corp_id);
        $data = $rol->getAllRole();
        return $data;
    }

    /**
     * 显示角色对应的权限
     * @param $role_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function showRules($role_id){
        $rol = new RoleModel($this->corp_id);
        $roles = $rol->getRoleInfo($role_id);
        return $roles;
    }

    /**
     * 添加角色
     * @param $role_name string 职位名称
     * @return array created by messhair
     * created by messhair
     */
    public function addRole($role_name){
        $rol = new RoleModel($this->corp_id);
        $data = [
            'role_name'=>$role_name,
        ];
        $b = $rol->addRole($data);
        if ($b>0) {
            return [
                'status'=>true,
                'message'=>'添加角色成功',
                'role_id'=>$b
            ];
        } else {
            return [
                'status'=>false,
                'message'=>'添加角色失败'
            ];
        }
    }

    /**
     * 修改角色名称
     * @param Request $request
     * @return array
     * created by messhair
     */
    public function editRoleName(Request $request){
        $input = $request->param();
        $rol = new RoleModel($this->corp_id);
        $data = [
            'role_name'=>$input['role_name']
        ];
        $b = $rol->setRole($input['role_id'],$data);
        if ($b > 0) {
            return [
                'status'=>true,
                'message'=>'修改角色名称成功'
            ];
        } else {
            return [
                'status'=>false,
                'message'=>'修改角色名称失败'
            ];
        }
    }

    /**
     * 修改角色对应所有权限
     */
    public function editRoleAll(){
        if(!($this->checkRule('systemsetting/role/index/save_rule'))){
            $result=$this->noRole();
            return json($result);
        }
        $result = ['status'=>0 ,'info'=>"修改角色权限时发生错误！"];
        $role_id = input("role_id","0","int");
        if(!$role_id){
            $result["info"] = "参数错误!";
            return json($result);
        }
        $data_type = input("data_type","0","int");
        $struct_ids = input("struct_ids/a");
        if($data_type==4 && empty($struct_ids)){
            $result["info"] = "参数错误!";
            return json($result);
        }
        $rule_ids = input("rule_ids/a");
        //从数据库获取该角色现有权限,与传入的新权限进行对比,添加新增的,删除不没有的
        if (empty($rule_ids)) {
            $result['info'] = "权限数据为空";
            return json($result);
        }
        $roleRuleM = new RoleRuleModel($this->corp_id);
        $roleRuleM->link->startTrans();
        try {
            $result = $this->_editRoleData($role_id,$data_type,$struct_ids);
            if (!$result["status"]) {
                $result['info'] = "修改数据权限失败";
                exception("修改数据权限失败");
            }
            $result = $this->_editRoleRule($role_id,$rule_ids);
            if (!$result["status"]) {
                $result['info'] = "修改功能权限失败";
                exception("修改功能权限失败");
            }
            $result['status'] = 1;
            $result['info'] = "修改功能权限成功!";
            del_keys('rules');//清除redis里缓存的权限，下次加载时重新查询最新的
            $roleRuleM->link->commit();
        } catch (\Exception $ex) {
            $roleRuleM->link->rollback();
            //$result['info'] = $ex->getMessage();
        }
        return json($result);
    }

    /**
     * 修改角色数据权限
     */
    protected function _editRoleData($role_id,$data_type,$struct_ids){
        $result = ['status'=>0 ,'info'=>"修改角色数据权限时发生错误！"];
        $data["data_type"] = $data_type;
        $hav_struct = '';
        if($data_type==4){
            //TODO 暂时使用字段记录对应部门,以后改为单独的关系表关联
            $hav_struct = implode(",",$struct_ids);
            $data["hav_struct"] = $hav_struct;
        }
        $rol = new RoleModel($this->corp_id);
        $role_info = $rol->getRoleInfo($role_id);
        $flg = false;
        if($role_info["data_type"]!=$data_type||$hav_struct!=$role_info["hav_struct"]){
            $flg = $rol->setRole($role_id,$data);
//            var_exp($flg,'$flg_data_type');
        }else{
            $flg = true;
        }
        if(!$flg){
            return $result;
        }
        $result["status"] = 1;
        $result["info"] = "修改角色数据权限成功!";
        return $result;
    }

    /**
     * 修改角色数据权限
     */
    public function editRoleData(){
        $result = ['status'=>0 ,'info'=>"修改角色数据权限时发生错误！"];
        $role_id = input("role_id","0","int");
        if(!$role_id){
            $result["info"] = "参数错误!";
            return json($result);
        }
        $data_type = input("data_type","0","int");
        $struct_ids = input("struct_ids/a");
        if($data_type==4 && empty($struct_ids)){
            $result["info"] = "参数错误!";
            return json($result);
        }
        $result = $this->_editRoleData($role_id,$data_type,$struct_ids);
        return json($result);
    }

    /**
     * 修改角色对应权限
     */
    protected function _editRoleRule($role_id,$rule_ids){
        $result = ['status'=>0 ,'info'=>"修改角色权限功能时发生错误！"];
        $roleRuleM = new RoleRuleModel($this->corp_id);
        //查询该角色下目前所有的权限
        $all_rules = $roleRuleM->getRuleIdByRoleId($role_id);
        $rules_old_arr = array_column($all_rules,"rule_id");
//        var_exp($rule_ids,'$rule_ids');
        $add_rules = array_diff($rule_ids,$rules_old_arr);
//        var_exp($add_rules,'$add_rules');
        $del_rules = array_diff($rules_old_arr,$rule_ids);
//        var_exp($del_rules,'$del_rules');
        $roleRuleM->link->startTrans();
        try {
            if (!empty($add_rules)) {
                $result = $this->_addRoleRule($role_id,$add_rules);
//                var_exp($result,'$result_addRoleRule');
                if (!$result["status"]) {
                    $result['info'] = "添加新权限失败";
                    exception("添加新权限失败");
                }
            }
            if (!empty($del_rules)) {
                $result = $this->_delRoleRule($role_id,$del_rules);
//                var_exp($result,'$result_delRoleRule');
                if (!$result["status"]) {
                    $result['info'] = "删除旧权限失败";
                    exception("删除旧权限失败");
                }
            }
            $roleRuleM->link->commit();
        } catch (\Exception $ex) {
            $roleRuleM->link->rollback();
            return json($result);
        }

        $result['status'] = 1;
        $result['info'] = "修改角色权限成功";
        return $result;
    }

    /**
     * 修改角色对应权限
     */
    public function editRoleRule(){
        $result = ['status'=>0 ,'info'=>"修改角色权限功能时发生错误！"];
        $role_id = input("role_id","0","int");
        $rule_ids = input("rule_ids/a");
        //从数据库获取该角色现有权限,与传入的新权限进行对比,添加新增的,删除不没有的
        if (!$role_id || empty($rule_ids)) {
            $result['info'] = "角色id或权限数据为空";
            return json($result);
        }
        $result = $this->_editRoleRule($role_id,$rule_ids);
        return json($result);
    }

    /**
     * 添加角色对应权限
     */
    protected function _addRoleRule($role_id,$rule_ids){
        $result = ['status'=>0 ,'info'=>"添加角色权限功能时发生错误！"];
        $roleRuleM = new RoleRuleModel($this->corp_id);
        foreach ($rule_ids as $key => $value) {
            $data[$key]['role_id'] = $role_id;
            $data[$key]['rule_id'] = $value;
        }
//        var_exp($data,'$data_addRoleRule');
        $flg = $roleRuleM->addRoleRule($data);
        if(!$flg){
            return $result;
        }
        $result['status'] = 1;
        $result['info'] = "添加角色权限成功";
        return $result;
    }

    /**
     * 添加角色对应权限
     */
    public function addRoleRule(){
        $result = ['status'=>0 ,'info'=>"添加角色权限功能时发生错误！"];
        $role_id = input("role_id","0","int");
        $rule_ids = input("rule_ids/a");
        //从数据库获取该角色权限,有的话返回已经有了,没有的话添加并返回结果
        if (!$role_id || empty($rule_ids)) {
            $result['info'] = "角色id或权限数据为空";
            return json($result);
        }
        $result = $this->_addRoleRule($role_id,$rule_ids);
        return json($result);
    }

    /**
     * 删除角色对应权限
     */
    protected function _delRoleRule($role_id,$rule_ids){
        $result = ['status'=>0 ,'info'=>"修改角色权限功能时发生错误！"];
        $roleRuleM = new RoleRuleModel($this->corp_id);
//        var_exp($rule_ids,'$rule_ids_deleteRoleRule');
        $flg = $roleRuleM->deleteRoleRule($role_id,$rule_ids);
        if(!$flg){
            return $result;
        }
        $result['status'] = 1;
        $result['info'] = "删除角色权限成功";
        return $result;
    }

    /**
     * 删除角色对应权限
     */
    public function delRoleRule(){
        $result = ['status'=>0 ,'info'=>"修改角色权限功能时发生错误！"];
        $role_id = input("role_id","0","int");
        $rule_ids = input("rule_ids/a");
        //从数据库获取该角色权限,没有的话返回已经没有了,有的话删除并返回结果
        if (!$role_id || empty($rule_ids)) {
            $result['info'] = "角色id或权限数据为空";
            return json($result);
        }
        $result = $this->_delRoleRule($role_id,$rule_ids);
        return json($result);
    }

    /**
     * 显示角色对应的员工
     * @param Request $request  ['role_id']
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function showRoleMember(Request $request){
        $input = $request->param();
        $employeeM = new Employee();
        $res = $employeeM->getEmployeeByRole($input['role_id']);
        return $res;
    }

    /**
     * 角色添加成员
     * @param Request $request
     * @return array|\think\response\View
     * created by messhair
     */
    public function addRoleMember(Request $request){
        if(!($this->checkRule('systemsetting/role/index/employee_add'))){
            $result=$this->noRole();
            $result['message']=$result['info'];
            return json($result);
        }

        $input = $request->param();
        $employeeM = new Employee();
        if ($request->isGet()) {
            $data = $employeeM->getEmployeeByNotRole($input['role_id'],$input['struct_id'],$input['user_tel_email']);
            $this->assign('data',$data);
            return view();
        } elseif ($request->isPost()) {
            $role_id = $input['role_id'];
            $user_ids = $input['user_ids'];
            if(!$role_id && !$user_ids){
                return [
                    'status'=>false,
                    'message'=>'参数错误'
                ];
            }
            $user_ids_arr = explode(",",$user_ids);
            $item_data = ['role_id'=>$role_id];
            $roleEmployees = [];
            foreach ($user_ids_arr as $user_id){
                $item_data["user_id"] = $user_id;
                $roleEmployees[] = $item_data;
            }
            $role_empM = new RoleEmployee($this->corp_id);
            $b = $role_empM->createMultipleRoleEmployee($roleEmployees);
            if ($b > 0) {
                return [
                    'status'=>true,
                    'message'=>'调整员工角色成功'
                ];
            } else {
                return [
                    'status'=>false,
                    'message'=>'调整员工角色失败'
                ];
            }
        }
    }

    public function _checkNotUse($ids){
        $is_not_use = true;
        $billSettingM = new BillSettingModel($this->corp_id);
        $in_use_bill_setting_count = $billSettingM->getBillSettingByRoleIds($ids);
        if(count($in_use_bill_setting_count)>0){
            //var_exp($in_use_bill_setting_count,'$in_use_bill_setting_count0',1);
            $is_not_use = false;
            return $is_not_use;
        }
        $businessFlowItemLinkM = new BusinessFlowItemLinkModel($this->corp_id);
        $in_use_business_flow_setting_count = $businessFlowItemLinkM->getBusinessFlowSettingByRoleIds($ids);
        if(count($in_use_business_flow_setting_count)>0){
            //var_exp($in_use_bill_setting_count,'$in_use_bill_setting_count1',1);
            $is_not_use = false;
            return $is_not_use;
        }
        $contractSettingM = new ContractSettingModel($this->corp_id);
        $in_use_contract_setting_count = $contractSettingM->getContractSettingByRoleIds($ids);
        if(count($in_use_contract_setting_count)>0){
            //var_exp($in_use_bill_setting_count,'$in_use_bill_setting_count2',1);
            $is_not_use = false;
            return $is_not_use;
        }
        //var_exp($is_not_use,'$is_not_use',1);
        return $is_not_use;
    }

    /**
     * 删除角色
     * @param Request $request
     * @return array
     * created by messhair
     */
    public function deleteRole(Request $request){
        $input = $request->param();
        if(!$this->_checkNotUse($input['role_id'])){
            $result['status'] = false;
            $result['message'] = "存在正在使用中的审核项目,不能修改！";
            return json($result);
        }
        $employeeM = new Employee();
        $res = $employeeM->getEmployeeByRole($input['role_id']);
        if (!empty($res)) {
            return [
                'status'=>false,
                'message'=>'该角色包含成员，不能删除'
            ];
        }
        $rolM = new RoleModel($this->corp_id);
        $b = $rolM->deleteRole($input['role_id']);
        if ($b > 0) {
            return [
                'status'=>true,
                'message'=>'删除角色成功'
            ];
        } else {
            return [
                'status'=>false,
                'message'=>'删除角色失败'
            ];
        }
    }

    /**
     * 删除角色成员
     * @param Request $request
     * @return array
     * created by messhair
     */
    public function deleteRoleMember(Request $request){
        if(!($this->checkRule('systemsetting/role/index/employee_del'))){
            $result=$this->noRole();
            $result['message']=$result['info'];
            return json($result);
        }

        $result = ['status'=>0 ,'message'=>"删除角色成员失败！"];
        $input = $request->param();
        $employeeM = new Employee();
        $res = $employeeM->getEmployeeByRole($input['role_id']);
        if(!$this->_checkNotUse($input['role_id'])){
            if(count($res)==1){
                $result['message'] = "存在正在使用中的审核项目且仅剩一人,不能删除成员！";
                return json($result);
            }
        }
        $role_empM = new RoleEmployee($this->corp_id);
        $data = ['role'=>$input['role_id']];
        $b = $role_empM->deleteMultipleRoleEmployee($input['user_id'], $data);
        if ($b > 0) {
            $result = [
                'status'=>true,
                'message'=>'删除角色成员成功'
            ];
        }
        return $result;
    }

    /**
     * 查看员工详情
     * @param \app\systemsetting\controller\Employee $employee
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function showEmployeeInfo(EmployeeController $employee){
        $input = input('param.');
        $res = $employee->showSingleEmployeeInfo($input['user_id']);
        return $res;
    }
}