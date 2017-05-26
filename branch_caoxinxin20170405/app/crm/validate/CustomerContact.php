<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\validate;

use think\Validate;

class CustomerContact extends Validate
{
    protected $rule = [
        'customer_id' => 'require|number',
        'contact_name' => ['require','regex'=>'/^[\x0391-\xFFE5]+$/'],
        'phone_first' => ['require','regex'=>'/^(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7})|(?:(13[0-9]|15[012356789]|18[0236789]|14[5789])[0-9]{8}))$/'],
    ];

    protected $message = [
        'customer_id.require' => '客户ID不能为空',
        'contact_name.require' => '客户名称不能为空',
        'contact_name.regex' =>'客户名称格式不正确',
        'phone_first.require' => '手机号码不能为空',
        'phone_first.regex' => '手机号码格式不正确',
    ];
}