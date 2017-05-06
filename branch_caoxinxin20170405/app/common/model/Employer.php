<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\common\model;

use app\common\model\Base;

class Employer extends Base
{
    public function __construct($corp_id)
    {
        $this->table=config('database.prefix').'employer';
        parent::__construct($corp_id);
    }

    /**
     * 根据用户名查询个人账号信息
     * @param $telephone
     * @return array
     */
    public function getEmployer($telephone)
    {
//        return $this->model->table($this->table)->where('telephone',$telephone)->cache('employer_info'.$telephone)->find();
        return $this->model->table($this->table)->where('telephone',$telephone)->find();
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
     * 获取所有用户列表
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllUsers()
    {
        return $this->model->table($this->table)->field('telephone,userpic,truename as nickname,structid')->select();
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
}