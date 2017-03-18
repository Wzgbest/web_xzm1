<?php
/**
 * Created by messhair
 * Date: 17-3-18
 */
namespace app\common\model;

use think\Db;

class CorporationAlipay
{
    public function getAlipaySetting($corp_id)
    {
        return Db::name('corporation_alipay')->where('corp_id',$corp_id)->find();
    }
}