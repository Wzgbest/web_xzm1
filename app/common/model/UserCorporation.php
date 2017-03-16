<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\common\model;

use think\Db;

class UserCorporation
{
    /**
     * 根据电话查询公司名
     * @param $tel
     * @return string
     */
    public static function getUserCorp($tel)
    {
        return Db::name('user_corporation')->where('telephone',$tel)->value('corp_name');
//        return $this->where(array('telephone'=>$tel))->getField('corp_name');
//        return self::get(['telephone'=>$tel])->getAttr('corp_name');
    }
}