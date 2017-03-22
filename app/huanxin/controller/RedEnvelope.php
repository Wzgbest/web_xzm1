<?php
/**
 * Created by messhair
 * Date: 17-3-13
 */
namespace app\huanxin\controller;

use app\huanxin\controller\User;
use app\huanxin\model\RedEnvelope as RedB;
use app\common\model\Employer;
use app\huanxin\service\OverTimeRedEnvelope;
use app\huanxin\model\TakeCash;
use think\Hook;

class RedEnvelope
{
    public function index()
    {
    }

    /**
     * 生成红包
     * @param \app\huanxin\controller\User $user
     * @param userid
     * @param access_token
     * @param totalmoney 单位元 3.33
     * @param num
     * @return string
     */
    public function createRedEnvelope(User $user)
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $total_money = input('param.totalmoney');
        $num = intval(input('param.num'));

        $red_money = intval($total_money*100);
        $info['status'] = false;
        if ($red_money < 1 ) {
            $info['message'] = '创建红包的总金额过少';
            return json_encode($info,true);
        }
        if (!preg_match('/^[0-9]{1,30}\.[0-9]{1,2}$/',$total_money)) {
            $info['message'] = '红包总金额格式不正确';
            return json_encode($info,true);
        }
        if ($num > $red_money) {
            $info['message'] = '创建红包的总金额过少';
            return json_encode($info,true);
        }
        $r = $user->checkUserAccess($userid,$access_token);
        if (!$r['status']) {
            return json_encode($r,true);
        }

        $data = get_red_bonus($total_money,$num);
        $red_id = md5(time().rand(1000,9999));
        $indata=[];
        $time = time();
        //红包数据
        foreach ($data as $key => $val) {
            $indata[$key]['money'] = $val;
            $indata[$key]['fromuser'] = $r['userinfo']['id'];
            $indata[$key]['redid'] = $red_id;
            $indata[$key]['create_time'] = $time;
            $indata[$key]['total_money'] = $total_money;
        }
        $redM = new RedB($r['corp_id']);
        $cashM = new TakeCash($r['corp_id']);

        $de_money = $total_money*100;
        //take_cash表记录
        $order_data = [
            'userid'=>$r['userinfo']['id'],
            'take_money'=> -$de_money,
            'status'=>1,
            'took_time'=>$time,
            'remark' => '创建红包'
        ];
        $info['status'] = false;
        $redM->link->startTrans();
        try{
            $res = $redM->createRedId($indata);
            $de = $user->employM->setEmployerSingleInfo($userid,['left_money'=>['exp',"left_money - $de_money"]]);
            $cash_rec = $cashM->addOrderNumber($order_data);
        }catch(\Exception $e){
            $redM->link->rollback();
            $info['message'] = '生成红包失败';
        }
        if ($res >0 && $de >0 && $cash_rec > 0) {
            $redM->link->commit();
            write_log($r['userinfo']['id'],2,'用户创建红包成功,总金额'.$de_money.'分，共'.$num.'个',$r['corp_id']);
            $info['status'] = true;
            $info['message'] = '生成红包成功';
            $info['redid'] = $red_id;
        } else {
            $redM->link->rollback();
            write_log($r['userinfo']['id'],2,'用户创建红包失败,总金额'.$de_money.'分，共'.$num.'个',$r['corp_id']);
            $info['message'] = '生成红包失败';
        }
        return json_encode($info,true);
    }

    /**
     * 领取红包
     * @param \app\huanxin\controller\User $user
     * @param userid
     * @param access_token
     * @param redid 红包标识
     * @return string
     */
    public function fetchRedEnvelope(User $user)
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $red_id = input('param.redid');

        $r = $user->checkUserAccess($userid,$access_token);
        if (!$r['status']) {
            return json_encode($r,true);
        }
        if (!preg_match('/[0-9a-fA-F]{32}/',$red_id)) {
            return json_encode(['status'=>false,'message'=>'红包id错误'],true);
        }

        $info['status'] =false;
        $redM = new RedB($r['corp_id']);
        $employerM = new Employer($r['corp_id']);
        $check_if = $redM->checkIfTook($r['userinfo']['id'],$red_id);
        if (!empty($check_if)) {
            $info['message'] = '您已领取红包';
            return json_encode($info,true);
        }
        $red_data = $redM->getOneRedId($red_id);
        if (empty($red_data)) {
            $info['message'] = '红包已被抢光了';
            return json_encode($info,true);
        }
        if (time()>($red_data['create_time']+config('red_envelope.overtime'))) {
            $params = json_encode(['userid'=>$r['userinfo']['id'],'corp_id'=>$r['corp_id'],'red_data'=>$red_data],true);
            Hook::listen('check_over_time_red',$params);
//            $redOver = new OverTimeRedEnvelope($r['userinfo']['id'],$r['corp_id'],$red_data);
//            $b = $redOver->sendBackOverTimeRed();
            $info['message'] = '红包已经过期';
            return json_encode($info,true);
        }

        $red_money = $red_data['money']*100;
        //余额增加
        $add = ['left_money'=>['exp',"left_money + $red_money"]];
        //红包领取状态改变
        $records = [
            'took_time'=>time(),
            'is_token' =>1,
            'took_user' => $r['userinfo']['id']
        ];
        //take_cash记录
        $cash_data = [
            'userid'=>$r['userinfo']['id'],
            'take_money'=>$red_money,
            'status'=>2,
            'took_time'=>time(),
            'remark' => '领取红包'
        ];
        $cashM = new TakeCash($r['corp_id']);
        $redM->link->startTrans();
        try{
            $res = $redM->setOneRedId($red_data['id'],$records);
            $re = $employerM->setEmployerSingleInfo($userid,$add);
            $cash_rec = $cashM->addOrderNumber($cash_data);
        }catch(\Exception $e){
            $redM->link->rollback();
        }
        if ($res > 0 && $re > 0 && $cash_rec > 0) {
            $redM->link->commit();
            write_log($r['userinfo']['id'],2,'用户领取红包,金额'.$red_money.'分',$r['corp_id']);
            $info['message'] = '恭喜领取成功';
            $info['money'] = $red_data['money'];
            $info['status'] = true;
        } else {
            $redM->link->rollback();
            $info['message'] = '红包已被抢光了';
        }
        return json_encode($info,true);
    }

    /**
     * 红包领取情况
     * @param userid
     * @param access_token
     * @param redid 红包标识
     * @param \app\huanxin\controller\User $user
     * @return string
     */
    public function getFetchedRedEnvelope(User $user)
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $red_id = input('param.redid');

        $r = $user->checkUserAccess($userid,$access_token);
        if (!$r['status']) {
            return json_encode($r,true);
        }
        $info['status'] = false;
        if (!preg_match('/[0-9a-fA-F]{32}/',$red_id)) {
            $info['message'] = '红包id错误';
            return json_encode($info,true);
        }

        $redM = new RedB($r['corp_id']);
        $red_num_total = $redM->getRedCount($red_id);
        if (empty($red_num_total)) {
            $info['message'] = '红包id错误';
            return json_encode($info,true);
        }
        $red_data = $redM->getFetchedRedList($red_id);
        $info = [
            'status' => true,
            'message' => 'SUCCESS',
            'total_num' =>$red_num_total,
            'red_info' =>$red_data
        ];
        return json_encode($info,true);
    }
}