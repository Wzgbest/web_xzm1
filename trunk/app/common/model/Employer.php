<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\common\model;

use app\common\model\Base;

class Employer extends Base
{
    protected $dbprefix;
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'employer';
        parent::__construct($corp_id);
        $this->dbprefix = config('database.prefix');
    }

    /**
     * 根据用户名查询个人账号信息，带角色名
     * @param $telephone
     * @return array
     */
    public function getEmployerByTel($telephone)
    {
//        return $this->model->table($this->table)->where('telephone',$telephone)->cache('employer_info'.$telephone)->find();
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
     */
    public function getEmployerByUserid($userid)
    {
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'role b','a.role = b.id','left')
            ->join($this->dbprefix.'corporation_structure d','a.structid = d.id')
            ->field('a.*,b.role_name,d.struct_name')
            ->where('a.id',$userid)->find();
    }

    public function addSingleEmployer($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    public function addMutipleEmployers()
    {
        return $this->model->table($this->table)->insertAll($data);
    }

    /**
     * 记录用户登陆信息
     * @param $telephone 电话号码
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setEmployerSingleInfo($telephone,$data)
    {
        return $this->model->table($this->table)->where('telephone',$telephone)->update($data);
    }

    /**
     * 取出employer表中所有未开通环信的账号
     * @return array
     */
    public function getAllEmployers()
    {
        return $this->model->table($this->table)->where('haveim',0)->field('telephone as username,password,truename as nickname')->select();
    }

    /**
     * 更新表中haveim为1
     * @param $save_up
     * @return int
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
     */
    public function getFriendsList($owner)
    {
        $owner_id = $this->model->table($this->table)->where('telephone','<>', $owner)->field('telephone,userpic,truename as nickname,rule,structid')->select();
        return $owner_id;
    }

    /**
     * 登陆成功，创建用户system_token,并返回给app
     * @param $telephone 电话号码
     * @return array
     * @throws \think\Exception
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
     */
    public function getFriendsTel($telephone)
    {
        return $this->model->table($this->table)->where('telephone','<>', $telephone)->column('telephone');
    }

    /**
     * 获取所有用户列表供app端使用
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllUsers()
    {
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'role b','a.role = b.id')
            ->join($this->dbprefix.'structure_employer c','a.id = c.user_id')
            ->join($this->dbprefix.'structure d','c.struct_id = d.id')
            ->field('a.telephone,a.userpic,a.truename as nickname,b.role_name as occupation,d.struct_name as structid')
            ->select();
    }

    /**
     * 获取所有用户电话
     * @return array
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
     */
    public function setSingleEmployerInfobyId($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 根据部门id查询该部门所有员工
     * @param $struct_id 部门id
     * @param int $page_first 当前页
     * @param null $rows 查找的行数
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getEmployerByStructId($struct_id,$page_first=0,$rows=null)
    {
        if (is_null($rows)) {
            return $this->model->table($this->table)->alias('a')
                ->join($this->dbprefix.'role b','a.role = b.id')
                ->join($this->dbprefix.'structure_employer c','a.id = c.user_id')
                ->join($this->dbprefix.'structure d','c.struct_id = d.id')
                ->field('a.id as user_id,a.truename,a.worknum,a.telephone,a.email,a.is_leader,a.role,b.role_name,c.struct_id,d.struct_name')
                ->where('c.struct_id',$struct_id)->select();
        } else {
            return $this->model->table($this->table)->alias('a')
                ->join($this->dbprefix.'role b','a.role = b.id')
                ->join($this->dbprefix.'structure_employer c','a.id = c.user_id')
                ->join($this->dbprefix.'structure d','c.struct_id = d.id')
                ->field('a.id as user_id,a.truename,a.worknum,a.telephone,a.email,a.is_leader,a.role,b.role_name,c.struct_id,d.struct_name')
                ->where('c.struct_id',$struct_id)->limit($page_first,$rows)->select();
        }
    }

    /**
     * 根据部门id查询该部门所有员工数量
     * @param $struct_id 部门id
     * @return int|string
     */
    public function countEmployerByStructId($struct_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'role b','a.role = b.id')
            ->join($this->dbprefix.'structure_employer c','a.id = c.user_id')
            ->join($this->dbprefix.'structure d','c.struct_id = d.id')
            ->field('a.id as user_id,a.truename,a.worknum,a.telephone,a.email,a.is_leader,a.role,b.role_name,c.struct_id,d.struct_name')
            ->where('c.struct_id',$struct_id)->count('a.id');
    }

    /**
     * 获取所有员工列表
     * @param int $page_now_num 当前页
     * @param null $rows 行数
     * @param null $where 查询条件
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getPageEmployerList($page_now_num = 0,$rows = null,$where = null)
    {
        if ($where) {
            $map = 'where ';
            if (isset($where['struct_id'])) {
                $map .= 'c.struct_id ='.$where['struct_id'].' ';
                if (isset($where['role'])) {
                    $map .= 'and a.role ='.$where['role'].' ';
                    if (isset($where['on_duty'])) {
                        $map .= 'and a.on_duty='.$where['on_duty'].' ';
                    }
                }
            } else {
                if (isset($where['role'])) {
                    $map .= ' a.role ='.$where['role'].' ';
                    if (isset($where['on_duty'])) {
                        $map .= ' and a.on_duty='.$where['on_duty'].' ';
                    }
                } else {
                    if (isset($where['on_duty'])) {
                        $map .= ' a.on_duty='.$where['on_duty'].' ';
                    }
                }
            }
        } else {
            $map = '';
        }
        if (is_null($rows)) {
            $sql = 'SELECT `a`.`id`,`a`.`truename`,`a`.`role`,`a`.`telephone`,`a`.`is_leader`,`a`.`on_duty`,`a`.`worknum`,`a`.`email`,`a`.`qqnum`,`b`.`role_name`,GROUP_CONCAT(`d`.`struct_name`) as `struct_name` FROM `'.$this->dbprefix.'employer` `a` LEFT JOIN `'.$this->dbprefix.'role` `b` ON `a`.`role`=`b`.`id` INNER JOIN `'.$this->dbprefix.'structure_employer` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` '.$map.'GROUP BY `a`.`id`;';
            return $this->model->table($this->table)->query($sql);
        } else {
            $sql = 'SELECT `a`.`id`,`a`.`truename`,`a`.`role`,`a`.`telephone`,`a`.`is_leader`,`a`.`on_duty`,`a`.`worknum`,`a`.`email`,`a`.`qqnum`,`b`.`role_name`,GROUP_CONCAT(`d`.`struct_name`) as `struct_name` FROM `'.$this->dbprefix.'employer` `a` LEFT JOIN `'.$this->dbprefix.'role` `b` ON `a`.`role`=`b`.`id` INNER JOIN `'.$this->dbprefix.'structure_employer` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` '.$map.'GROUP BY `a`.`id` limit '.$page_now_num.','.$rows.';';
            return $this->model->table($this->table)->query($sql);
        }
    }

    /**
     * 所有员工总数
     * @param null $where 查询条件
     * @return mixed
     */
    public function countPageEmployerList($where = null)
    {
        if ($where) {
            $map = 'where ';
            if (isset($where['struct_id'])) {
                $map .= 'c.struct_id ='.$where['struct_id'].' ';
                if (isset($where['role'])) {
                    $map .= 'and a.role ='.$where['role'].' ';
                    if (isset($where['on_duty'])) {
                        $map .= 'and a.on_duty='.$where['on_duty'].' ';
                    }
                }
            } else {
                if (isset($where['role'])) {
                    $map .= ' a.role ='.$where['role'].' ';
                    if (isset($where['on_duty'])) {
                        $map .= ' and a.on_duty='.$where['on_duty'].' ';
                    }
                } else {
                    if (isset($where['on_duty'])) {
                        $map .= ' a.on_duty='.$where['on_duty'].' ';
                    }
                }
            }
        } else {
            $map = '';
        }
        $sql = 'SELECT count(distinct `a`.`id`) as num FROM `'.$this->dbprefix.'employer` `a` LEFT JOIN `'.$this->dbprefix.'role` `b` ON `a`.`role`=`b`.`id` INNER JOIN `'.$this->dbprefix.'structure_employer` `c` ON `a`.`id`=`c`.`user_id` INNER JOIN `'.$this->dbprefix.'structure` `d` ON `c`.`struct_id`=`d`.`id` '.$map.';';
        return $this->model->table($this->table)->query($sql);
    }
}