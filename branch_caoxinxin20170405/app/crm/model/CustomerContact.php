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
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'customer_contact';
        parent::__construct($corp_id);
    }

    public function addCustomerContact($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }
}