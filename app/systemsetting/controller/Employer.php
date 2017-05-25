<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Corporation;
use app\common\model\Employer as EmployerModel;
use app\huanxin\service\Api as HuanxinApi;
use app\common\model\StructureEmployer;
use think\Request;
use app\crm\model\Customer as CustomerModel;
use app\common\model\EmployerDelete;
use app\common\model\UserCorporation;

class Employer extends Initialize
{
    public function index()
    {}

    /**
     * 查看员工详情
     * @param $user_id 员工id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function showSingleEmployerInfo($user_id)
    {
        $employerM = new EmployerModel($this->corp_id);
        $info = $employerM->getEmployerByUserid($user_id);
        return $info;
    }

    /**
     * 分页展示员工列表
     * @param int $page_now_num 当前页
     * @param int $page_rows 行数
     * @param int $struct_id 部门id
     * @param int $role 角色id
     * @param int $on_duty 状态 1在职 2休假 -1离职 0默认
     * @return array
     * created by messhair
     */
    public function showEmployerList(Request $request,$page_now_num = 0, $page_rows = 10)
    {
        $input = $request->param();
        $employerM = new EmployerModel($this->corp_id);
        $res = $employerM->getPageEmployerList($page_now_num,$page_rows,$input);
        $count = $employerM->countPageEmployerList($input);
        $count = empty($count)? 0:$count[0]['num'];
        return [
            'data'=>$res,
            'page_now_num'=>$page_now_num,
            'page_row'=>$page_rows,
            'total_num'=>$count,
        ];
    }

