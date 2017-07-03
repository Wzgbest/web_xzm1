<?php
/**
 * Created by messhair
 * Date: 17-3-21
 */
namespace app\common\model;

use think\Db;

class CorporationCash
{
    /**
     * 公司资金流动记录
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addCorporationCashInfo($data)
    {
        return Db::name('corporation_cash')->insert($data);
    }
}