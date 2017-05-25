<?php
/**
 * Created by messhair.
 * Date: 2017/5/11
 */
namespace app\crm\validate;

use think\Validate;

class SearchCustomer extends Validate
{
    protected $rule = [
        'customer_name' => ['require','regex'=>'/^[\x0391-\xFFE5]+$/'],
        'phone' => ['require','regex'=>'/^(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7})|(?:(13[0-9]|15[012356789]|18[0236789]|14[5789])[0-9]{8}))$/'],
    ];

    protected $message = [
        'customer_name.require' => '客户名称不能为空',
        'customer_name.regex' =>'客户名称格式不正确',
        'telephone.require' => '手机号码不能为空',
        'telephone.regex' => '手机号码格式不正确',
    ];
}