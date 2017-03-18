<?php
/**
 * Created by messhair
 * Date: 17-3-18
 */
namespace app\huanxin\service;

import('myimport.alipaysdk.aop.AopClient',EXTEND_PATH);
import('myimport.alipaysdk.aop.request.AlipayFundTransToaccountTransferRequest',EXTEND_PATH);
use app\common\model\CorporationAlipay;

class TakeCash
{
    /**
     * 系统账户向员工转账
     * @param $corp_id 公司代号
     * @param $trans_data array 存储转账信息
     * @return array
     * @throws \Exception
     */
    public function handleCash($corp_id,$trans_data)
    {
        $aop = new \AopClient();
        $alipay_config = new CorporationAlipay();
        $config = $alipay_config->getAlipaySetting($corp_id);
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        $aop->appId = $config['alipay_appid'];
        $aop->alipayrsaPublicKey = $config['alipay_public_key'];
        $aop->rsaPrivateKey = $config['alipay_private_key'];
        $aop->signType = 'RSA2';
        unset($config);

        $trans = new \AlipayFundTransToaccountTransferRequest();
        $biz_content = [
            'out_biz_no' => $trans_data['order_num'],
            'payee_type' => 'ALIPAY_LOGONID',
            'payee_account' => $trans_data['recv_account'],
            'amount' => $trans_data['take_money'],//单位元
//            'payer_real_name' => '上海交通卡公司',
//            'payer_show_name' => '上海交通卡退款',
            'remark' => $trans_data['remark'],
        ];
        $biz_content = json_encode($biz_content,true);
        $trans->setBizContent($biz_content);
        $result = $aop->execute($trans);

        $responseNode = str_replace(".", "_", $trans->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $info['status'] = false;
        if (!empty($resultCode) && $resultCode == 10000) {
            $info['status'] = true;
            $info['message'] = 'Success';
        } else {
            $info['message'] = '提现转账失败';
        }
        return $info;
    }
}