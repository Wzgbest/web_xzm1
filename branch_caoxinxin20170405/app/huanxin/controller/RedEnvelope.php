<?php
/**
 * Created by messhair
 * Date: 17-3-13
 */
namespace app\huanxin\controller;

use app\huanxin\model\RedEnvelope as RedB;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\huanxin\service\RedEnvelope as RedEnvelopeService;
use app\common\controller\Initialize;

class RedEnvelope extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 生成红包
     * @param userid
     * @param access_token
     * @param redtype  1运气红包 2普通红包
     * @param totalmoney 单位元 3.33
     * @param num
     * @return string
     */
    public function createRedEnvelope(){
        $total_money = input('param.totalmoney');
        $redtype = input('param.redtype');
        $num = intval(input('param.num'));
        $pay_pass = input('param.paypassword');

        $red_money = intval($total_money*100);
        $info['status'] = false;
        if (($redtype != 1) && ($redtype !=2)  ) {
            $info['message'] = '红包类型有误';
            $info['errnum'] = 5;
            return json($info);
        }
        if ($red_money < 1 ) {
            $info['message'] = '创建红包的总金额过少';
            $info['errnum'] = 1;
            return json($info);
        }
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$total_money)) {
            $info['message'] = '红包总金额格式不正确';
            $info['errnum'] = 2;
            return json($info);
        }
        if ($num > $red_money) {
            $info['message'] = '创建红包的总金额过少';
            $info['errnum'] = 3;
            return json($info);
        }
        $user_info = get_userinfo();

        if (md5($pay_pass) != $user_info['userinfo']['pay_password']) {
            $info['message'] = '支付密码错误';
            $info['errnum'] = 6;
            return json($info);
        }

        if ($user_info['userinfo']['left_money'] < $red_money) {
            $info['message'] = '账户余额不足';
            $info['errnum'] = 5;
            return json($info);
        }

        $data = get_red_bonus($total_money,$num,$redtype);
        $red_id = md5(time().rand(1000,9999));
        $indata=[];
        $time = time();
        //红包数据
        foreach ($data as $key => $val) {
            $indata[$key]['money'] = $val;
            $indata[$key]['fromuser'] = $this->uid;
            $indata[$key]['redid'] = $red_id;
            $indata[$key]['create_time'] = $time;
            $indata[$key]['total_money'] = $total_money;
        }
        $redM = new RedB($this->corp_id);
        $cashM = new TakeCash($this->corp_id);

        $de_money = $total_money*100;
        //take_cash表记录
        $order_data = [
            'userid'=>$this->uid,
            'take_money'=> -$de_money,
            'status'=>1,
            'took_time'=>$time,
            'remark' => '创建红包'
        ];
        $redM->link->startTrans();
        try{
            $res = $redM->createRedId($indata);
            $employM = new Employee($this->corp_id);
            $de = $employM->setEmployeeSingleInfo($this->telephone,['left_money'=>['exp',"left_money - $de_money"]]);
            $cash_rec = $cashM->addOrderNumber($order_data);
        }catch(\Exception $ex){
            $redM->link->rollback();
            $info['message'] = '生成红包失败';
        }
        if ($res >0 && $de >0 && $cash_rec > 0) {
            $redM->link->commit();
            write_log($this->uid,2,'用户创建红包成功,总金额'.$de_money.'分，共'.$num.'个',$this->corp_id);

            $employM = new Employee($this->corp_id);
            $user_info = $employM->getEmployeeByTel($this->telephone);
            set_userinfo($this->corp_id,$this->telephone,$user_info);

            $info['status'] = true;
            $info['message'] = '生成红包成功';
            $info['errnum'] = 0;
            $info['redid'] = $red_id;
        } else {
            $redM->link->rollback();
            write_log($this->uid,2,'用户创建红包失败,总金额'.$de_money.'分，共'.$num.'个',$this->corp_id);
            $info['message'] = '生成红包失败';
            $info['errnum'] = 4;
        }
        return json($info);
    }
    /**
     * 红包领取情况
     * @param userid
     * @param access_token
     * @param redid 红包标识
     
     * @return string
     */
    public function getFetchedRedEnvelope(){
        $red_id = input('param.redid');
        $info['status'] = false;
        if (!preg_match('/[0-9a-fA-F]{32}/',$red_id)) {
            $info['message'] = '红包id错误';
            $info['errnum'] = 1;
            $info['overtime'] = false;
            return json($info);
        }

        $redM = new RedB($this->corp_id);
        $red_num_total = $redM->getRedCount($red_id);
        if (empty($red_num_total)) {
            $info['message'] = '红包id错误';
            $info['errnum'] = 2;
            $info['overtime'] = false;
            return json($info);
        }

        $pre_red = $redM->getRedInfoByRedId($red_id);
        if (empty($pre_red)) {
            $info['overtime'] = true;
            $info['errnum'] = 3;
            $info['message'] = '红包已过期';
        } elseif (time()>($pre_red[0]['create_time']+config('red_envelope.overtime'))) {
            $params = json_encode([
                'userid'=>$this->uid,
                'corp_id'=>$this->corp_id,
                'red_data'=>$pre_red[0]
            ],true);
            //Hook::listen('check_over_time_red',$params);
            $info['message'] = '红包已经过期';
            $info['errnum'] = 3;
            $info['overtime'] = true;
        }else{
            $info['overtime'] = false;
            $info['message'] = 'SUCCESS';
            $info['errnum'] = 0;
        }
        $red_data = $redM->getFetchedRedList($red_id);
        $info['status'] = true;
        $info['left_num'] = $red_num_total - count($red_data);
        $info['total_num'] = $red_num_total;
        $info['red_info'] = $red_data;

        return json($info);
    }

    /**
     * 领取红包
     
     * @param userid
     * @param access_token
     * @param redid 红包标识
     * @return string
     */
    public function fetchRedEnvelope(){
        $red_id = input('param.redid');
        $result = ['status'=>false,'errnum'=>1,'message'=>'领取红包错误'];
        if (!preg_match('/[0-9a-fA-F]{32}/',$red_id)) {
            $result['message'] = '红包id错误';
            return json($result);
        }

        $redEnvelopeS = new RedEnvelopeService($this->corp_id);
        $getRedEnvelopeInfo = $redEnvelopeS->getRedEnvelope($red_id);
        if($getRedEnvelopeInfo["status"]==1){
            $result['status'] = true;
            $result['message'] = '恭喜领取成功';
            $result['errnum'] = 0;
            $result['money'] = $getRedEnvelopeInfo['data']['money'];
            $result['red_info'] = $getRedEnvelopeInfo['data']["red_info"];
        }else{
            $result['status'] = false;
            $result['message'] = $getRedEnvelopeInfo["info"];
            $result['errnum'] = $getRedEnvelopeInfo["status"];
        }
        return json($result);
    }
    /**
     * 红包领取明细
     * @param userid
     * @param access_token
     
     * @return string
     */
    public function getMyRedEnvelopeList(){

        $result = ['status'=>0 ,'info'=>"查询红包收支明细时发生错误！"];
        $num = 10;
        $p = input("p",1,"int");

        try{
            $map["type"] = ["neq",3];
            $redM = new RedB($this->corp_id);
            $myRedEnvelopeList = $redM->getMyRedEnvelope($num,$p,$this->uid,$map);
            $result['data'] = $myRedEnvelopeList;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询红包收支明细成功！";
        return json($result);
    }
}