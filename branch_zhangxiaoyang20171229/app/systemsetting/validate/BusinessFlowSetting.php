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

class BusinessFlowSetting extends Validate{
    protected $rule = [
        'business_flow_name' => 'require',
        'set_to_role' => 'require',
    ];

    protected $message = [
        'business_flow_name.require' => '工作流名称不能为空',
        'set_to_role.require' => '拥有此业务角色不能为空',
    ];
}