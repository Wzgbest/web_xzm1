<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\common\model;

use app\common\model\Base;

class Employee extends Base{
    protected $dbprefix;
    public function __construct($corp_id=null){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'employee';
        parent::__construct($corp_id);
    }

    /**
     * 根据用户名查询个人账号信息，带角色名
     * @param $telephone
     * @return array
     * created by messhair
     */
    public function getEmployeeByTel($telephone)
    {
//        return $this->model->table($this->table)->where('telephone',$telephone)->cache('employee_info'.$telephone)->find();
//        return $this->model->table($this->table)->where('telephone',$telephone)->find();
        return $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','e.id = re.user_id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->field('e.*,GROUP_CONCAT( distinct r.role_name) as role_name,GROUP_CONCAT( distinct re.role_id) as role_id')
            ->where('e.telephone',$telephone)
            ->group("e.id")
            ->find();
    }

    /**
     * 按用户id查询 带角色名，部门名
     * @param $userid 用户id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getEmployeeByUserid($userid)
    {
        return $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','e.id = re.user_id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->field('e.*,case when `e`.`status` = -1 then `e`.`status` else `e`.`on_duty` end as on_duty,GROUP_CONCAT( distinct r.role_name) as role_name,GROUP_CONCAT( distinct re.role_id) as role_id,GROUP_CONCAT( distinct s.struct_name) as struct_name,GROUP_CONCAT( distinct se.struct_id) as struct_id')
            ->where('e.id',$userid)
            ->group("e.id")
            ->find();
    }

    /**
     * 按员工ids查询员工信息
     * @param $user_ids
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByUserids($user_ids)
    {
        return $this->model->table($this->table)
            ->where('id','in',$user_ids)
            ->select();
    }

    /**
     * 按员工ids查询员工姓名
     * @param $user_ids
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeNameByUserids($user_ids)
    {
        return $this->model->table($this->table)
            ->where('id','in',$user_ids)
            ->column("truename","id");
    }

    /**
     * 查询非当前角色
     * @param $role_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByNotRole($role_id, $struct_id, $user_tel_email)
    {
        $map = $this->_getPageEmployeeListWhereSql([
            "role"=>$role_id,
            "structure"=>$struct_id,
            "tel_email"=>$user_tel_email
        ]);
        $field = '`e`.`id`,`e`.`truename`,GROUP_CONCAT( distinct `re`.`role_id`) as role,`e`.`telephone`,`e`.`is_leader`,`e`.`worknum`,`e`.`create_time`,GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name` ';
        $employee_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->order("e.worknum desc")
            ->group("e.id")
            ->field($field)
            ->select();
        //var_exp($employee_list,'$employee_list',1);
        return $employee_list;
    }

    /**
     * 添加单用户
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addSingleEmployee($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 添加多用户
     * @param array $data
     * @return int|string
     * created by messhair
     */
    public function addMutipleEmployees($data)
    {
        return $this->model->table($this->table)->insertAll($data);
    }

    /**
     * 设置用户信息
     * @param $telephone 电话号码
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setEmployeeSingleInfo($telephone,$data)
    {
        return $this->model->table($this->table)->where('telephone',$telephone)->update($data);
    }

    /**
     * 取出employee表中所有未开通环信的账号
     * @return array
     * created by messhair
     */
    public function getAllEmployees()
    {
        return $this->model->table($this->table)
            ->where('haveim',0)
            ->field('telephone as username,password,truename as nickname')
            ->select();
    }

    /**
     * 更新表中haveim为1
     * @param $save_up
     * @return int
     * created by messhair
     */
    public function saveIm($save_up)
    {
        $data=['haveim'=>1];
        return $this->model->table($this->table)
            ->where("telephone in ('".implode("','",$save_up)."')")
            ->update($data);
    }

    /**
     * 取出所有可以添加环信好友账号信息，即非本人的其他人信息
     * @param $owner 电话号码
     * @return array
     * created by messhair
     */
    public function getFriendsList($owner)
    {
        $owner_id = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->where('telephone','<>', $owner)
            ->group("e.id")
            ->field('e.telephone,e.userpic,e.truename as nickname,GROUP_CONCAT( distinct re.role_id) as role')
            ->select();
        return $owner_id;
    }

    /**
     * 登陆成功，创建用户system_token,并返回给app
     * @param $telephone 电话号码
     * @return array
     * @throws \think\Exception
     * created by messhair
     */
    public function createSystemToken($telephone)
    {
        $sys_token=md5($telephone.time().rand(10000,99999));
        $b=$this->model->table($this->table)
            ->where('telephone',$telephone)
            ->update(['system_token'=>$sys_token]);
        return ['system_token'=>$sys_token,'res'=>$b];
    }

