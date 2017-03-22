<?php
/**
 * Created by messhair
 * Date: 17-3-20
 */
namespace app\huanxin\service;

import('myimport.alipaysdk.aop.AopClient',EXTEND_PATH);
import('myimport.alipaysdk.aop.request.AlipayTradeQueryRequest',EXTEND_PATH);
import('myimport.alipaysdk.aop.AlipayConfig',EXTEND_PATH);
use app\huanxin\model\AppAlipayTrade;

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
}