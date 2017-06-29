<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Corporation;
use app\common\model\Employee as EmployeeModel;
use app\huanxin\service\Api as HuanxinApi;
use app\common\model\StructureEmployee;
use think\Request;
use think\Db;
use app\crm\model\Customer as CustomerModel;
use app\common\model\EmployeeDelete;
use app\common\model\UserCorporation;

class Employee extends Initialize{
    public function index(){}

    /**
     * 员工列表
     * created by blu10ph
     */
    public function manage(Request $request){
        $num = input('num',20,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $uid = session('userinfo.userid');
        $filter = $this->_getCustomerFilter(["structure","role","on_duty","worknum","truename"]);
        $field = $this->_getCustomerField([]);
        try{
            $res = $this->showEmployeeList($request,$p,$num);
            $this->assign("listdata",$res["data"]);
            $employees_count = $res["total_num"];
            $this->assign("count",$employees_count);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($employees_count/$num);
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("start_num",$start_num+1);
        $this->assign("truename",session('userinfo.truename'));
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
            $add_man = input("worknum");
            if($add_man){
                $filter["worknum"] = $add_man;
            }
        }
        if(in_array("truename", $filter_column)){//姓名
            $add_man = input("truename");
            if($add_man){
                $filter["truename"] = $add_man;
            }
        }
        return $filter;
    }
    protected function _getCustomerField($field_column){
        $field = [];
        $fields = input('field',"",'string');
        $fields_arr = explode(',',$fields);
        $fields_arr = array_filter($fields_arr);
        $fields_arr = array_unique($fields_arr);
//        if(in_array("in_column", $field_column) && in_array("in_column", $fields_arr)){//所在列
//            $field[] = "in_column";
//        }
        return $field;
    }

    protected function _showEmployee(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }
        $this->assign("id",$id);
        $this->assign("fr",input('fr'));
        $employee = $this->showSingleEmployeeInfo($id);
        $this->assign("employee",$employee);
    }
    public function show(){
        $this->_showEmployee();
        return view();
    }
    public function edit(){
        $this->_showEmployee();
        return view();
    }
    /**
     * 查看员工详情
     * @param $user_id int 员工id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function showSingleEmployeeInfo($user_id)
    {
        $employeeM = new EmployeeModel($this->corp_id);
        $info = $employeeM->getEmployeeByUserid($user_id);
        return $info;
    }

    /**
     * 分页展示员工列表
     * @param $request Request 请求参数
     * @param int $page_now_num 当前页
     * @param int $page_rows 行数
     * @return array
     * created by messhair
     */
    public function showEmployeeList(Request $request,$page_now_num = 0, $page_rows = 10)
    {
        $input = $request->param();
        $employeeM = new EmployeeModel($this->corp_id);
        $res = $employeeM->getPageEmployeeList($page_now_num,$page_rows,$input);
        $count = $employeeM->countPageEmployeeList($input);
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
     * @param $request Request 增加员工页面提交信息
     * @return array
     * created by messhair
     */
    public function addEmployee(Request $request)
    {
        if ($request->isGet()) {
            return view();
        } elseif ($request->isPost()) {
            $input = $request->param();
            $result = $this->validate($input,'Employee');
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
            $employeeM = new EmployeeModel($this->corp_id);
            $struct_empM = new StructureEmployee($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;

            UserCorporation::startTrans();
            $employeeM->link->startTrans();
            try{
                //员工表增加信息
                $id = $employeeM->addSingleEmployee($input);
                $user_tel = ['telephone'=>$input['telephone'],'corp_name'=>$this->corp_id];
                $b = UserCorporation::addSingleUserTel($user_tel);
                //部门表增加信息
                if ($input['is_leader'] == 1) {
                    foreach ($struct_ids as $k=>$v) {
                        $struct_data[$k]['user_id'] =$id;
                        $struct_data[$k]['struct_id'] = $v;
                    }
                    $f = $struct_empM->addMultipleStructureEmployee($struct_data);
                } else {
                    $struct_data['user_id'] = $id;
                    $struct_data['struct_id'] = $struct_ids;
                    $f = $struct_empM->addStructureEmployee($struct_data);
                }
                if ($id > 0 && $f > 0 && $b > 0) {
                    //环信增加好友
                    $d = $huanxin->addFriend($this->corp_id,$input['telephone']);//TODO 测试注释掉
//                $d['status'] = true;//TODO 测试开启
                    if ($d['status']) {
                        $tel = [];
                        array_push($tel,$input['telephone']);
                        $im = $employeeM->saveIm($tel);
                    } else {
                        $employeeM->link->rollback();
                        UserCorporation::rollback();
                        $info['message'] = '添加环信好友有失败，联系管理员';
                        $info['error'] = $d['error'];
                        return $info;
                    }
                } else {
                    $employeeM->link->rollback();
                    UserCorporation::rollback();
                    $info['message'] = '添加员工失败，联系管理员';
                    return $info;
                }
                if ($id > 0 && $f >0 && $d['status'] && $b > 0 && $im > 0) {
                    $employeeM->link->commit();
                    UserCorporation::commit();
                    return [
                        'status' => true,
                        'message' => '新增员工成功，添加环信好友成功',
                    ];
                } else {
                    $employeeM->link->rollback();
                    UserCorporation::rollback();
                    $info['message'] = '新增员工失败，或添加环信好友失败';
                    return $info;
                }
            }catch (\Exception $e){
                $employeeM->link->rollback();
                UserCorporation::rollback();
                $info['message'] = $e->getMessage();
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
    public function editEmployee(Request $request, $user_id)
    {
        if ($request->isGet()) {
            $employeeM = new EmployeeModel($this->corp_id);
            $structM = new StructureEmployee($this->corp_id);
            $employee_info = $employeeM->getEmployeeByUserid($user_id);
            $struct_info = $structM->getEmployeeStructure($user_id);
            $employee_info['struct_info'] = $struct_info;
            return $employee_info;
        } elseif ($request->isPost()) {
            $input = $request->param();
            $result = $this->validate($input,'Employee');
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
            $employeeM = new EmployeeModel($this->corp_id);
            $struct_empM = new StructureEmployee($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;
            //取出旧设置的部门ids
            $struct_old = $struct_empM->getStructIdsByEmployee($user_id);
            $struct_ = [];
            foreach ($struct_old as $val) {
                $struct_[] .=$val['struct_id'];
            }

            $employeeM->link->startTrans();
            try{
                //员工表修改信息
                $em_res = $employeeM->setSingleEmployeeInfobyId($user_id,$input);

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
                            $res = $struct_empM->addMultipleStructureEmployee($insert_data);
                        } else {
                            $res = $struct_empM->addStructureEmployee($insert_data);
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
                        $del_res = $struct_empM->deleteMultipleStructureEmployee($user_id,$delete_data);
                    } else {
                        $del_res = 1;
                    }
                } else {
                    //非领导
                    $struct_data['user_id'] = $user_id;
                    $struct_data['struct_id'] = $struct_ids[0];
                    $res = $struct_empM->setStructureEmployeeById($user_id,$struct_old[0]['struct_id'],$struct_data);
                    if ($res ===0) {
                        $res =1;
                    }
                    $del_res = 1;
                }
                if ($em_res >= 0 && $res>0 && $del_res>0) {
                    $employeeM->link->commit();
                    return [
                        'status' => true,
                        'message' => '修改员工信息成功',
                    ];
                } else {
                    $employeeM->link->rollback();
                    $info['message'] = '修改员工信息失败';
                    return $info;
                }
            }catch (\Exception $ex){
                $employeeM->link->rollback();
                $info['message'] = $ex->getMessage();
                return $info;
            }
        }
    }

    /**
     * 删除单个员工、多个员工
     * @param $user_ids string 用户ids，逗号分隔
     * @return array
     * created by messhair
     */
    public function deleteMultipleEmployee($user_ids)
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
            $employeeM = new EmployeeModel();
            $dat = $employeeM->getEmployeeByUserids($user_ids);
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
                $emp_delM = new EmployeeDelete();
                $stru_empM = new StructureEmployee();
                $emp_delM->link->startTrans();
                Corporation::startTrans();
                try{
//                删除员工
                    $b = $employeeM->deleteMultipleEmployee($user_ids);
//                转移到employee_delete表
                    $d =$emp_delM->addMultipleBackupInfo($users);
//                    删除用户公司对照表信息
                    $f = UserCorporation::deleteUserCorp($tel_arr);
//                    删除部门员工表信息
                    $g = $stru_empM->deleteMultipleStructureEmployee($user_ids);
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
}