    /**
     * 通过手机号查询所有其他手机号
     * @param $telephone 电话号码
     * @return array
     * created by messhair
     */
    public function getFriendsTel($telephone)
    {
        return $this->model->table($this->table)
            ->where('telephone','<>', $telephone)
            ->column('telephone');
    }

    /**
     * 获取所有用户列表供app端使用
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllUsers()
    {
        return $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->group("e.id")
            ->field('e.telephone,e.userpic,e.truename as nickname,GROUP_CONCAT( distinct r.role_name) as occupation,GROUP_CONCAT( distinct se.struct_id) as struct_id,GROUP_CONCAT( distinct s.struct_name) as struct_name')
            ->select();
    }

    /**
     * 获取所有用户电话
     * @return array
     * created by messhair
     */
    public function getAllTels()
    {
        return $this->model->table($this->table)->column('telephone');
    }

    /**
     * 修改用户密码
     * @param $telephone 电话号码
     * @param $password 密码md5加密后
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function reSetPass($telephone,$password)
    {
        return $this->model->table($this->table)
            ->where('telephone',$telephone)
            ->update('password',$password);
    }

    /**
     * 按用户id更新数据
     * @param $id 用户id非tel
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setSingleEmployeeInfobyId($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 更新多个员工信息
     * @param $ids
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setMultipleEmployeeInfoByIds($ids,$data)
    {
        return $this->model->table($this->table)->where('id','in',$ids)->update($data);
    }

    /**
     * 删除多个员工
     * @param $ids
     * @return int
     * @throws \think\Exception
     * created by messhair
     */
    public function deleteMultipleEmployee($ids)
    {
        return $this->model->table($this->table)->where('id','in',$ids)->delete();
    }

    /**
     * 根据角色id查询员工信息
     * @param $role_id int 员工id
     * @param $page int 开始数量
     * @param $rows int 获取数量
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByRole($role_id, $page=0, $rows = 10)
    {
        $field = '`e`.`id`,`e`.`truename`,GROUP_CONCAT( distinct `re`.`role_id`) as role,GROUP_CONCAT( distinct `res`.`role_id`) as role_id,`e`.`telephone`,`e`.`is_leader`,`e`.`worknum`,`e`.`create_time`,GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name` ';
        $employee_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role_employee res','res.user_id = e.id')
            ->join($this->dbprefix.'role r','res.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where("re.role_id",$role_id)
            ->order("e.worknum desc")
            ->group("e.id")
            ->limit($page,$rows)
            ->field($field)
            ->select();
        return $employee_list;
    }

    /**
     * 根据角色id查询员工数量
     * @param $role_id int 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeCountByRole($role_id)
    {
        $count = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where('re.role_id',$role_id)
            ->group("e.id")
            ->count();
        return $count;
    }

    public function getInRoleEmployeeIds($role_id){
        $in_role_employee_id_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'role_employee res','res.user_id = e.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where("re.role_id",$role_id)
            ->order("e.worknum desc")
            ->group("e.id")
            ->column('`e`.`id`');
        return $in_role_employee_id_list;
    }

    /**
     * 根据角色id查询没有该角色员工信息
     * @param $role_id int 员工id
     * @param $page int 开始数量
     * @param $rows int 获取数量
     * @param $filter array 过滤条件
     * @param $order string 排序字段
     * @param $direction string 排序方向
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getNotRoleEmployeeByRole($role_id, $page=0, $rows = 10,$filter=null,$order="worknum",$direction="desc")
    {
        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = "e.".$order." ".$direction;

        //显示字段
        $field = '`e`.`id`,`e`.`truename`,GROUP_CONCAT( distinct `res`.`role_id`) as role,`e`.`telephone`,`e`.`is_leader`,`e`.`worknum`,`e`.`create_time`,GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name` ';

        //筛选
        $map = $this->_getPageEmployeeListWhereSql($filter);
        $in_role_employee_id_list = $this->getInRoleEmployeeIds($role_id);
        if($in_role_employee_id_list){
            $map["e.id"] = ["not in",$in_role_employee_id_list];
        }

        $employee_list = $employee_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role_employee res','res.user_id = e.id')
            ->join($this->dbprefix.'role r','res.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->order($order)
            ->group("e.id")
            ->limit($page,$rows)
            ->field($field)
            ->select();
        //var_exp($employee_list,'$employee_list',1);
        return $employee_list;
    }

    /**
     * 根据角色id查询没有该角色员工数量
     * @param $role_id int 员工id
     * @param $filter array 过滤条件
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getNotRoleEmployeeCountByRole($role_id,$filter=null)
    {
        $map = $this->_getPageEmployeeListWhereSql($filter);
        $in_role_employee_id_list = $this->getInRoleEmployeeIds($role_id);
        if($in_role_employee_id_list){
            $map["e.id"] = ["not in",$in_role_employee_id_list];
        }

        $count = $employee_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role_employee res','res.user_id = e.id')
            ->join($this->dbprefix.'role r','res.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->group("e.id")
            ->count();
        return $count;
    }

    /**
     * 根据部门id查询该部门所有员工
     * @param $struct_id 部门id
     * @param int $page 当前页
     * @param null $rows 查找的行数
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByStructId($struct_id,$page=0,$rows=null)
    {
        $query = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure_employee ses','e.id = ses.user_id')
            ->join($this->dbprefix.'structure s','ses.struct_id = s.id')
            ->field('e.id as user_id,e.truename,e.worknum,e.telephone,e.email,e.create_time,e.is_leader,GROUP_CONCAT( distinct re.role_id) as role,GROUP_CONCAT( distinct r.role_name) as role_name,GROUP_CONCAT( distinct se.struct_id) as struct,GROUP_CONCAT( distinct ses.struct_id) as struct_id,GROUP_CONCAT( distinct s.struct_name) as struct_name')
            ->where('se.struct_id',$struct_id)
            ->group("e.id");
        if (!is_null($rows)) {
            $query = $query->limit($page,$rows);
        }
        $employee_list = $query->select();
        return $employee_list;
    }

    /**
     * 根据部门id查询该部门所有员工数量
     * @param $struct_id 部门id
     * @return int|string
     * created by messhair
     */
    public function countEmployeeByStructId($struct_id,$filter=null)
    {
        return $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where('se.struct_id',$struct_id)
            ->group("e.id")
            ->count();
    }

