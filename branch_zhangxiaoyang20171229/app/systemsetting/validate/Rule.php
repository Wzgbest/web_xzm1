<?php
/**
 * Created by PhpStorm.
 * User: erin
 * Date: 2017/11/10
 */
namespace app\systemsetting\validate;

use think\Validate;
class Rule extends Validate
{
    protected $rule = [
        'rule_title' => ['require','regex'=>'/^[\S]{0,10}/'],
        'rule_name' => ['require'],
    ];
    protected $message = [
        'rule_title.require' => '规则名不能为空',
        'rule_name.require' => '规则英文标识不能为空',
    ];
}