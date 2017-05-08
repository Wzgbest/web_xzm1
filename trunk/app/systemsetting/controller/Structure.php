<?php
/**
 * Created by: messhair
 * Date: 2017/5/8
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\systemsetting\controller\Employer;
use app\common\model\CorporationStructure;
use app\common\model\Employer as EmployerModel;

class Structure extends Initialize
{
    public function index()
    {
        $corp_id = get_corpid();
        $struM = new CorporationStructure($corp_id);
        $structs = $struM->getAllStructure();
        $tree = new \myvendor\Tree($structs,['id','struct_pid']);
        $res = $tree->leaf(0);
        $this->assign('struct',$res);
        return view();
    }

    /**
     * 显示指定部门的员工
     * @param $struct_id 部门id
     * @return \think\response\View
     */
    public function showPointedDepartment($struct_id,$page_now_num=0,$page_row=10)
    {
        $corp_id = get_corpid();
        $employerM = new EmployerModel($corp_id);
        $total_num = $employerM->countEmployerByStructId($struct_id);
        $data = $employerM->getEmployerByStructId($struct_id,$page_now_num,$page_row);
        $res = ['data'=>$data,'page'=>['page_now_num'=>$page_now_num,'page_row'=>$page_row,'total_num'=>$total_num]];
        return $res;
    }

    /**
     * 显示员工详情
     * @param \app\systemsetting\controller\Employer $employer
     * @param $user_id 员工id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function showEmployerInfo(Employer $employer, $user_id)
    {
        $info = $employer->showSingleEmployerInfo($user_id);
        return $info;
    }

    /**
     * 切换员工所在部门
     * @param $user_id 员工id
     * @param $to_group 目标部门id
     * @return array
     */
    public function changeEmployerStructure($user_id,$to_group)
    {
        $corp_id = get_corpid();

        $struM = new CorporationStructure($corp_id);
        $st_res = $struM->getStructureInfo($to_group);
        if (empty($st_res)) {
            return ['status'=>false,'message'=>'选择的部门不存在'];
        }
        $employerM = new EmployerModel($corp_id);
        $data = [
            'structid' => $to_group,
        ];
        $res = $employerM->setSingleEmployerInfobyId($user_id,$data);
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
     */
    public function deleteStructure($struct_id,$trans)
    {
        $corp_id = get_corpid();

        $struM = new CorporationStructure($corp_id);
        $st_res = $struM->getStructureInfo($struct_id);
        if (empty($st_res)) {
            return ['status'=>false,'message'=>'选择的部门不存在'];
        }

        $st_res2 = $struM->getStructureInfo();

        $employerM = new EmployerModel($corp_id);
        $users = $employerM->getEmployerByStructId($struct_id);
        if ($trans == 1) {
            //转移到默认组
            if (empty($users)) {
                $employerM->link->startTrans();
                $b = 1;
            } else {
                $user_ids = [];
                foreach ($users as $val) {
                    $user_ids[] .= $val['user_id'];
                }
                $user_ids = implode(',',$user_ids);
                $data = [
                    'structid' => -1,
                ];
                $employerM->link->startTrans();
                $b = $employerM->setSingleEmployerInfobyIds($user_ids,$data);
            }

            $d = $struM->deleteStructure($struct_id);
            if ($b>0 && $d>0) {
                $employerM->link->commit();
                $info = [
                    'status' =>true,
                    'message' =>'员工移动到默认部门，部门已删除'
                ];
            } else {
                $employerM->link->rollback();
                $info = [
                    'status' =>true,
                    'message' =>'操作失败'
                ];
            }
        } elseif ($trans == 0) {
            //删除所有员工
            if (empty($users)) {
                $employerM->link->startTrans();
                $b = 1;
            } else {
                $user_ids = [];
                foreach ($users as $val) {
                    $user_ids[] .= $val['user_id'];
                }
                $user_ids = implode(',',$user_ids);
                $data = [
                    'structid' => -1,
                ];
                $employerM->link->startTrans();
                $b = $employerM->setSingleEmployerInfobyIds($user_ids,$data);
            }

            $d = $struM->deleteStructure($struct_id);
            if ($b>0 && $d>0) {
                $employerM->link->commit();
                $info = [
                    'status' =>true,
                    'message' =>'员工已删除，部门已删除'
                ];
            } else {
                $employerM->link->rollback();
                $info = [
                    'status' =>true,
                    'message' =>'操作失败'
                ];
            }
        }
        return $info;
    }
}