    public function getInStructEmployeeIds($struct_id){
        $in_struct_employee_id_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure_employee ses','e.id = ses.user_id')
            ->join($this->dbprefix.'structure s','ses.struct_id = s.id')
            ->field('e.id as user_id,e.truename,e.worknum,e.telephone,e.email,e.create_time,e.is_leader,GROUP_CONCAT( distinct re.role_id) as role,GROUP_CONCAT( distinct r.role_name) as role_name,GROUP_CONCAT( distinct se.struct_id) as struct,GROUP_CONCAT( distinct ses.struct_id) as struct_id,GROUP_CONCAT( distinct s.struct_name) as struct_name')
            ->where('se.struct_id',$struct_id)
            ->group("e.id")
            ->column('`e`.`id`');
        return $in_struct_employee_id_list;
    }

    public function getInStructEmployeenum($struct_id){
        $in_struct_employee_num = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure_employee ses','e.id = ses.user_id')
            ->join($this->dbprefix.'structure s','ses.struct_id = s.id')
            ->field('e.id as user_id,e.truename,e.worknum,e.telephone,e.email,e.create_time,e.is_leader,GROUP_CONCAT( distinct re.role_id) as role,GROUP_CONCAT( distinct r.role_name) as role_name,GROUP_CONCAT( distinct se.struct_id) as struct,GROUP_CONCAT( distinct ses.struct_id) as struct_id,GROUP_CONCAT( distinct s.struct_name) as struct_name')
            ->where('se.struct_id',$struct_id)
            ->group("e.id")
            ->count('`e`.`id`');
        return $in_struct_employee_num;
    }

