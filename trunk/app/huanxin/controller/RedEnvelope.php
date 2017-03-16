<?php
/**
 * Created by messhair
 * Date: 17-3-13
 */
namespace app\huanxin\controller;

use app\huanxin\controller\User;
//use myvendor\RedBonus;
use app\huanxin\model\RedEnvelope as RedB;
use app\common\model\Employer;
use app\huanxin\job\OverTimeRedEnvelope;

class RedEnvelope
{
    public function index()
    {
        $data=json_encode(['title'=>'job red 1'],true);
        \think\Queue::push('huanxin/OverTimeRedEnvelope',$data);
    }

    /**
     * 生成红包
     * @param \app\huanxin\controller\User $user
     * @param userid
     * @param access_token
     * @param totalmoney
     * @param num
     * @return string
     */
    public function createRedEnvelope(User $user)
    {
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $total_money = intval(input('param.totalmoney'));
        $num = intval(input('param.num'));

        $r = $user->checkUserAccess($userid,$access_token);
        if (!$r['status']) {
            return json_encode($r,true);
        }

        $data = get_red_bonus($total_money,$num);
        $red_id = md5(time().rand(1000,9999));
        $indata=[];
        foreach ($data as $key => $val) {
            $indata[$key]['money'] = $val;
            $indata[$key]['fromuser'] = $r['userinfo']['id'];
            $indata[$key]['redid'] = $red_id;
            $indata[$key]['create_time'] = time();
            $indata[$key]['total_money'] = $total_money;
        }
        $redM = new RedB($r['corp_id']);
        $employerM = new Employer($r['corp_id']);

        $de_money = $total_money*100;
        $info['status'] = false;
        $redM->link->startTrans();
        try{
            $res = $redM->createRedId($indata);
            $de = $employerM->setEmployerSingleInfo($userid,['left_money'=>['exp',"left_money - $de_money"]]);
        }catch(\Exception $e){
            $redM->link->rollback();
            $info['message'] = '生成红包失败';
        }
        if ($res >0 && $de >0) {
            $redM->link->commit();
            write_log($r['userinfo']['id'],2,'用户创建红包,总金额'.$de_money.'分，共'.$num.'个',$r['corp_id']);
            $info['status'] = true;
            $info['message'] = '生成红包成功';
            $info['redid'] = $red_id;
        } else {
            $redM->link->rollback();
            $info['message'] = '生成红包失败';
        }
        return json_encode($info,true);

//        $right = ($total_money/$num) * config('red_envelope.max_money_rate');
//        $dto = new RedBonus($total_money,$num,config('red_envelope.min_money'),$right);
//        $data = $dto->create();
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

        $red_money = $red_data['money']*100;
        $add = ['left_money'=>['exp',"left_money + $red_money"]];
        $records = [
            'took_time'=>time(),
            'is_token' =>1,
            'took_user' => $r['userinfo']['id']
        ];
        $redM->link->startTrans();
        try{
            $res = $redM->setOneRedId($red_data['id'],$records);
            $re = $employerM->setEmployerSingleInfo($userid,$add);
        }catch(\Exception $e){
            $redM->link->rollback();
        }
        if ($res > 0 && $re > 0) {
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

    public function overTimeRed($red_id='',$now='')
    {
        $red = new OverTimeRedEnvelope();
        $now = 1489450580;
        $red_id = '5ad78ce5ec2b1f8f58d8efad3bafb966';
        $red->overTimeJob('sdzhongxun',$now,$red_id);
    }

}