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

class Customer extends Validate
{
    protected $rule = [
        'customer_name' => ['require','regex'=>'/^[\x0391-\xFFE5]+$/'],
        'telephone' => ['require','regex'=>'/^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}|17[0-9]{9}$|18[0-9]{9}$|^((0(10|2[0-9]|[3-9]\d{2}))[- ]?[1-9]\d{6,7})$|^400[0-9]{7}$/'],
        'resource_from' => 'require|number',
    ];

    protected $message = [
        'customer_name.require' => '客户名称不能为空',
        'customer_name.regex' =>'客户名称格式不正确',
        'telephone.require' => '手机号码不能为空',
        'telephone.regex' => '手机号码格式不正确',
        'resource_from.require' => '客户来源不能为空',
    ];
}