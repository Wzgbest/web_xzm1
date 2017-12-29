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
use app\common\model\Structure as StructureModel;
use app\common\model\Role as RoleModel;
use app\common\model\RoleEmployee;

class Employee extends Initialize{
    var $default_password = 87654321;
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){}

    /**
     * 员工列表
     * created by blu10ph
     */
    public function manage(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $employees_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $filter = $this->_getCustomerFilter(["structure","role","on_duty","worknum","truename"]);
        try{
            $employeeM = new EmployeeModel($this->corp_id);
            $listdata = $employeeM->getPageEmployeeList($start_num,$num,$filter);
            //var_exp($listdata,'$listdata',1);
            $this->assign("listdata",$listdata);
            $count = $employeeM->countPageEmployeeList($filter);
            $employees_count = empty($count)? 0:$count;
            $this->assign("count",$employees_count);
            $struM = new StructureModel($this->corp_id);
            $structs = $struM->getAllStructure();
            $this->assign("structs",$structs);
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
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("start_num",$employees_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$employees_count?$end_num:$employees_count);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
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
        $employee["role_id"] = explode(",",$employee["role_id"]);
        //var_exp($employee,'$employee',1);
        $this->assign("employee",$employee);
        $struM = new StructureModel($this->corp_id);
        $structs = $struM->getAllStructure();
        $this->assign("structs",$structs);
        $rolM = new RoleModel($this->corp_id);
        $roles = $rolM->getAllRole();
        $this->assign("roles",$roles);
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
        //var_exp($info,'$info',1);
        return $info;
    }

    /**
     * 分页展示员工列表
     * @param int $page 当前页
     * @param int $page_rows 行数
     * @return array
     * created by messhair
     */
    public function showEmployeeList($page = 0, $page_rows = 10)
    {
        $filter = $this->_getCustomerFilter(["structure","role","on_duty","worknum","truename"]);
        $employeeM = new EmployeeModel($this->corp_id);
        $res = $employeeM->getPageEmployeeList($page,$page_rows,$filter);
        $count = $employeeM->countPageEmployeeList($filter);
        $count = empty($count)? 0:$count;
        return [
            'data'=>$res,
            'page'=>$page,
            'page_row'=>$page_rows,
            'total_num'=>$count,
        ];
    }

    public function checkPhone(){
        $result = ['status'=>0 ,'info'=>"查询时发生错误！"];
        $phone = input('phone',0,'int');
        $employeeM = new EmployeeModel($this->corp_id);
        $check_flg = $employeeM->getEmployeeByTel($phone);
        if($check_flg){
            $result['info'] = "电话已被使用！";
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "电话未被使用！";
        return json($result);
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
            $input = array_intersect_key($request->param(),[
                "telephone"=>"",
                "truename"=>"",
                "nickname"=>"",
                "gender"=>"",
                "wired_phone"=>"",
                "part_phone"=>"",
                "on_duty"=>"",
                "worknum"=>"",
                "is_leader"=>"",
                "email"=>"",
                "qqnum"=>"",
                "wechat"=>"",
                "struct_id"=>"",
                "role"=>"",
                "user_id"=>""
            ]);
            $struct_ids = $input['struct_id'];
            $structrues_id = $input['struct_id'];
            $role_ids = $input['role'];
            if(!$struct_ids || !$role_ids){
                return [
                    'status' =>false,
                    'message' =>'参数错误!',
                ];
            }
            $result = $this->validate($input,'Employee');
            //var_exp($result,'$result',1);
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
            unset($input['struct_id']);
            unset($input['role']);
            if($input["on_duty"]==-1){
                $input["on_duty"] = 1;
                $input["status"] = -1;
            }
            $input["create_time"] = time();
            $employeeM = new EmployeeModel($this->corp_id);
            $struct_empM = new StructureEmployee($this->corp_id);
            $structM = new StructureModel($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;

            UserCorporation::startTrans();
            $employeeM->link->startTrans();
            try{
                $check_flg = $employeeM->getEmployeeByTel($input['telephone']);
                if($check_flg){
                    $employeeM->link->rollback();
                    UserCorporation::rollback();
                    $result['message'] = "手机号已存在！";
                    return json($result);
                }
                //员工表增加信息
                $input["password"] = md5($this->default_password);
                $input["userpic"] = "/static/images/".($input["gender"]?"default_head_man.jpg":"default_head_woman.jpg");
                $id = $employeeM->addSingleEmployee($input);
                $user_tel = ['telephone'=>$input['telephone'],'corp_name'=>$this->corp_id];
                $b = UserCorporation::addSingleUserTel($user_tel);
                //部门表增加信息
                if ($input['is_leader'] == 1) {
                    $struct_ids = explode(",",$struct_ids);
                    $struct_data=[];
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
                $role_empM = new RoleEmployee($this->corp_id);
                $role_ids = explode(",",$role_ids);
                $role_data=[];
                foreach ($role_ids as $k=>$v) {
                    $role_data[$k]['user_id'] =$id;
                    $role_data[$k]['role_id'] = $v;
                }
                $r = $role_empM->createMultipleRoleEmployee($role_data);
                if ($id > 0 && $f > 0 && $b > 0 && $r > 0) {
                    //环信增加帐号
                    $d = $huanxin->regUser($this->corp_id,$this->corp_id."_".$id,$input["password"],$input['truename']);//TODO 测试注释掉
                    //$d['status'] = true;//TODO 测试开启
                    //var_dump($d,'$d',1);
                    if (!$d['status']) {
                        $employeeM->link->rollback();
                        UserCorporation::rollback();
                        $info['message'] = '添加IM帐号失败，请联系管理员';
                        $info['error'] = $d['error'];
                        return $info;
                    }
                    
                    $struct_ids = explode(",",$structrues_id);
                    $insert_group = $structM->getStructureGroup($struct_ids);
                    // var_dump($insert_group);die();
                    // if (count($insert_group) > 1) {
                        $in_g = $huanxin->addUserFromMoreGroup($this->corp_id."_".$id,$insert_group);
                    // }else{
                    //     $in_g = $huanxin->addOneEmployee($insert_group[0],$this->corp_id."_".$id);
                    // }
                    if (!$in_g['status']) {
                        $employeeM->link->rollback();
                        UserCorporation::rollback();
                        $info['message'] = '添加群组失败，请联系管理员';
                        $info['error'] = $in_g['error'];
                        return $info;
                    }
                    
                } else {
                    $employeeM->link->rollback();
                    UserCorporation::rollback();
                    $info['message'] = '添加员工失败，联系管理员';
                    return $info;
                }
                if ($id > 0 && $f >0 && $d['status'] && $in_g['status'] && $b > 0) {
                    $employeeM->link->commit();
                    UserCorporation::commit();
                    return [
                        'status' => true,
                        'message' => '新增员工成功，添加IM帐号成功',
                    ];
                } else {
                    $employeeM->link->rollback();
                    UserCorporation::rollback();
                    $info['message'] = '新增员工失败，或添加IM帐号失败';
                    return $info;
                }
            }catch (\Exception $ex){
                $employeeM->link->rollback();
                UserCorporation::rollback();
                $info['message'] = $ex->getMessage();
                return $info;
            }
        }
    }

    public function changePhone(){
        $result = ['status'=>0 ,'info'=>"更换手机号时发生错误！"];
        $phone = input('phone',0,'int');
        $employeeM = new EmployeeModel($this->corp_id);
        $check_flg = $employeeM->getEmployeeByTel($phone);
        if($check_flg){
            $result['info'] = "更换手机号失败！";
            return json($result);
        }
        $this->telephone = $phone;
        $employM = new EmployeeModel($this->corp_id);
        $user_info = $employM->getEmployeeByTel($this->telephone);
        set_userinfo($this->corp_id,$this->telephone,$user_info);
        set_telephone_by_token($this->access_token,$this->telephone);
        set_token_by_cookie($this->access_token);
        set_user_device($this->telephone,$this->access_token,$this->device_type,$this->corp_id,$this->uid);
        $result['status'] = 1;
        $result['info'] = "更换手机号成功！";
        return json($result);
    }

    public function changeEmployeePhone($user_id,$telephone){
        $result = ['status'=>0 ,'info'=>"更换手机号时发生错误！"];
        $id = $user_id?$user_id:input('id',0,'int');
        $phone = $telephone?$telephone:input('phone',0,'int');
        $employeeM = new EmployeeModel($this->corp_id);
        $check_flg = $employeeM->getEmployeeByUserid($id);
        if(!$check_flg){
            $result['info'] = "未找到员工！";
            return $result;
        }
        $check_flg = $employeeM->getEmployeeByTel($telephone);
        if($check_flg){
            $result['info'] = "手机号已经存在！";
            return $result;
        }
        $check_flg = $employeeM->setEmployeeSingleInfoById($id,["telephone"=>$phone]);
        $check_flg1=UserCorporation::setUserCorpByPhone($phone);
        if(!$check_flg || $check_flg1===false){
            $result['info'] = "更换手机号失败！";
            return $result;
        }
        $result['status'] = 1;
        $result['info'] = "更换手机号成功！";
        return $result;
    }

    /**
     * 编辑员工信息
     * @param Request $request
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function editEmployee(Request $request)
    {
        if(!($this->checkRule('systemsetting/employee/manage/edit'))){
            $result=$this->noRole();
            $result['message']=$result['info'];
            return json($result);
        }

        if($request->isPost()) {
            $input = array_intersect_key($request->param(),[
                "truename"=>"",
                "nickname"=>"",
                "gender"=>"",
                "wired_phone"=>"",
                "part_phone"=>"",
                "on_duty"=>"",
                "worknum"=>"",
                "is_leader"=>"",
                "email"=>"",
                "qqnum"=>"",
                "wechat"=>"",
                "struct_id"=>"",
                "role"=>"",
                "user_id"=>"",
                "telephone"=>""
            ]);
//             var_dump($input);die();
//            $input["telephone"]=15888888888;
            $result = $this->validate($input,'Employee');
            $user_id = $input['user_id'];
            $employeeM = new EmployeeModel($this->corp_id);
            $employee_info = $employeeM->getEmployeeByUserid($user_id);
            $telephone=$input['telephone'];
            $pre_telephone=$employee_info['telephone'];
            unset($input["telephone"]);
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
            $struct_ids = explode(",",$input['struct_id']);
            $role_ids = explode(",",$input['role']);

            if($input["on_duty"]==-1){
                $input["on_duty"] = 1;
                $input["status"] = -1;
            }else{
                $input["status"] = 1;
            }
            if(isset($input["status"]) && $input["status"] == -1){
                $customerM = new CustomerModel($this->corp_id);
                //检测有无保护客户
                $res = $customerM->getCustomersByUserIds($user_id);
                //$res =null;
                if (!empty($res)) {
                    return [
                        'status'=>false,
                        'message'=>'用户有未释放的客户，不能设置为离职!',
                    ];
                }
            }
            unset($input['struct_id']);
            unset($input['role']);
            unset($input['user_id']);
//            $employeeM = new EmployeeModel($this->corp_id);
            $struct_empM = new StructureEmployee($this->corp_id);
            $role_empM = new RoleEmployee($this->corp_id);
            $structM = new StructureModel($this->corp_id);
            $huanxin = new HuanxinApi();
            $info['status'] = false;
            //取出旧设置的部门ids
            $struct_old = $struct_empM->getStructIdsByEmployee($user_id);
            $struct_old_arr = [];
            foreach ($struct_old as $val) {
                $struct_old_arr[] .=$val['struct_id'];
            }

            //查找群组id
            $struct_info = $struct_empM->getGroupIdsByEmployee($user_id);
            $user_name = $this->corp_id."_".$user_id;
            $group_id = [];
            foreach ($struct_info as $one_struct) {
                $group_id[] = $one_struct['groupid'];
            }
            // var_dump(get_userinfo());die();
            //查询当前员工在职离职状态
            $userinfo = $employeeM->getEmployeeByUserid($user_id);
            $user_status = $userinfo['status'];
            // var_dump($user_status);die();
            $employeeM->link->startTrans();
            try{
                //员工表修改信息
                $em_res = $employeeM->setSingleEmployeeInfobyId($user_id,$input);

                //部门表修改信息，1,2,3 ---> 2,3,4 => 新增4,删除1
                //if ($input['is_leader'] == 1) {
                    $insert = array_diff($struct_ids,$struct_old_arr);//新添加的
                    $delete = array_diff($struct_old_arr,$struct_ids);//需要删除的

                    //有需要删除的
                    if (!empty($delete)) {
                        $delete_data = [];
                        foreach ($delete as $k=>$v) {
                            array_push($delete_data,$v);
                        }
                        $del_res = $struct_empM->deleteMultipleStructureEmployee($user_id,$delete_data);

                        if ($input['status'] == 1 && $user_status == 1) {
                            $delete_group = $structM->getStructureGroup($delete);
                            if (!empty($delete_group)) {
                                // if (count($delete_group)>1) {
                                    $result_info = $huanxin->deleteUserFromMoreGroup($user_name,$delete_group);
                                // }else{
                                //     $result_info = $huanxin->deleteOneEmployee($delete_group[0],$user_name);
                                // }
                                if (!$result_info['status']) {
                                    $del_hx = 0;
                                }else{
                                    $del_hx = 1;
                                }
                            }
                        }else{
                            $del_hx = 1;
                        }
                    } else {
                        $del_res = 1;
                        $del_hx = 1;
                    }

                    //有需要添加的
                    if (!empty($insert)) {
                        $insert_data = [];
                        foreach ($insert as $k=>$v) {
                            array_push($insert_data,['user_id'=>$user_id,'struct_id'=>$v]);
                        }
                        if (count($insert_data) >1) {
                            $res = $struct_empM->addMultipleStructureEmployee($insert_data);
                        } else {
                            $res = $struct_empM->addStructureEmployee($insert_data[0]);
                        }
                        if ($input['status'] == 1 && $user_status == 1) {
                            $insert_group = $structM->getStructureGroup($insert);
                            if (!empty($insert_group)) {
                                // if (count($insert_group)>1) {
                                    $result_info = $huanxin->addUserFromMoreGroup($user_name,$insert_group);
                                // }else{
                                //     $result_info = $huanxin->addOneEmployee($insert_group[0],$user_name);
                                // }
                                if (!$result_info['status']) {
                                    $ad_hx = 0;
                                }else{
                                    $ad_hx = 1;
                                }
                            }
                        }else{
                            $ad_hx = 1;
                        }
                    } else {
                        $res = 1;
                        $ad_hx = 1;
                    }

                /*} else {
                    //非领导
                    $struct_data['user_id'] = $user_id;
                    $struct_data['struct_id'] = $struct_ids[0];
                    $res = $struct_empM->setStructureEmployeeById($user_id,$struct_old[0]['struct_id'],$struct_data);
                    if ($res ===0) {
                        $res =1;
                    }
                    $del_res = 1;
                }*/


                $role_old = $role_empM->getRoleIdsByEmployee($user_id);
                $role_old_arr = [];
                foreach ($role_old as $val) {
                    $role_old_arr[] .=$val['role_id'];
                }
                $role_insert = array_diff($role_ids,$role_old_arr);//新添加的
                $role_delete = array_diff($role_old_arr,$role_ids);//需要删除的

                //有需要删除的
                if (!empty($role_delete)) {
                    $role_delete_data = [];
                    foreach ($role_delete as $k=>$v) {
                        array_push($role_delete_data,$v);
                    }
                    $role_del_res = $role_empM->deleteMultipleRoleEmployee($user_id,$role_delete_data);
                } else {
                    $role_del_res = 1;
                }

                //有需要添加的
                if (!empty($role_insert)) {
                    $role_insert_data = [];
                    foreach ($role_insert as $k=>$v) {
                        array_push($role_insert_data,['user_id'=>$user_id,'role_id'=>$v]);
                    }
                    if (count($role_insert_data) >1) {
                        $role_res = $role_empM->createMultipleRoleEmployee($role_insert_data);
                    } else {
                        $role_res = $role_empM->createRoleEmployee($role_insert_data[0]);
                    }
                } else {
                    $role_res = 1;
                }

                //设置离职时删除群组
                if ($input["status"] == -1 && !empty($group_id)) {
                    // if (count($group_id)>1) {
                        $del_group = $huanxin->deleteUserFromMoreGroup($user_name,$group_id);
                    // }else{
                    //     $del_group = $huanxin->deleteOneEmployee($group_id[0],$user_name);
                    // }
                    if (!$del_group['status']) {
                        $is_hx = 0;
                    }else{
                        $is_hx = 1;
                    }
                }else{
                    $is_hx = 1;
                }

                //离职设置在职时
                if ($input["status"] == 1 && $user_status == -1) {
                    $set_group = $structM->getStructureGroup($struct_ids);
                    // if (count($set_group)>1) {
                        $result_info = $huanxin->addUserFromMoreGroup($user_name,$set_group);
                    // }else{
                    //     $result_info = $huanxin->addOneEmployee($set_group[0],$user_name);
                    // }
                    if (!$result_info['status']) {
                        $set_hx = 0;
                    }else{
                        $set_hx = 1;
                    }
                }else{
                    $set_hx = 1;
                }
                

                if ($em_res >= 0 && $res>0 && $del_res>0 && $role_res>0 && $role_del_res>0 && $is_hx>0 && $ad_hx>0 && $del_hx>0 && $set_hx>0) {
                    if($pre_telephone!=$telephone){
                        $result_data=$this->changeEmployeePhone($user_id,$telephone);
                        if($result_data['status']){
                            $employeeM->link->commit();
                            return [
                                'status' => true,
                                'message' => '修改员工信息成功',
                            ];
                        }
                        else{
                            $employeeM->link->rollback();
                            return [
                                'status' => false,
                                'message' => $result_data['info'],
                            ];
                        }
                    }
                    else{
                        $employeeM->link->commit();
                        return [
                            'status' => true,
                            'message' => '修改员工信息成功',
                        ];
                    }

                } else {
                    $employeeM->link->rollback();
                    $info['message'] = '修改员工信息失败';
                    return $info;
                }
            }catch (\Exception $ex){
                $employeeM->link->rollback();
                $info['message'] = $ex->getMessage();
                //print_r($ex->getTrace());
                return $info;
            }
        }
    }

    /**
     * 删除单个员工、多个员工
     * @return array
     * created by messhair
     */
    public function deleteMultipleEmployee()
    {
        if(!($this->checkRule('systemsetting/employee/manage/exportEmployee') || $this->checkRule('systemsetting/employee/manage/del'))){
            $result=$this->noRole();
            $result['message']=$result['info'];
            return json($result);
        }

        $user_ids = input("ids/a");
//      查询员工状态是否为离职
        $employeeM = new EmployeeModel($this->corp_id);
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
            $tel_str = implode(',',$tel_arr);
            $names_str = implode(',',$names);
            $emp_delM = new EmployeeDelete($this->corp_id);
            $stru_empM = new StructureEmployee($this->corp_id);
            $role_empM = new RoleEmployee($this->corp_id);
            $huanxin = new HuanxinApi();
            $emp_delM->link->startTrans();
            Corporation::startTrans();
            $b = 0;
            $d = 0;
            $f = 0;
            $g = 0;
            $h = false;
            $r = false;
            try{
//                删除员工
                $b = $employeeM->deleteMultipleEmployee($user_ids);
//                转移到employee_delete表
                $d =$emp_delM->addMultipleBackupInfo($users);
//                    删除用户公司对照表信息
                $f = UserCorporation::deleteUserCorp($tel_str);
//                    删除部门员工表信息
                $g = $stru_empM->deleteMultipleStructureEmployee($user_ids);
//                    删除部门员工表信息
                $r = $role_empM->deleteMultipleRoleEmployee($user_ids);
                //删除环信账户
                $h = true;
                // if(count($tel_arr)==1){
                //     $result = $huanxin->deleteSingleHuanxinUser($tel_str);
                //     if(!isset($result['error'])){
                //         $h = true;
                //     }
                // }else{
                //     //环信不能删除指定的多个帐号,这个循环删除方法还未验证
                //     /*$result = $huanxin->deleteMultiHuanxinUser($tel_arr);
                //     if($result && count(array_column($result,"error"))<count($tel_arr)){
                //         $h = true;
                //     }*/
                //     $h = true;
                // }
            }catch(\Exception $e){
                $emp_delM->link->rollback();
                UserCorporation::rollback();
            }
            if ($b > 0 && $d > 0 && $f > 0 && $g > 0 && $h && $r) {
                $emp_delM->link->commit();
                UserCorporation::commit();
                $userinfo = get_userinfo();
                $uid = $userinfo["userid"];
                write_log($uid,6,'删除员工'.$names_str.'成功',$this->corp_id);
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

    /**
     * 修改密码
     * @return array
     * created by blu10ph
     */
    public function reset_password(){
        //TODO权限验证
        $result = ['status'=>0 ,'info'=>"重设密码时发生错误！"];
        return json($result);
    }

    /**
     * 修改密码
     * @return array
     * created by blu10ph
     */
    public function change_my_password(){
        $result = ['status'=>0 ,'info'=>"修改我的密码时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];

        $old_password = input('old_password');
        $new_password = input('new_password');
        $re_password = input('re_password');
        if(empty($old_password)||empty($new_password)||empty($re_password)){
            $result['info'] = "参数错误！";
            return json($result);
        }
        if($old_password!=$new_password){
            $result['info'] = "输入的新密码和不能和原密码相同！";
            return json($result);
        }
        if($new_password!=$re_password){
            $result['info'] = "两次输入的密码不一致！";
            return json($result);
        }
        if (md5($old_password) != $userinfo['userinfo']['password']) {
            $result['info'] = '原密码错误';
            $result['status'] = 6;
            return json($result);
        }

        $employeeM = new EmployeeModel($this->corp_id);
        $huanxin = new HuanxinApi();
        $employeeM->link->startTrans();

        try {
            $flg = $employeeM->reSetPass($userinfo["telephone"],$new_password);
            if(!$flg){
                $employeeM->link->rollback();
                $result['info'] = "更新数据库密码失败!";
                return json($result);
            }
            $flg = $huanxin->updatePassword($this->corp_id,$this->corp_id."_".$uids,$new_password);
            if (!flg) {
                  $employeeM->link->rollback();
                    $result['info'] = "更新环信密码失败!";
                    return json($result);
            }
            $employeeM->link->commit();
        } catch (\Exception $e) {
            $employeeM->link->rollback();
            $info['info'] = $e->getMessage();
            return json($result);
        }
        

        $result['status'] = 1;
        $result['info'] = "修改我的密码成功！";
        return json($result);
    }

    /**
     * 接口添加员工到群组
     * @return [type] [description]
     */
     public function groupAddEmployees(){
        $result = ['status'=>0,'info'=>"添加成员失败"];

        $groupid = input('groupid','','string');
        $usernames = input('usernames/a');
        
        if (!$groupid || empty($usernames)) {
            $result['info'] = "参数错误";
            return $result;
        }

        $huanxin = new HuanxinApi();
        $request = $huanxin->addAllUsers($groupid,$usernames);

        if (isset($request['error'])) {
            $result['info'] = "添加失败";
            $result['error'] = $request['error'];
        }else{
            $result['info'] = "添加成功";
            $result['status'] = 1;
        }

        return json($result);
    }

}