<?php
/**
 * Created by messhair
 * Date: 17-3-20
 */
namespace app\huanxin\controller;

use app\common\model\Corporation;
use app\huanxin\model\TakeCash;
use app\huanxin\service\DepositMoney as DepositMoneyService;
use app\huanxin\model\AppAlipayTrade;
use app\common\model\Employee;
use app\common\controller\Initialize;

class DepositMoney extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 根据app充值参数生成订单
     * @param userid
     * @param access_token
     * @param money 充值金额，单位元，3.33
     * @return string
     */
    public function createAppPrepay(){
        $money = input('param.money');
        $info['status'] = false;
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$money)) {
            $info['message'] = '用户充值金额格式不正确';
            $info['errnum'] = 1;
            return json($info);
        }
        $total_money = intval($money*100);
        if ($total_money < 1) {
            $info['message'] = '用户充值金额过少';
            $info['errnum'] = 2;
            return json($info);
        }

        $out_trade_no = 'guguo_app_pay'.date('YmdHis',time()).time().rand(1000,9999);
        $data = [
            'corp_id' => $this->corp_id,
            'userid' =>$this->uid,
            'money' =>$total_money,
            'out_trade_no' =>$out_trade_no,
            'create_time' =>time(),
            'status' => 0
        ];
        $res = AppAlipayTrade::addTradeInfo($data);
        if ($res > 0) {
            $info['status'] = true;
            $info['message'] = '生成订单成功';
            $info['errnum'] = 0;
            $info['out_trade_no'] = $out_trade_no;
            $info['paymoney'] = $money;
            write_log($this->uid,5,'生成用户订单，订单号'.$out_trade_no,$this->corp_id);
        } else {
            $info['message'] = '生成订单失败';
            $info['errnum'] = 3;
        }
        return json($info);
    }

    /**
     * 接收app端发送的通知，给用户充值
     * @param userid
     * @param access_token
     * @param money 充值金额，单位元，3.33
     * @param trade_no 支付宝系统内部账单
     * @param out_trade_no 生成的订单号返回给app端
     
     * @return string
     */
    public function getAppNotice(){
        $user_info = get_userinfo();
        $money = input('param.money');
        $trade_no = input('param.trade_no');
        $out_trade_no = input('param.out_trade_no');

        $info['status'] = false;
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$money)) {
            $info['message'] = '用户充值金额格式不正确';
            $info['errnum'] = 1;
            return json($info);
        }
        $total_money = intval($money*100);
        if ($total_money < 1) {
            $info['message'] = '用户充值金额过少';
            $info['errnum'] = 2;
            return json($info);
        }
        if (!preg_match('/^guguo_app_pay[0-9]{28}/',$out_trade_no)) {
            $info['message'] = '订单格式不正确';
            $info['errnum'] = 3;
            return json($info);
        }
        $trade_info = AppAlipayTrade::getTradeInfo($out_trade_no);
        if (empty($trade_info)) {
            $info['message'] = '提交的订单不存在';
            $info['errnum'] = 4;
            return json($info);
        }
        if ($trade_info['status'] ==1) {
            $info['message'] = '该订单已在系统中充值，不要重复提交';
            $info['errnum'] = 5;
            write_log($this->uid,0,'app充值刷单嫌疑',$this->corp_id);
            return json($info);
        }
        $depoM = new DepositMoneyService($this->corp_id);
        $res = $depoM->queryTradeNumber($trade_no,$out_trade_no,$money);
        if (!$res['status']) {
            $res['errnum'] = 6;
            return json($res);
        }

        //兑换货币
        $left_money = $total_money + $user_info['userinfo']['left_money'];
        $cashM = new TakeCash($this->corp_id);
        //take_cash记录
        $cash_data = [
            'userid'=>$this->uid,
            'take_money'=> $total_money,
            'status'=>2,
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
            $employM = new Employee($this->corp_id);
            $add = $employM->setEmployeeSingleInfo($this->telephone,['left_money' => $left_money]);
            $cash_rec = $cashM->addOrderNumber($cash_data);
            $app_r = AppAlipayTrade::setTradeStatus($out_trade_no,$app_data);
        } catch (\Exception $e){
            $cashM->link->rollback();
        }
        if ($add > 0 && $cash_rec > 0 && $app_r > 0) {
            $cashM->link->commit();
            $employM = new Employee($this->corp_id);
            $user_info = $employM->getEmployeeByTel($this->telephone);
            set_userinfo($this->corp_id,$this->telephone,$user_info);
            write_log($this->uid,5,'用户充值成功,总金额'.$total_money.'分',$this->corp_id);
            $info['status'] = true;
            $info['message'] = '用户充值成功';
            $info['errnum'] = 0;
        } else {
            $cashM->link->rollback();
            write_log($this->uid,5,'用户充值成功,兑换系统货币失败，总金额'.$total_money.'分',$this->corp_id);
            send_mail(config('system_email.user'),config('system_email.pass'),'wangqiwen@winbywin.com','充值问题',config('system_email.from_name'),'支付宝充值成功，兑换货币失败'.json_encode($cash_data,true));
            $info['message'] = '兑换系统货币失败，联系管理员';
            $info['errnum'] = 7;
        }
        return json($info);
    }

    /**
     * 接收支付宝异步通知
     * http://webcall.app/index.php/huanxin/deposit_money/getNotifyNotice?notify_time=2017-03-24 09:40:49&notify_type=trade_status_sync&notify_id=4a91b7a78a503640467525113fb7d8bg8e&app_id=2016080200150817&charset=utf-8&version=1.0&sign_type=RSA2&sign=kPbQIjX+xQc8F0/A6/AocELIjhhZnGbcBN6G4MM/HmfWL4ZiHM6fWl5NQhzXJusaklZ1LFuMo+lHQUELAYeugH8LYFvxnNajOvZhuxNFbN2LhF0l/KL8ANtj8oyPM4NN7Qft2kWJTDJUpQOzCzNnV9hDxh5AaT9FPqRS6ZKxnzM=&trade_no=2016071921001003030200089909&out_trade_no=guguo_app_pay1490085658396691&total_amount=200.00&trade_status=TRADE_SUCCESS&seller_id=2088102169636639
     * 通知消息中必含
     *  notify_time=2016-07-19 14:10:49
        notify_type=trade_status_sync
        notify_id=4a91b7a78a503640467525113fb7d8bg8e
        app_id=2016080200150817
        charset=utf-8
        version=1.0
        sign_type=RSA2
        sign=kPbQIjX+xQc8F0/A6/AocELIjhhZnGbcBN6G4MM/HmfWL4ZiHM6fWl5NQhzXJusaklZ1LFuMo+lHQUELAYeugH8LYFvxnNajOvZhuxNFbN2LhF0l/KL8ANtj8oyPM4NN7Qft2kWJTDJUpQOzCzNnV9hDxh5AaT9FPqRS6ZKxnzM=
        trade_no=2016071921001003030200089909
        out_trade_no=0719141034-6418
        total_amount=2.00
        trade_status=TRADE_SUCCESS
        seller_id=2088102169636639
     *
     * @return string
     */
    public function getNotifyNotice()
    {
        $raw_data=input('param.');
        $out_trade_no = $raw_data['out_trade_no'];
        $alipay_info = AppAlipayTrade::getTradeInfo($out_trade_no);
        $corp_id = Corporation::getCorpId($alipay_info['corp_id']);
        $depositM = new DepositMoneyService($corp_id);
        $result = $depositM->checkAlipaySign($raw_data,$alipay_info);
        if (!$result) {
            return 'fail';
        } else {
            $employM = new Employee($corp_id);
            $cashM = new TakeCash($corp_id);
            $in_money = $alipay_info['money'];
            $in_data = [
                'left_money' => ['exp', "left_money + $in_money"]
            ];
            $cash_data = [
                'userid'=>$alipay_info['userid'],
                'take_money'=> $in_money,
                'status'=>2,
                'took_time'=>time(),
                'remark' => '用户充值'
            ];
            $employM->link->startTrans();
            Corporation::startTrans();
            try{
                $add = $employM->setSingleEmployeeInfobyId($alipay_info['userid'],$in_data);
                $cash_rec = $cashM->addOrderNumber($cash_data);
            }catch (\Exception $e){
                $employM->link->rollback();
                Corporation::rollback();
                return 'fail';
            }
            if ($add > 0 && $cash_rec > 0) {
                $employM->link->commit();
                Corporation::commit();
                $employM = new Employee($this->corp_id);
                $user_info = $employM->getEmployeeByTel($this->telephone);
                set_userinfo($this->corp_id,$this->telephone,$user_info);
                write_log($alipay_info['userid'],5,'用户充值成功,总金额'.$in_money.'分',$corp_id['corp_id']);
                return 'success';
            } else {
                $employM->link->rollback();
                Corporation::rollback();
                return 'fail';
            }
        }
    }
}