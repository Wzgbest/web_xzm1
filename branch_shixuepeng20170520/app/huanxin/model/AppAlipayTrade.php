<?php
/**
 * Created by messhair
 * Date: 17-3-21
 */
namespace app\huanxin\model;

use think\Db;

class AppAlipayTrade
{
    /**
     * 添加订单信息
     * @param $data
     * @return int|string
     */
    public static function addTradeInfo($data)
    {
        return Db::name('app_alipay_trade')->insert($data);
    }

    /**
     * 根据订单号查询
     * @param $out_trade_no
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getTradeInfo($out_trade_no)
    {
        return Db::name('app_alipay_trade')->where('out_trade_no',$out_trade_no)->find();
    }

    /**
     * 更新系统订单状态
     * @param $out_trade_no
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public static function setTradeStatus($out_trade_no,$data)
    {
        return Db::name('app_alipay_trade')->where('out_trade_no',$out_trade_no)->update($data);
    }
}