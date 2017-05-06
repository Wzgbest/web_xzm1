<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;

class Customer extends Base
{
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'customer';
        parent::__construct($corp_id);
    }

    public function getAllCustomers($userid,$scale)
    {
        return $this->model->table($this->table)
            ->field('customer_name,resource_from,telephone,add_man,add_batch,handle_man,take_time,field,grade,address,location,website,remark,belongs_to,is_public,public_to_employer,public_to_department,is_partner')
            ->where('belongs_to',$scale)
            ->where('handle_man',$userid)
            ->select();
    }

    public function addCustomer($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    public function setCustomer($customer_id,$data)
    {
        return $this->model->table($this->table)->where('id',$customer_id)->update($data);
    }
}