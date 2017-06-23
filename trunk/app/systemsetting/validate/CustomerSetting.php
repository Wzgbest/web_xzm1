<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\systemsetting\validate;

use think\Validate;

class CustomerSetting extends Validate
{
    protected $rule = [
        'protect_customer_day' => 'require|number',
        'take_times_employee' => 'require|number',
        'take_times_structure' => 'require|number',
        'to_halt_day' => 'require|number',
        'effective_call' => 'require|number',
        'protect_customer_num' => 'require|number',
        'public_sea_seen' => 'require|number',
        'set_to_structure' => ['require','regex'=>'/^([1-9]|(\d{2,})){1}(,([1-9]|(\d{2,})))*$/'],//正则的格式为 非零正数[,非零正数]
    ];

    protected $message = [
        'resource_from.require' => '客户保护天数不能为空',
        'take_times_employee.require' => '员工同一客户领取次数不能为空',
        'take_times_structure.require' => '部门同一客户领取次数不能为空',
        'to_halt_day.require' => '划归停滞客户的天数不能为空',
        'effective_call.require' => '有效通话时间不能为空',
        'protect_customer_num.require' => '保护客户个数不能为空',
        'public_sea_seen.require' => '公海池客户名称是否可见不能为空',
        'set_to_structure.require' => '部门不能为空',
        'set_to_structure.regex' => '部门格式不正确',
    ];
}