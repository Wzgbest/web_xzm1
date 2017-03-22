<?php
/**
 * Created by messhair
 * Date: 17-3-20
 */
namespace app\huanxin\controller;

use app\huanxin\controller\User;
use app\huanxin\model\TakeCash;
use app\huanxin\service\DepositMoney as DepositMoneyService;
use app\huanxin\model\AppAlipayTrade;

class DepositMoney
{
    public function index()
    {
    }

    /**
     * 根据app充值参数生成订单
     * @param userid
     * @param access_token
     * @param money 充值金额，单位元，3.33
     * @param \app\huanxin\controller\User $user
     * @return string
     */
    public function createAppPrepay(User $user)
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $money = input('param.money');

        $info['status'] = false;
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$money)) {
            $info['message'] = '用户充值金额格式不正确';
            return json_encode($info,true);
        }
        $total_money = intval($money*100);
        if ($total_money < 1) {
            $info['message'] = '用户充值金额过少';
            return json_encode($info,true);
        }
        $r = $user->checkUserAccess($userid,$access_token);
        if (!$r['status']) {
            return json_encode($r,true);
        }

        $out_trade_no = 'guguo_app_pay'.date('YmdHis',time()).time().rand(1000,9999);
        $data = [
            'corp_id' => $r['userinfo']['corpid'],
            'userid' =>$r['userinfo']['id'],
            'money' =>$total_money,
            'out_trade_no' =>$out_trade_no,
            'create_time' =>time(),
            'status' => 0
        ];
        $res = AppAlipayTrade::addTradeInfo($data);
        if ($res > 0) {
            $info['status'] = true;
            $info['message'] = 'SUCCESS';
            $info['out_trade_no'] = $out_trade_no;
            $info['paymoney'] = $money;
            write_log($r['userinfo']['id'],5,'生成用户订单，订单号'.$out_trade_no,$r['corp_id']);
        } else {
            $info['message'] = '生成订单失败';
        }
        return json_encode($info,true);
    }

    /**
     * 接收app端发送的通知，给用户充值
     * @param userid
     * @param access_token
     * @param money 充值金额，单位元，3.33
     * @param trade_no 支付宝系统内部账单
     * @param out_trade_no 生成的订单号返回给app端
     * @param \app\huanxin\controller\User $user
     * @return string
     */
    public function getAppNotice(User $user)
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $money = input('param.money');
        $trade_no = input('param.trade_no');
        $out_trade_no = input('param.out_trade_no');

        $info['status'] = false;
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$money)) {
            $info['message'] = '用户充值金额格式不正确';
            return json_encode($info,true);
        }
        $total_money = intval($money*100);
        if ($total_money < 1) {
            $info['message'] = '用户充值金额过少';
            return json_encode($info,true);
        }
        if (!preg_match('/^guguo_app_pay[0-9]{28}/',$out_trade_no)) {
            $info['message'] = '订单格式不正确';
            return json_encode($info,true);
        }
        $r = $user->checkUserAccess($userid,$access_token);
        if (!$r['status']) {
            return json_encode($r,true);
        }
        $trade_info = AppAlipayTrade::getTradeInfo($out_trade_no);
        if (empty($trade_info)) {
            $info['message'] = '提交的订单不存在';
            return json_encode($info,true);
        }
        $depoM = new DepositMoneyService();
        $res = $depoM->queryTradeNumber($trade_no,$out_trade_no,$money);
        if (!$res['status']) {
            return json_encode($res,true);
        }

        //兑换货币
        $left_money = $total_money + $r['userinfo']['left_money'];
        $cashM = new TakeCash($r['corp_id']);
        //take_cash记录
        $cash_data = [
            'userid'=>$r['userinfo']['id'],
            'take_money'=> $total_money,
            'status'=>2,
            'truename'=>$r['userinfo']['truename'],
            'took_time'=>time(),
            'remark' => '用户充值'
        ];
        //app_alipay_trade表，订单状态改变
        $app_data = [
            'status' => 1,
            'pay_time' =>time(),
            'trade_no' => $trade_no
        ];
        $cashM->link->startTrans();
        try {
            $add = $user->employM->setEmployerSingleInfo($userid,['left_money' => $left_money]);
            $cash_rec = $cashM->addOrderNumber($cash_data);
            $app_r = AppAlipayTrade::setTradeStatus($out_trade_no,$app_data);
        } catch (\Exception $e){
            $cashM->link->rollback();
        }
        if ($add > 0 && $cash_rec > 0 && $app_r > 0) {
            $cashM->link->commit();
            write_log($r['userinfo']['id'],5,'用户充值成功,总金额'.$total_money.'分',$r['corp_id']);
            $info['status'] = true;
            $info['message'] = '用户充值成功';
        } else {
            $cashM->link->rollback();
            write_log($r['userinfo']['id'],5,'用户充值成功,兑换系统货币失败，总金额'.$total_money.'分',$r['corp_id']);
            send_mail('wangqiwen@winbywin.com','充值问题','支付宝充值成功，兑换货币失败'.json_encode($cash_data,true));
            $info['message'] = '兑换系统货币失败，联系管理员';
        }
        return json_encode($info,true);
    }
}