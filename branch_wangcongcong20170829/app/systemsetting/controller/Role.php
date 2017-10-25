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
        /*$roles_str_arr = array_column($roles,"roles");
        $role_ids = [];
        foreach ($roles_str_arr as $roles_str){
            $role_ids = array_merge($role_ids,explode(",",$roles_str));
        }
        $roleM = new RuleModel($this->corp_id);
        $roles = $roleM->getRulesColumnByIds($role_ids);
        //var_exp($roles,'$roles',1);
        foreach ($roles as &$role){
            $roles_arr = [];
            $roles_id_arr = explode(",",$role["roles"]);
            foreach ($roles_id_arr as $roles_id){
                $roles_arr[] = ["id"=>$roles_id,"role_name"=>$roles[$roles_id]["role_name"],"role_title"=>$roles[$roles_id]["role_title"]];
            }
            $role["roles"] = $roles_arr;
        }
        $this->assign('roles',$roles);*/
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
        try{
            $employeeM = new Employee($this->corp_id);
            $employee_list = $employeeM->getEmployeeByRole($role_id,$start_num,$num);
            //var_exp($employee_list,'$employee_list',1);
            $this->assign('listdata',$employee_list);
            $employees_count = $employeeM->getEmployeeCountByRole($role_id);
            //var_exp($employees_count,'$employees_count',1);
            $this->assign("count",$employees_count);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($employees_count/$num);
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("id",$role_id);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
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
    public function editRole(Request $request){
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
     * 修改角色---权限
     * @param Request $request  ['role_id','roles']
     * @return array|\think\response\View
     * created by messhair
     */
    public function editRoleRule(Request $request){
        $input = $request->param();
        if ($request->isGet()) {
            $rolRulM = new RoleModel($this->corp_id);
            $roles = $rolRulM->getRoleInfo($input['role_id']);
            $this->assign('roles',$roles);
            return view();
        } elseif ($request->isPost()) {
            $rolRulM = new RoleModel($this->corp_id);
            $data = [
                'roles'=>$input['roles']
            ];
            $b = $rolRulM->setRole($input['role_id'],$data);
            if ($b > 0) {
                return [
                    'status'=>true,
                    'message'=>'修改权限成功'
                ];
            } else {
                return [
                    'status'=>true,
                    'message'=>'修改权限失败'
                ];
            }
        }
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
        $is_not_use = false;
        $billSettingM = new BillSettingModel($this->corp_id);
        $in_use_bill_setting_count = $billSettingM->getBillSettingByRoleIds($ids);
        if($in_use_bill_setting_count==0){
            $is_not_use = true;
            return $is_not_use;
        }
        $businessFlowItemLinkM = new BusinessFlowItemLinkModel($this->corp_id);
        $in_use_bill_setting_count = $businessFlowItemLinkM->getBusinessFlowSettingByRoleIds($ids);
        if($in_use_bill_setting_count==0){
            $is_not_use = true;
            return $is_not_use;
        }
        $contractSettingM = new ContractSettingModel($this->corp_id);
        $in_use_bill_setting_count = $contractSettingM->getContractSettingByRoleIds($ids);
        if($in_use_bill_setting_count==0){
            $is_not_use = true;
            return $is_not_use;
        }
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