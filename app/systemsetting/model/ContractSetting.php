<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\systemsetting\model;

use app\common\model\Base;

class ContractSetting extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'customer_setting';
        parent::__construct();
    }

    public function getAllContract(){
        $contractSettingList = $this->model
            ->table($this->table)
            ->order("id desc")
            ->field("*")
            ->select();
        return $contractSettingList;
    }
}