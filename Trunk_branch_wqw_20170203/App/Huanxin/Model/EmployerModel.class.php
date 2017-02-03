<?php
/**
 * Created by messhair.
 * Date: 2017/1/13
 */
namespace Huanxin\Model;

use Think\Model;

class EmployerModel extends Model{
    protected $trueTableName;

    protected function _initialize($db_table){
        $this->trueTableName=$db_table;
    }
    /**
     * 根据用户名查询
     * @param $telephone
     * @return array
     */
    public function getEmployer($telephone) {
        return $this->field('password,truename,userpic')->where(array('telephone'=>$telephone))->find();
    }

    /**
     * 取出employer表中所有未开通环信的账号
     * @return mixed
     */
    public function getAllEmployers() {
        return $this->field('telephone as username,password,truename as nickname')->where(array('haveim'=>0))->select();
    }

    /**
     * 更新表中haveim为1
     * @param $save_up
     * @return bool
     */
    public function saveIm($save_up) {
        $where['telephone']=array('in',$save_up);
        $data=array('haveim'=>1);
        return $this->where($where)->data($data)->save();
    }

    /**
     * 取出所有可以添加环信好友的telephone账号
     * @param $owner
     * @return mixed
     */
    public function getFriendsList($owner) {
        $owner_id=$this->where(array('telephone'=>$owner))->getField('id');
        $where['id']=array('neq',$owner_id);
        return $this->field('telephone')->where($where)->select();
//        return $this->where($where)->getField('telephone');
    }
}