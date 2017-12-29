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

class ContractSetting extends Validate{
    protected $rule = [
        'contract_name' => 'require',
        'start_num' => 'require|number',
        'end_num' => 'require|number',
        'current_contract' => 'require|number',
        'apply_1' => 'require|number',
    ];

    protected $message = [
        'contract_name.require' => '合同设置名称不能为空',
        'start_num.require' => '合同起始编号不能为空',
        'end_num.require' => '合同结束编号不能为空',
        'current_contract.require' => '当前合同号不能为空',
        'apply_1.require' => '合同审核角色不能为空',
    ];
}