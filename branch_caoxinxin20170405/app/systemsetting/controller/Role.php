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
        $roleM = new RoleModel();
        $roles = $roleM->getAllRole();
        $this->assign('roles',$roles);
        return view();
    }

    public function rule_manage(){
        /*$rules_str_arr = array_column($roles,"rules");
        $rule_ids = [];
        foreach ($rules_str_arr as $rules_str){
            $rule_ids = array_merge($rule_ids,explode(",",$rules_str));
        }
        $ruleM = new RuleModel();
        $rules = $ruleM->getRulesColumnByIds($rule_ids);
        //var_exp($rules,'$rules',1);
        foreach ($roles as &$role){
            $rules_arr = [];
            $rules_id_arr = explode(",",$role["rules"]);
            foreach ($rules_id_arr as $rules_id){
                $rules_arr[] = ["id"=>$rules_id,"rule_name"=>$rules[$rules_id]["rule_name"],"rule_title"=>$rules[$rules_id]["rule_title"]];
            }
            $role["rules"] = $rules_arr;
        }
        $this->assign('rules',$rules);*/
        return view();
    }

    public function employee_list(){
        $rule_id = input("id",0,"int");
        if(!$rule_id){
            $this->error("参数错误!");
        }
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        try{
            $employeeM = new Employee();
            $employee_list = $employeeM->getEmployeeByRole($rule_id,$start_num,$num);
            //var_exp($employee_list,'$employee_list',1);
            $this->assign('listdata',$employee_list);
            $employees_count = $employeeM->getEmployeeCountByRole($rule_id);
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
        $this->assign("id",$rule_id);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
        return view();
    }

    /**
     * 角色列表
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function showRoles()
    {
        $rol = new RoleModel();
        $data = $rol->getAllRole();
        return $data;
    }

    /**
     * 显示角色对应的权限
     * @param $role_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function showRules($role_id)
    {
        $rol = new RoleModel();
        $rules = $rol->getRoleInfo($role_id);
        return $rules;
    }

    /**
     * 添加角色
     * @param $role_name string 职位名称
     * @return array created by messhair
     * created by messhair
     */
    public function addRole($role_name)
    {
        $rol = new RoleModel();
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
    public function editRole(Request $request)
    {
        $input = $request->param();
        $rol = new RoleModel();
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
     * @param Request $request  ['role_id','rules']
     * @return array|\think\response\View
     * created by messhair
     */
    public function editRoleRule(Request $request)
    {
        $input = $request->param();
        if ($request->isGet()) {
            $rolRulM = new RoleModel();
            $rules = $rolRulM->getRoleInfo($input['role_id']);
            $this->assign('rules',$rules);
            return view();
        } elseif ($request->isPost()) {
            $rolRulM = new RoleModel();
            $data = [
                'rules'=>$input['rules']
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
    public function showRoleMember(Request $request)
    {
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
    public function addRoleMember(Request $request)
    {
        $input = $request->param();
        $employeeM = new Employee();
        if ($request->isGet()) {
            $data = $employeeM->getEmployeeByNotRole($input['role_id'],$input['struct_id'],$input['user_tel_email']);
            $this->assign('data',$data);
            return view();
        } elseif ($request->isPost()) {
            $data = ['role'=>$input['role_id']];
            $b = $employeeM->setMultipleEmployeeInfoByIds($input['user_ids'],$data);
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

    /**
     * 删除角色
     * @param Request $request
     * @return array
     * created by messhair
     */
    public function deleteRole(Request $request)
    {
        $input = $request->param();
        $employeeM = new Employee();
        $res = $employeeM->getEmployeeByRole($input['role_id']);
        if (!empty($res)) {
            return [
                'status'=>false,
                'message'=>'该角色包含成员，不能删除'
            ];
        }
        $rolM = new RoleModel();
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
    public function deleteRoleMember(Request $request)
    {
        $input = $request->param();
        $employeeM = new Employee();
        $data = ['role'=>''];
        $b = $employeeM->setSingleEmployeeInfobyId($input['user_id'], $data);
        if ($b > 0) {
            return [
                'status'=>true,
                'message'=>'删除角色成员成功'
            ];
        } else {
            return [
                'status'=>false,
                'message'=>'删除角色成员失败'
            ];
        }
    }

    /**
     * 查看员工详情
     * @param \app\systemsetting\controller\Employee $employee
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function showEmployeeInfo(EmployeeController $employee)
    {
        $input = input('param.');
        $res = $employee->showSingleEmployeeInfo($input['user_id']);
        return $res;
    }
}