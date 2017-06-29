<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\common\model;

use app\common\model\Base;

class Employee extends Base
{
    protected $dbprefix;
    public function __construct($corp_id=null)
    {
        $this->table = config('database.prefix').'employee';
        parent::__construct($corp_id);
        $this->dbprefix = config('database.prefix');
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
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'role b','a.role = b.id','left')
            ->field('a.*,b.role_name')
            ->where('a.telephone',$telephone)->find();
    }

    /**
     * 按用户id查询 带角色名，部门名
     * @param $userid 用户id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getEmployeeByUserid($userid)
    {
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'role b','a.role = b.id','left')
            ->join($this->dbprefix.'structure_employee c','a.id = c.user_id','left')
            ->join($this->dbprefix.'structure d','c.struct_id = d.id','left')
            ->field('a.*,b.role_name,GROUP_CONCAT(d.struct_name) as struct_name,GROUP_CONCAT(c.struct_id) as struct_id')
            ->where('a.id',$userid)
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
     * 查询非当前角色
     * @param $role_id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByNotRole($role_id, $struct_id, $user_tel_email)
    {
        $map = '';
        if (!empty($struct_id)) {
            if (!empty($user_tel_email)) {
                if (preg_match('/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}/',$user_tel_email)) {
                    $map .= 'WHERE `a`.`role`='.$role_id.' AND `d`.`id`='.$struct_id.' AND `a`.`telephone`='.$user_tel_email;
                } elseif (preg_match('/^[\w\+-]+(\.[\w\+-]+)*@[a-z\d-]+(\.[a-z\d-]+)*\.([a-z]{2,4})$/',$user_tel_email)) {
                    $map .= 'WHERE `a`.`role`='.$role_id.' AND `d`.`id`='.$struct_id.' AND `a`.`email`='.$user_tel_email;
                } else {
                    $map .= 'WHERE `a`.`role`='.$role_id.' AND `d`.`id`='.$struct_id.' AND `a`.`truename`="'.$user_tel_email.'"';
                }
            } else {
                $map .= 'WHERE `a`.`role`='.$role_id.' AND `d`.`id`='.$struct_id;
            }
        } else {
            if (!empty($user_tel_email)) {
                if (preg_match('/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}/',$user_tel_email)) {
                    $map .= 'WHERE `a`.`role`='.$role_id.' AND `d`.`id`='.$struct_id.' AND `a`.`telephone`='.$user_tel_email;
                } elseif (preg_match('/^[\w\+-]+(\.[\w\+-]+)*@[a-z\d-]+(\.[a-z\d-]+)*\.([a-z]{2,4})$/',$user_tel_email)) {
                    $map .= 'WHERE `a`.`role`='.$role_id.' AND `d`.`id`='.$struct_id.' AND `a`.`email`='.$user_tel_email;
                } else {
                    $map .= 'WHERE `a`.`role`='.$role_id.' AND `d`.`id`='.$struct_id.' AND `a`.`truename`="'.$user_tel_email.'"';
                }
            } else {
                $map .= 'WHERE `a`.`role`='.$role_id;
            }
        }
        $sql = 'SELECT `a`.`id`,`a`.`truename`,`a`.`role`,`a`.`telephone`,`a`.`is_leader`,`a`.`worknum`,GROUP_CONCAT(`d`.`struct_name`) as `struct_name` FROM `'.$this->dbprefix.'employee` `a` INNER JOIN `'.$this->dbprefix.'structure_employee` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` '.$map.' GROUP BY `a`.`id` order by `a`.`worknum`;';
        return $this->model->table($this->table)->query($sql);
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
     * 记录用户登陆信息
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
        return $this->model->table($this->table)->where('haveim',0)->field('telephone as username,password,truename as nickname')->select();
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
        return $this->model->table($this->table)->where("telephone in ('".implode("','",$save_up)."')")->update($data);
    }

    /**
     * 取出所有可以添加环信好友账号信息，即非本人的其他人信息
     * @param $owner 电话号码
     * @return array
     * created by messhair
     */
    public function getFriendsList($owner)
    {
        $owner_id = $this->model->table($this->table)->where('telephone','<>', $owner)->field('telephone,userpic,truename as nickname,role')->select();
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
        $b=$this->model->table($this->table)->where('telephone',$telephone)->update(['system_token'=>$sys_token]);
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
        return $this->model->table($this->table)->where('telephone','<>', $telephone)->column('telephone');
    }

    /**
     * 获取所有用户列表供app端使用
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllUsers()
    {
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'role b','a.role = b.id')
            ->join($this->dbprefix.'structure_employee c','a.id = c.user_id')
            ->join($this->dbprefix.'structure d','c.struct_id = d.id')
            ->field('a.telephone,a.userpic,a.truename as nickname,b.role_name as occupation,c.struct_id,d.struct_name as struct_name')
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
        return $this->model->table($this->table)->where('telephone',$telephone)->update('password',$password);
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
     * @param $role_id 员工id
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByRole($role_id, $page_first=0, $page_rows = 10)
    {
        $sql = 'SELECT `a`.`id`,`a`.`truename`,`a`.`role`,`a`.`telephone`,`a`.`is_leader`,`a`.`worknum`,GROUP_CONCAT(`d`.`struct_name`) as `struct_name` FROM `'.$this->dbprefix.'employee` `a` INNER JOIN `'.$this->dbprefix.'structure_employee` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` WHERE `a`.`role`='.$role_id.' GROUP BY `a`.`id` order by `a`.`worknum` LIMIT '.$page_first.','.$page_rows.';';
        return $this->model->table($this->table)->query($sql);
    }

    /**
     * 根据部门id查询该部门所有员工
     * @param $struct_id 部门id
     * @param int $page_first 当前页
     * @param null $rows 查找的行数
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getEmployeeByStructId($struct_id,$page_first=0,$rows=null)
    {
        if (is_null($rows)) {
            return $this->model->table($this->table)->alias('a')
                ->join($this->dbprefix.'role b','a.role = b.id')
                ->join($this->dbprefix.'structure_employee c','a.id = c.user_id')
                ->join($this->dbprefix.'structure d','c.struct_id = d.id')
                ->field('a.id as user_id,a.truename,a.worknum,a.telephone,a.email,a.is_leader,a.role,b.role_name,c.struct_id,d.struct_name')
                ->where('c.struct_id',$struct_id)->select();
        } else {
            return $this->model->table($this->table)->alias('a')
                ->join($this->dbprefix.'role b','a.role = b.id')
                ->join($this->dbprefix.'structure_employee c','a.id = c.user_id')
                ->join($this->dbprefix.'structure d','c.struct_id = d.id')
                ->field('a.id as user_id,a.truename,a.worknum,a.telephone,a.email,a.is_leader,a.role,b.role_name,c.struct_id,d.struct_name')
                ->where('c.struct_id',$struct_id)->limit($page_first,$rows)->select();
        }
    }

    /**
     * 根据部门id查询该部门所有员工数量
     * @param $struct_id 部门id
     * @return int|string
     * created by messhair
     */
    public function countEmployeeByStructId($struct_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'role b','a.role = b.id')
            ->join($this->dbprefix.'structure_employee c','a.id = c.user_id')
            ->join($this->dbprefix.'structure d','c.struct_id = d.id')
            ->field('a.id as user_id,a.truename,a.worknum,a.telephone,a.email,a.is_leader,a.role,b.role_name,c.struct_id,d.struct_name')
            ->where('c.struct_id',$struct_id)->count('a.id');
    }

    /**
     * 获取所有员工列表
     * @param int $page_now_num 当前页
     * @param null $rows 行数
     * @param null|array $where[
     *      'struct_id'=>,
     *      'role'=>,
     *      'on_duty'=>,
     * ] 查询条件
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getPageEmployeeList($page_now_num = 0,$rows = null,$where = null)
    {
        $map = $this->_getPageEmployeeListWhereSql($where);
        if (is_null($rows)) {
            $sql = 'SELECT `a`.`id`,`a`.`truename`,`a`.`role`,`a`.`telephone`,`a`.`is_leader`,`a`.`on_duty`,`a`.`worknum`,`a`.`email`,`a`.`qqnum`,`a`.`create_time`,`b`.`role_name`,GROUP_CONCAT(`d`.`struct_name`) as `struct_name` FROM `'.$this->dbprefix.'employee` `a` LEFT JOIN `'.$this->dbprefix.'role` `b` ON `a`.`role`=`b`.`id` INNER JOIN `'.$this->dbprefix.'structure_employee` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` '.$map.'GROUP BY `a`.`id` order by `a`.`worknum` desc;';

        } else {
            $sql = 'SELECT `a`.`id`,`a`.`truename`,`a`.`role`,`a`.`telephone`,`a`.`is_leader`,`a`.`on_duty`,`a`.`worknum`,`a`.`email`,`a`.`qqnum`,`a`.`create_time`,`b`.`role_name`,GROUP_CONCAT(`d`.`struct_name`) as `struct_name` FROM `'.$this->dbprefix.'employee` `a` LEFT JOIN `'.$this->dbprefix.'role` `b` ON `a`.`role`=`b`.`id` INNER JOIN `'.$this->dbprefix.'structure_employee` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` '.$map.'GROUP BY `a`.`id` order by `a`.`worknum` desc limit '.$page_now_num.','.$rows.';';
        }
        //var_exp($sql,'$sql',1);
        return $this->model->table($this->table)->query($sql);
    }
     protected function _getPageEmployeeListWhereSql($where){
         $map = "";
         if ($where) {
             $map = 'where 1=1 ';
             if (isset($where['struct_id']) && $where['struct_id']) {
                 $map .= 'c.struct_id ='.$where['struct_id'].' ';
                 if (isset($where['role']) && $where['role']) {
                     $map .= 'and a.role ='.$where['role'].' ';
                     if (isset($where['on_duty']) && $where['on_duty']) {
                         if ($where['on_duty']==-1) {
                             $map .= 'and a.status=-1 ';
                         } else {
                             $map .= 'and a.on_duty='.$where['on_duty'].' ';
                         }
                     } else {
                         $map .= 'and a.status = 1 ';
                     }
                 }
             } else {
                 if (isset($where['role']) && $where['role']) {
                     $map .= ' a.role ='.$where['role'].' ';
                     if (isset($where['on_duty']) && $where['on_duty']) {
                         if ($where['on_duty']==-1) {
                             $map .= 'and a.status=-1 ';
                         } else {
                             $map .= 'and a.on_duty='.$where['on_duty'].' ';
                         }
                     } else {
                         $map .= 'and a.status = 1';
                     }
                 } else {
                     if (isset($where['on_duty']) && $where['on_duty']) {
                         if ($where['on_duty']==-1) {
                             $map .= 'a.status=-1 ';
                         } else {
                             $map .= 'a.on_duty='.$where['on_duty'].' ';
                         }
                     } else {
                         $map .= 'and a.status = 1 ';
                     }
                 }
             }
         } else {
             $map = '';
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
        $sql = 'SELECT count(distinct `a`.`id`) as num FROM `'.$this->dbprefix.'employee` `a` LEFT JOIN `'.$this->dbprefix.'role` `b` ON `a`.`role`=`b`.`id` INNER JOIN `'.$this->dbprefix.'structure_employee` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` '.$map.';';
        return $this->model->table($this->table)->query($sql);
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
        return $this->model
            ->table($this->table)
            ->where($where)
            ->field('id,truename,telephone,wired_phone,part_phone,gender,worknum,is_leader,role,qqnum,wechat')
            ->select();
    }
}