    /**
     * 根据部门id查询没有该部门所有员工
     * @param $struct_id 部门id
     * @param int $page 当前页
     * @param null $rows 查找的行数
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByNotStructId($struct_id,$page=0,$rows=null,$filter=null,$order="worknum",$direction="desc")
    {
        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = "e.".$order." ".$direction;

        //显示字段
        $field = '`e`.`id`,`e`.`truename`,GROUP_CONCAT( distinct `res`.`role_id`) as role,`e`.`telephone`,`e`.`is_leader`,`e`.`worknum`,`e`.`create_time`,GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name` ';

        //筛选
        $map = $this->_getPageEmployeeListWhereSql($filter);
        $in_struct_employee_id_list = $this->getInStructEmployeeIds($struct_id);
        if($in_struct_employee_id_list){
            $map["e.id"] = ["not in",$in_struct_employee_id_list];
        }

        $employee_list = $employee_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role_employee res','res.user_id = e.id')
            ->join($this->dbprefix.'role r','res.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->order($order)
            ->group("e.id")
            ->limit($page,$rows)
            ->field($field)
            ->select();
        //var_exp($employee_list,'$employee_list',1);
        return $employee_list;
    }

    /**
     * 根据部门id查询没有该部门所有员工数量
     * @param $struct_id 部门id
     * @return int|string
     * created by messhair
     */
    public function countEmployeeByNotStructId($struct_id,$filter=null)
    {
        $map = $this->_getPageEmployeeListWhereSql($filter);
        $in_struct_employee_id_list = $this->getInStructEmployeeIds($struct_id);
        if($in_struct_employee_id_list){
            $map["e.id"] = ["not in",$in_struct_employee_id_list];
        }
        $count = $employee_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role_employee res','res.user_id = e.id')
            ->join($this->dbprefix.'role r','res.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','e.id = se.user_id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->group("e.id")
            ->count();
        return $count;
    }

    /**
     * 获取所有员工列表
     * @param int $page 当前页
     * @param null $rows 行数
     * @param null|array $where[
     *      'struct_id'=>,
     *      'role'=>,
     *      'on_duty'=>,
     * ] 查询条件
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getPageEmployeeList($page = 0,$rows = null,$where = null)
    {
        $map = $this->_getPageEmployeeListWhereSql($where);
        $field = '`e`.`id`,`e`.`truename`,GROUP_CONCAT( distinct `re`.`role_id`) as role,`e`.`telephone`,`e`.`is_leader`,case when `e`.`status` = -1 then `e`.`status` else `e`.`on_duty` end as on_duty,`e`.`worknum`,`e`.`email`,`e`.`qqnum`,`e`.`create_time`,GROUP_CONCAT( distinct `r`.`role_name`) as role_name,GROUP_CONCAT( distinct `s`.`struct_name`) as `struct_name` ';
        $employee_list = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->limit($page,$rows)
            ->field($field)
            ->group("e.id")
            ->order("e.worknum desc")
            ->select();
        //var_exp($employee_list,'$employee_list',1);
        return $employee_list;
    }
     protected function _getPageEmployeeListWhereSql($where){
         $map = [];
         if (isset($where['structure']) && $where['structure']) {
             $map["se.struct_id"] = $where['structure'];
         }
         if (isset($where['role']) && $where['role']) {
             $map["re.role_id"] = $where['role'];
         }
         if (isset($where['tel_email']) && $where['tel_email']) {
             if (preg_match('/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}/',$where['tel_email'])) {
                 $map["e.telephone"] = $where['tel_email'];
             } elseif (preg_match('/^[\w\+-]+(\.[\w\+-]+)*@[a-z\d-]+(\.[a-z\d-]+)*\.([a-z]{2,4})$/',$where['tel_email'])) {
                 $map["e.email"] = $where['tel_email'];
             }else{
                 $map["e.truename"] = ["like","%".$where['tel_email']."%"];
             }
         }
         if (isset($where['on_duty']) && $where['on_duty']) {
             if ($where['on_duty']==-1) {
                 $map["e.status"] = -1;
             } else {
                 $map["e.on_duty"] = $where['on_duty'];
             }
         }
         return $map;
     }
    /**
     * 所有员工总数
     * @param null|array $where[
     *      'struct_id'=>,
     *      'role'=>,
     *      'on_duty'=>,
     * ] 查询条件
     * @return mixed
     * created by messhair
     */
    public function countPageEmployeeList($where = null)
    {
        $map = $this->_getPageEmployeeListWhereSql($where);
        $count = $this->model->table($this->table)->alias('e')
            ->join($this->dbprefix.'role_employee re','re.user_id = e.id')
            ->join($this->dbprefix.'role r','re.role_id = r.id')
            ->join($this->dbprefix.'structure_employee se','se.user_id = e.id')
            ->join($this->dbprefix.'structure s','se.struct_id = s.id')
            ->where($map)
            ->group("e.id")
            ->count();
        return $count;
    }

    /**
     * 导出所有员工
     * @param null|array $where[
     *      'struct_id'=>,
     *      'role'=>,
     *      'on_duty'=>,
     * ] 查询条件
     * @return mixed
     * created by blu10ph
     */
    public function exportAllEmployees($where = null){
        return $this->model->table($this->table)
            ->where($where)
            ->field('id,truename,telephone,wired_phone,part_phone,gender,worknum,is_leader,"" as struct,"" as role,qqnum,wechat')
            ->select();
    }
}