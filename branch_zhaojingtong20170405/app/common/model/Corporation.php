<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\common\model;

use think\Db;

class Corporation extends Db
{
    /**
     * 根据公司代号查询
     * @param $corp_id
     * @return array
     */
    public static function getCorporation($corp_id)
    {
        return Db::name('corporation')->where('corp_id',$corp_id)->find();
    }

    /**
     * 根据id查询
     * @param $id 公司表id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getCorpId ($id)
    {
        return Db::name('corporation')->where('id',$id)->find();
    }

    public static function getAllCorpIds()
    {
        return Db::name('corporation')->field('corp_id')->select();
    }

    /**
     * 更新公司表信息
     * @param $corp_id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public static function setCorporationInfo($corp_id,$data)
    {
        return Db::name('corporation')->where('corp_id',$corp_id)->update($data);
    }
}