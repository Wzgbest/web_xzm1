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
        'contact_name' => ['require','regex'=>'/^[\x0391-\xFFE5]+$/'],
        'phone_first' => ['require','regex'=>'/^(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7})|(?:(13[0-9]|15[012356789]|18[0236789]|14[5789])[0-9]{8}))$/'],
        'phone_second'=>['different:phone_first','regex'=>'/^(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7})|(?:(13[0-9]|15[012356789]|18[0236789]|14[5789])[0-9]{8}))$/'],
        'phone_third'=>['different:phone_first','different:phone_second','regex'=>'/^(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7})|(?:(13[0-9]|15[012356789]|18[0236789]|14[5789])[0-9]{8}))$/'],
    ];

    protected $message = [
        'contact_name.require' => '客户名称不能为空',
        'contact_name.regex' =>'客户名称格式不正确',
        'phone_first.require' => '联系方式1格式不正确',
        'phone_second.different' => '联系方式2和联系方式1重复',
        'phone_second.require' => '联系方式2格式不正确',
        'phone_third.different' => '联系方式3和其他联系方式重复',
        'phone_third.require' => '联系方式3格式不正确',
    ];
}