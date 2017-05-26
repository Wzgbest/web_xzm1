<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;

class Customer extends Base
{
    protected $dbprefix;
    public function __construct()
    {
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'customer';
        parent::__construct();
    }

    /**
     * 根据员工id查询客户信息
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getCustomerByUserId($user_id)
    {
        return $this->model->table($this->table)->where('handle_man',$user_id)->field('id')->find();
    }

    /**
     * 根据员工ids获取客户信息
     * @param $ids
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getCustomersByUserIds($ids)
    {
        return $this->model->table($this->table)->alias('a')
            ->join($this->dbprefix.'employer b','a.handle_man = b.id')
            ->field('b.id as userid,b.truename,a.id as customer_id')
            ->where('handle_man','in',$ids)
            ->select();
    }

    /**
     * 获取所有客户信息
     * @param $userid 员工id
     * @param $scale 客户类型，1 我的客户 2 公海池 3我的客户 4待处理
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllCustomers($userid,$scale)
    {
        return $this->model->table($this->table)
            ->field('customer_name,resource_from,telephone,add_man,add_batch,handle_man,take_time,field,grade,address,location,website,remark,belongs_to,is_public,public_to_employer,public_to_department,is_partner')
            ->where('belongs_to',$scale)
            ->where('handle_man',$userid)
            ->select();
    }

    /**
     * 添加单个客户信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addCustomer($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 根据客户id修改客户信息
     * @param $customer_id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setCustomer($customer_id,$data)
    {
        return $this->model->table($this->table)->where('id',$customer_id)->update($data);
    }
}