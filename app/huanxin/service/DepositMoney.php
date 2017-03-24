<?php
/**
 * Created by messhair
 * Date: 17-3-20
 */
namespace app\huanxin\service;

import('myimport.alipaysdk.aop.AopClient',EXTEND_PATH);
import('myimport.alipaysdk.aop.request.AlipayTradeQueryRequest',EXTEND_PATH);
import('myimport.alipaysdk.aop.AlipayConfig',EXTEND_PATH);

class DepositMoney
{

    /**
     * 根据app充值后得到的订单号trade_no查询
     * @param $trade_no 支付宝返回的支付宝系统订单号
     * @return bool
     * @throws \Exception
     */
    public function queryTradeNumber($trade_no,$out_trade_no,$money)
    {
        $aop = new \AopClient();
        $alipay_config = new \AlipayConfig();
        $config = $alipay_config->getAlipaySetting();
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';//TODO 测试开启
        $aop->appId = $config['appid'];
        $aop->alipayrsaPublicKey = $config['public_key'];
//        $aop->rsaPrivateKey = $config['alipay_private_key'];//用密钥字符串
        $aop->rsaPrivateKeyFilePath = $config['private_key_path'];
        $aop->signType = 'RSA2';
        unset($config);

        $query = new \AlipayTradeQueryRequest();
        $data = ['trade_no' => $trade_no];
        $info['status'] = false;
        $query->setBizContent(json_encode($data,true));

        $result = $aop->execute($query);
        $responseNode = str_replace(".", "_", $query->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000 ){
            $result_out_trade_no = $result->$responseNode->out_trade_no;
            $result_money = $result->$responseNode->total_amount;
            if ($out_trade_no==$result_out_trade_no && $result_money==$money) {
                $info['status'] = true;
                $info['message'] = 'SUCCESS';
            }else {
                $info['message'] = '支付的订单与系统订单不符,或用户充值金额与实际支付金额不匹配';
            }
        } else {
            $info['message'] = '查询订单未支付，或用户未充值成功';
        }
        return $info;
    }

    /**
     * 处理支付宝异步通知
     * @param $raw_data
     * @param $alipay_info 订单表记录
     * @return bool
     */
    public function checkAlipaySign($raw_data,$alipay_info)
    {
        $aop = new \AopClient();
        $alipay_config = new \AlipayConfig();
        $config = $alipay_config->getAlipaySetting();
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';//TODO 测试开启
        $aop->appId = $config['appid'];
        $aop->alipayrsaPublicKey = $config['public_key'];
//        $aop->rsaPrivateKey = $config['alipay_private_key'];//用密钥字符串
        $aop->rsaPrivateKeyFilePath = $config['private_key_path'];
        $aop->signType = 'RSA2';
        unset($config);

        $app_id = $raw_data['app_id'];
        $trade_no = $raw_data['trade_no'];
        $total_amount = $raw_data['total_amount'];
//            $seller_id = $raw_data['seller_id'];

        if (empty($alipay_info) || $alipay_info['status'] == 1) {
            return false;
        }
        if ($alipay_info['money'] != $total_amount * 100 || $app_id != $aop->appId) {
            return false;
        }
        $result = $aop->rsaCheckV2($raw_data, $aop->alipayrsaPublicKey, $aop->signType);
        $result = true;//TODO 测试开启
        if ($result) {
            if ($raw_data['trade_status'] == 'TRADE_SUCCESS' ||$raw_data['trade_status'] == 'TRADE_FINISHED') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}