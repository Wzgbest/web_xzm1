<?php
/**
 * Created by: messhair
 * Date: 2017/5/8
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\systemsetting\controller\Employee;
use app\common\model\Employee as EmployeeModel;
use app\common\model\Structure as StructureModel;
use app\common\model\StructureEmployee as StructureEmployeeModel;

class Structure extends Initialize
{
    /**
     * 首页显示
     * @return \think\response\View
     * created by messhair
     */
    public function index(){
        $root_id = 1;
        $struM = new StructureModel();
        $structs = $struM->getAllStructure();
        $tree = new \myvendor\Tree($structs,['id','struct_pid']);
        $res = $tree->leaf($root_id);
        $this->assign('struct',$res);
        $this->assign('struct_json',json_encode($res));
        $this->assign('root_id',$root_id);
        return view();
    }

    /**
     * 显示指定部门的员工
     * @param $struct_id 部门id
     * @return \think\response\View
     * created by messhair
     */
    public function showPointedDepartment($struct_id,$page_now_num=0,$page_row=10)
    {
        $employeeM = new EmployeeModel();
        $total_num = $employeeM->countEmployeeByStructId($struct_id);
        $data = $employeeM->getEmployeeByStructId($struct_id,$page_now_num,$page_row);
        $res = ['data'=>$data,'page'=>['page_now_num'=>$page_now_num,'page_row'=>$page_row,'total_num'=>$total_num]];
        return $res;
    }

    /**
     * 显示员工详情
     * @param \app\systemsetting\controller\Employee $employee
     * @param $user_id 员工id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function showEmployeeInfo(Employee $employee, $user_id)
    {
        $info = $employee->showSingleEmployeeInfo($user_id);
        return $info;
    }

    /**
     * 部门重命名
     * @param $struct_id 部门id
     * @param $new_name 新名称
     * @return array
     * created by messhair
     */
    public function renameStructure($struct_id,$new_name)
    {
        $struM = new StructureModel();
        $data = [
            'struct_name'=>$new_name,
        ];
        $b = $struM->setStructure($struct_id,$data);
        if ($b>=0) {
            $info = [
                'status'=>true,
                'message'=>'更改部门名称成功',
            ];
        } else {
            $info = [
                'status' => false,
                'message'=> '更改部门名称失败',
            ];
        }
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
        $struM = new CorporationStructure();
        $st_res = $struM->getStructureInfo($to_group);
        if (empty($st_res)) {
            return ['status'=>false,'message'=>'选择的部门不存在'];
        }
        $employeeM = new StructureEmployeeModel();
        $data = [
            'struct_id' => $to_group,
        ];
        $res = $employeeM->setStructureEmployeeById($user_id,$group,$data);
        if ($res >0) {
            $info=[
                'status' =>true,
                'message' => '更换部门成功',
            ];
        } else {
            $info = [
                'status'=>false,
                'message'=>'更换部门失败或未更换部门'
            ];
        }
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
        $struM = new StructureModel();
        $st_res = $struM->getStructureInfo($struct_id);
        if (empty($st_res)) {
            return ['status'=>false,'message'=>'选择的部门不存在'];
        }

        $st_res_all = $struM->getAllStructure();
        $ids = deep_get_ids($st_res_all,$struct_id);
        $ids = implode(',',$ids);

        $employeeM = new StructureEmployeeModel();
        $users = $employeeM->getEmployeeByStructIds($ids);
        if ($trans == 1) {
            //转移员工到默认组
            if (empty($users)) {
                $employeeM->link->startTrans();
                $b = 1;
            } else {
                $user_ids = [];
                foreach ($users as $val) {
                    $user_ids[] .= $val['id'];
                }
                $user_ids = implode(',',$user_ids);
                $data = [
                    'struct_id' => -1,
                ];
                $employeeM->link->startTrans();
                $b = $employeeM->setStructureEmployeebyIds($user_ids,$data);
            }

            $d = $struM->deleteStructure($ids);
            if ($b>0 && $d>0) {
                $employeeM->link->commit();
                $info = [
                    'status' =>true,
                    'message' =>'员工移动到默认部门，部门已删除'
                ];
            } else {
                $employeeM->link->rollback();
                $info = [
                    'status' =>true,
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
                    $user_ids[] .= $val['id'];
                }
                $user_ids = implode(',',$user_ids);
                $data = [
                    'struct_id' => -1,
                ];
                $employeeM->link->startTrans();
                $b = $employeeM->setStructureEmployeebyIds($user_ids,$data);
            }

            $d = $struM->deleteStructure($ids);
            if ($b>0 && $d>0) {
                $employeeM->link->commit();
                $info = [
                    'status' =>true,
                    'message' =>'员工已删除，部门已删除'
                ];
            } else {
                $employeeM->link->rollback();
                $info = [
                    'status' =>true,
                    'message' =>'操作失败'
                ];
            }
        }
        return $info;
    }
}