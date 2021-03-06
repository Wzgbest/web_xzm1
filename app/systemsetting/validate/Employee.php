<?php
/**
 * Created by messhair.
 * Date: 2017/5/11
 */
namespace app\systemsetting\validate;

use think\Validate;

class Employee extends Validate{
    protected $rule = [
        'truename' => ['require','regex'=>'/^[\x0391-\xFFE5]+$/'],
        'nickname' => ['regex'=>'/^[\x0391-\xFFE5]+$/'],
        'telephone' => ['require','regex'=>'/^(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[5789])[0-9]{8}$/'],
        'wired_phone' => ['regex'=>'/^(?:(?:0\d{2,3}[- ]?[1-9]\d{6,7})|(?:[48]00[- ]?[1-9]\d{6}))$/'],
        'part_phone' => ['regex'=>'/^[0-9]{3,6}$/'],
        'email' => ['regex'=>'/^[\w\+-]+(\.[\w\+-]+)*@[a-z\d-]+(\.[a-z\d-]+)*\.([a-z]{2,4})$/'],
        'is_leader' => 'require|number',
        'worknum' => ['require','regex'=>'/^[0-9a-zA-Z]{1,10}$/'],
        'qqnum' => ['regex' =>'/^[0-9]{5,10}$/'],
        'wechat' => ['regex' =>'/^[0-9a-zA-Z]{6,32}$/'],
        'gender' =>'require|number',
        'user_id' => 'number',
    ];

    protected $message = [
        'truename.require' => '姓名不能为空',
        'truename.regex' =>'姓名格式不正确',
        'telephone.require' => '手机号码不能为空',
        'telephone.regex' => '手机号码格式不正确',
        'nickname.regex' => '昵称格式不正确',
        'wired_phone.regex' => '座机号码格式不正确',
        'part_phone.regex' => '分机号码格式不正确',
        'email.regex' => '邮箱格式不正确',
        'is_leader.require' => '请选择是否是领导',
        'worknum.require' => '工号不能为空',
        'qqnum.regex' => 'QQ号码格式不正确',
        'wechat.regex' => '微信号格式不正确',
        'gender.require' => '性别不能为空',
        'user_id' => '用户id有误',
    ];
}