    /**
     * 添加员工
     * @param $input 增加员工页面提交信息
     * @return array
     * created by messhair
     */
    public function addEmployer(Request $request)
    {
        if ($request->isGet()) {
            return view();
        } elseif ($request->isPost()) {
            $input = $request->param();
            $result = $this->validate($input,'Employer');
            $info['status'] = false;
            //验证字段
            if(true !== $result){
                $info['message'] = $result;
                return $info;
            }
            if ($input['is_leader'] == 0) {
                if (count($input['struct_id'])>1) {
                    return [
                        'status' =>false,
                        'message' =>'非领导不可选择多部门',
                    ];
                }
            }
            $struct_ids = $input['struct_id'];
            unset($input['struct_id']);
            $employerM = new EmployerModel($this->corp_id);
            $struct_empM = new StructureEmployer($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;

            UserCorporation::startTrans();
            $employerM->link->startTrans();
            try{
                //员工表增加信息
                $id = $employerM->addSingleEmployer($input);
                $user_tel = ['telephone'=>$input['telephone'],'corp_name'=>$this->corp_id];
                $b = UserCorporation::addSingleUserTel($user_tel);
                //部门表增加信息
                if ($input['is_leader'] == 1) {
                    foreach ($struct_ids as $k=>$v) {
                        $struct_data[$k]['user_id'] =$id;
                        $struct_data[$k]['struct_id'] = $v;
                    }
                    $f = $struct_empM->addMultipleStructureEmployer($struct_data);
                } else {
                    $struct_data['user_id'] = $id;
                    $struct_data['struct_id'] = $struct_ids;
                    $f = $struct_empM->addStructureEmployer($struct_data);
                }
                if ($id > 0 && $f > 0 && $b > 0) {
                    //环信增加好友
                    $d = $huanxin->addFriend($input['telephone']);//TODO 测试注释掉
//                $d['status'] = true;//TODO 测试开启
                    if ($d['status']) {
                        $tel = [];
                        array_push($tel,$input['telephone']);
                        $im = $employerM->saveIm($tel);
                    } else {
                        $employerM->link->rollback();
                        UserCorporation::rollback();
                        $info['message'] = '添加环信好友有失败，联系管理员';
                        $info['error'] = $d['error'];
                        return $info;
                    }
                } else {
                    $employerM->link->rollback();
                    UserCorporation::rollback();
                    $info['message'] = '添加员工失败，联系管理员';
                    return $info;
                }
            }catch (\Exception $e){
                $employerM->link->rollback();
                UserCorporation::rollback();
            }
            if ($id > 0 && $f >0 && $d['status'] && $b > 0 && $im > 0) {
                $employerM->link->commit();
                UserCorporation::commit();
                return [
                    'status' => true,
                    'message' => '新增员工成功，添加环信好友成功',
                ];
            } else {
                $employerM->link->rollback();
                UserCorporation::rollback();
                $info['message'] = '新增员工失败，或添加环信好友失败';
                return $info;
            }
        }
    }

    /**
     * 编辑员工信息
     * @param Request $request
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function editEmployer(Request $request, $user_id)
    {
        if ($request->isGet()) {
            $employerM = new EmployerModel($this->corp_id);
            $structM = new StructureEmployer($this->corp_id);
            $employer_info = $employerM->getEmployerByUserid($user_id);
            $struct_info = $structM->getEmployerStructure($user_id);
            $employer_info['struct_info'] = $struct_info;
            return $employer_info;
        } elseif ($request->isPost()) {
            $input = $request->param();
            $result = $this->validate($input,'Employer');
            $info['status'] = false;
            //验证字段
            if(true !== $result){
                $info['message'] = $result;
                return $info;
            }
            if ($input['is_leader'] == 0) {
                if (count($input['struct_id'])>1) {
                    return [
                        'status' =>false,
                        'message' =>'非领导不可选择多部门',
                    ];
                }
            }
            $struct_ids = $input['struct_id'];
            $user_id = $input['user_id'];
            unset($input['struct_id']);
            unset($input['user_id']);
            $employerM = new EmployerModel($this->corp_id);
            $struct_empM = new StructureEmployer($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;
            //取出旧设置的部门ids
            $struct_old = $struct_empM->getStructIdsByEmployer($user_id);
            $struct_ = [];
            foreach ($struct_old as $val) {
                $struct_[] .=$val['struct_id'];
            }

            $employerM->link->startTrans();
            try{
                //员工表修改信息
                $em_res = $employerM->setSingleEmployerInfobyId($user_id,$input);

                //部门表修改信息，1,2,3 --->  1,2,3,4,5   1,2,3,4,5--->1,2,3
                if ($input['is_leader'] == 1) {
                    $insert = array_diff($struct_ids,$struct_);//新添加的
                    $delete = array_diff($struct_,$struct_ids);//需要删除的
                    //有需要添加的
                    if (!empty($insert)) {
                        $insert_data = [];
                        foreach ($insert as $k=>$v) {
                            array_push($insert_data,['user_id'=>$user_id,'struct_id'=>$v]);
                        }
                        if (count($insert_data) >1) {
                            $res = $struct_empM->addMultipleStructureEmployer($insert_data);
                        } else {
                            $res = $struct_empM->addStructureEmployer($insert_data);
                        }
                    } else {
                        $res = 1;
                    }

                    //有需要删除的
                    if (!empty($delete)) {
                        $delete_data = [];
                        foreach ($delete as $k=>$v) {
                            array_push($delete_data,$v);
                        }
                        $del_res = $struct_empM->deleteMultipleStructureEmployer($user_id,$delete_data);
                    } else {
                        $del_res = 1;
                    }
                } else {
                    //非领导
                    $struct_data['user_id'] = $user_id;
                    $struct_data['struct_id'] = $struct_ids[0];
                    $res = $struct_empM->setStructureEmployerById($user_id,$struct_old[0]['struct_id'],$struct_data);
                    if ($res ===0) {
                        $res =1;
                    }
                    $del_res = 1;
                }
            }catch (\Exception $e){
                $employerM->link->rollback();
            }
            if ($em_res >= 0 && $res>0 && $del_res>0) {
                $employerM->link->commit();
                return [
                    'status' => true,
                    'message' => '修改员工信息成功',
                ];
            } else {
                $employerM->link->rollback();
                $info['message'] = '修改员工信息失败';
                return $info;
            }
        }
    }

    /**
     * 删除单个员工、多个员工
     * @param $user_ids 用户ids，逗号分隔
     * @return array
     * created by messhair
     */
    public function deleteMultipleEmployer($user_ids)
    {
        $customerM = new CustomerModel();
//        检测有无保护客户
        $res = $customerM->getCustomersByUserIds($user_ids); $res =null;
        if (!empty($res)) {
            $arr=[];
            foreach ($res as $k=>$v) {
                array_push($arr,$v['truename']);
            }
            $arr = array_unique($arr);
            if (count($arr) < 4) {
                $str = implode(',',$arr);
            } else {
                $str = $arr[0].','.$arr[1].','.$arr[2];
            }
            return [
                'status'=>false,
                'message'=>$str.'等用户有未释放的客户，删除失败',
            ];
        } else {
//        查询员工状态是否为离职
            $employerM = new EmployerModel();
            $dat = $employerM->getEmployerByUserids($user_ids);
            if (!empty($dat)) {
                $arr=[];
                $users=[];
                $tel_arr=[];
                $names = [];
                foreach ($dat as $k=>$v) {
                    if ($v['status'] ==1) {
                        array_push($arr,$v['truename']);
                    }
                    unset($v['status']);
                    unset($v['on_duty']);
                    array_push($users,$v);
                    array_push($tel_arr,$v['telephone']);
                    array_push($names,$v['truename']);
                }
                if (!empty($arr)) {
                    $str = count($arr) < 4 ? implode(',',$arr) : $arr[0].','.$arr[1].','.$arr[2];
                    return [
                        'status'=>false,
                        'message'=>$str.'等员工未改为离职状态，无法删除'
                    ];
                }
                $tel_arr = implode(',',$tel_arr);
                $names = implode(',',$names);
                $emp_delM = new EmployerDelete();
                $stru_empM = new StructureEmployer();
                $emp_delM->link->startTrans();
                Corporation::startTrans();
                try{
//                删除员工
                    $b = $employerM->deleteMultipleEmployer($user_ids);
//                转移到employer_delete表
                    $d =$emp_delM->addMultipleBackupInfo($users);
//                    删除用户公司对照表信息
                    $f = UserCorporation::deleteUserCorp($tel_arr);
//                    删除部门员工表信息
                    $g = $stru_empM->deleteMultipleStructureEmployer($user_ids);
                }catch(\Exception $e){
                    $emp_delM->link->rollback();
                    UserCorporation::rollback();
                }
                if ($b > 0 && $d > 0 && $f > 0 && $g > 0) {
                    $emp_delM->link->commit();
                    UserCorporation::commit();
                    write_log(session('userinfo')['userid'],6,'删除员工'.$names.'成功',$this->corp_id);
                    return [
                        'status'=>true,
                        'message'=>'删除员工成功',
                    ];
                } else {
                    $emp_delM->link->rollback();
                    UserCorporation::rollback();
                    return [
                        'status'=>false,
                        'message' =>'删除员工失败',
                    ];
                }
            } else {
                return [
                    'status'=>false,
                    'message'=>'员工不存在'
                ];
            }
        }
    }

    public function importEmployer(){
        $file_id = input("file_id",0,"int");
    }
}