<?php
/**
 * Created by messhair.
 * Date: 2017/2/8
 */
namespace app\common\model;

use think\Db;

class UserCorporation extends Db
{
    /**
     * 根据电话查询公司名
     * @param $tel
     * @return string
     * created by messhair
     */
    public static function getUserCorp($tel)
    {
        return Db::name('user_corporation')->where('telephone',$tel)->value('corp_name');
    }

    /**
     * 删除多条员工--公司对照信息
     * @param $tels 员工电话
     * @return int
     * @throws \think\Exception
     * created by messhair
     */
    public static function deleteUserCorp($tels)
    {
        return Db::name('user_corporation')->where('telephone','in',$tels)->delete();
    }

    /**
     * 添加单条记录
     * @param $data
     * @return int|string
     * created by messhair
     */
    public static function addSingleUserTel($data)
    {
        return Db::name('user_corporation')->insert($data);
    }

    /**
     * 添加多条记录
     * @param $data
     * @return int|string
     * created by messhair
     */
    public static function addMutipleUserTel($data)
    {
        return Db::name('user_corporation')->insertAll($data);
    }
    public static function setUserCorpByPhone($tel)
    {
        $data['telephone']=$tel;
        return Db::name('user_corporation')->where('telephone',$tel)->update($data);
    }
}