<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Employer;
use app\common\model\Role as RoleModel;
use think\Request;
use app\systemsetting\controller\Employer as EmployerController;

class Role extends Initialize
{
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
     * created by messhair
     */
    public function showRules($role_id)
    {
        $rol = new RoleModel();
        $rules = $rol->getRoleInfo($role_id);
        $this->assign('role',$rules);
        return view();
    }

    /**
     * 添加角色
     * @return array
     * created by messhair
     */
    public function addRole()
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
        $employerM = new Employer();
        $res = $employerM->getEmployerByRole($input['role_id']);
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
        $employerM = new Employer();
        if ($request->isGet()) {
            $data = $employerM->getEmployerByNotRole($input['role_id'],$input['struct_id'],$input['user_tel_email']);
            $this->assign('data',$data);
            return view();
        } elseif ($request->isPost()) {
            $data = ['role'=>$input['role_id']];
            $b = $employerM->setMultipleEmployerInfoByIds($input['user_ids'],$data);
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
        $employerM = new Employer();
        $res = $employerM->getEmployerByRole($input['role_id']);
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
        $employerM = new Employer();
        $data = ['role'=>''];
        $b = $employerM->setSingleEmployerInfobyId($input['user_id'], $data);
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
     * @param \app\systemsetting\controller\Employer $employer
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function showEmployerInfo(EmployerController $employer)
    {
        $input = input('param.');
        $res = $employer->showSingleEmployerInfo($input['user_id']);
        return $res;
    }
}