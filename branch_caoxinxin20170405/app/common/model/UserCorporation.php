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
     */
    public static function deleteUserCorp($tels)
    {
        return Db::name('user_corporation')->where('telephone','in',$tels)->delete();
    }
    
    /**
     * 添加多用户
     * @param array $data
     * @return int|string
     */
    public function addMutipleUserCorp($data)
    {
        return Db::name('user_corporation')->insertAll($data);
    }
}