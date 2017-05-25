<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\crm\model;

use app\common\model\Base;

class CustomerContact extends Base
{
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'customer_contact';
        parent::__construct($corp_id);
    }

    public function getAllCustomerContacts($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    public function addCustomerContact($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }
}