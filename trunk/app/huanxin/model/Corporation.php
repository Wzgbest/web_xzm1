<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\huanxin\model;

use think\Db;

class Corporation
{
    /**
     * 根据公司代号查询
     * @param $corp_id
     * @return array
     */
    public static function getCorporation($corp_id)
    {
        return Db::name('corporation')->where('corp_id',$corp_id)->field('id,corp_id,corp_name')->find();
//        return $this->field('id,corp_id,corp_name')->where(array('corp_id'=>$corp_id))->find();
//        return self::where('corp_id',$corp_id)->column('id','corp_id','corp_name');
    }
}