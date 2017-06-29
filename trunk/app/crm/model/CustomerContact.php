<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\model;

use app\common\model\Base;

class CustomerContact extends Base
{
    protected $dbprefix;
    public function __construct($corp_id)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'customer_contact';
        parent::__construct($corp_id);
    }

    public function addCustomerContact($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**根据客户ID获取所有
     * @param $customer_id int 客户id
     * @return false|\PDOStatement|int|\think\Collection
     * created by blu10ph
     */
    public function getAllCustomerContactsByCustomerId($customer_id)
    {
        return $this->model->table($this->table)->alias('cc')
            ->join($this->dbprefix.'employee e','cc.create_user = e.id',"LEFT")
            ->where('customer_id',$customer_id)
            ->field("cc.*,e.truename as create_user_name")
            ->select();
    }

    public function getCustomerContactCount($customer_id)
    {
        return $this->model->table($this->table)->where('customer_id',$customer_id)->count();
    }

    public function getCustomerContact($id)
    {
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    public function setCustomerContact($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
}