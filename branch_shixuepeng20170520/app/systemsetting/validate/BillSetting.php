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

class BillSetting extends Validate
{
    protected $rule = [
        'bill_type' => 'require',
        'need_tax_id' => 'require|number',
    ];

    protected $message = [
        'bill_type.require' => '发票设置名称不能为空',
        'need_tax_id.require' => '是否需要公司税号不能为空',
    ];
}