<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\Task\controller;

use app\common\controller\Initialize;
use app\task\model\TaskTip;
use app\task\model\EmployeeTask as EmployeeTaskModel;
use app\task\model\TaskComment as TaskCommentModel;
use app\common\model\Employee;
use app\task\model\TaskTake as TaskTakeModel;
use app\task\model\TaskReward as TaskRewardModel;
use app\huanxin\model\TakeCash;
use app\task\model\TaskGuess as TaskGuessModel;
use app\task\model\TaskTip as TaskTipModel;
use app \index\controller\SystemMessage;

class EmployeeTask extends Initialize{

	/**
	 * 获取任务列表
	 * @return arr [description]
	 */
	public function taskList(){
		$result = ['status'=>0,'info'=>"获取列表时失败!"];

		$num = input('num',10,'int');
		$last_id = input('last_id',0,'int');
		$task_type = input('task_type',0,'int');
		$user_info = get_userinfo();
		$uid = $user_info['userid'];
		$employeeTaskModel = new EmployeeTaskModel($this->corp_id);
		$task_list = $employeeTaskModel->getEmployeeTaskAndRedEnvelopeList($uid,$num,$last_id,$task_type);

        $uids = [];
        foreach ($task_list as $task_info){
            $uids_temp = explode(",",$task_info["public_to_take"]);
            if(!empty($uids_temp)){
                $uids = array_merge($uids,$uids_temp);
            }
            $uids_temp = explode(",",$task_info["public_to_view"]);
            if(!empty($uids_temp)){
                $uids = array_merge($uids,$uids_temp);
            }
        }
        $uids = array_filter($uids);
        $uids = array_unique($uids);
        $employM = new Employee($this->corp_id);
        $employees_name = $employM->getEmployeeNameByUserids($uids);

        foreach ($task_list as &$task_info){
            $uids_temp = explode(",",$task_info["public_to_take"]);
            $uids_temp = array_flip($uids_temp);
            $uids_temp = array_intersect_key($employees_name,$uids_temp);
            if(!empty($uids_temp)){
                $task_info["public_to_take_name"] = $uids_temp;
            }
            $uids_temp = explode(",",$task_info["public_to_view"]);
            $uids_temp = array_flip($uids_temp);
            $uids_temp = array_intersect_key($employees_name,$uids_temp);
            if(!empty($uids_temp)){
                $task_info["public_to_view_name"] = $uids_temp;
            }
        }

        $result['data'] = $task_list;
		$result['status'] = 1;
		$result['info'] = "获取成功!";

		return json($result);
	}

	/**
	 * 我的任务列表
	 * @return arr 任务列表
	 */
	public function myTaskList(){
		$result = ['status'=>0,'info'=>"获取列表失败!"];

		$num = input('num',10,'int');
		$last_id = input('last_id',0,'int');
		$task_type = input('task_type',0,'int');
		$is_direct = input('is_direct',0,'int');
		$is_indirect = input('is_indirect',0,'int');
		$is_own = input('is_own',0,'int');
		$is_old = input('is_old',0,'int');
		$user_info = get_userinfo();
		$uid = $user_info['userid'];
		$employeeTaskModel = new EmployeeTaskModel($this->corp_id);
		$my_task_list = $employeeTaskModel->getMyTaskList($uid,$num,$last_id,$task_type,$is_direct,$is_indirect,$is_own,$is_old);
        //var_exp($my_task_list,'$my_task_list',1);

        $uids = [];
        foreach ($my_task_list as $task_info){
            $uids_temp = explode(",",$task_info["public_to_take"]);
            if(!empty($uids_temp)){
                $uids = array_merge($uids,$uids_temp);
            }
            $uids_temp = explode(",",$task_info["public_to_view"]);
            if(!empty($uids_temp)){
                $uids = array_merge($uids,$uids_temp);
            }
        }
        $uids = array_filter($uids);
        $uids = array_unique($uids);
        $employM = new Employee($this->corp_id);
        $employees_name = $employM->getEmployeeNameByUserids($uids);

        foreach ($my_task_list as &$task_info){
            $uids_temp = explode(",",$task_info["public_to_take"]);
            $uids_temp = array_flip($uids_temp);
            $uids_temp = array_intersect_key($employees_name,$uids_temp);
            if(!empty($uids_temp)){
                $task_info["public_to_take_name"] = $uids_temp;
            }
            $uids_temp = explode(",",$task_info["public_to_view"]);
            $uids_temp = array_flip($uids_temp);
            $uids_temp = array_intersect_key($employees_name,$uids_temp);
            if(!empty($uids_temp)){
                $task_info["public_to_view_name"] = $uids_temp;
            }
        }

		$result['status'] = 1;
		$result['info'] = "获取列表成功!";
		$result['data'] = $my_task_list;
		return json($result);
	}

    /**
     * 任务大厅里的任务列表数据
     */
	public function get_task_list(){
        $result = ['status'=>0,'info'=>"获取列表时失败!"];

        $num = input('num',10,'int');
        $p = input("p",1,"int");
        $task_type = input('task_type',0,'int');
        $order_name=input('order_name','','string');
        $map=[];
        $url_args['p']=2;
        $url_args['order_name']=$order_name;
        $url_args['task_type']=$task_type;
        if($task_type){
            $map['task_type']=$task_type;//任务类型
        }
        if($order_name){
            $order=$order_name;
        }
        else{
            $order='id';
        }
        $user_info = get_userinfo();
        $uid = $user_info['userid'];
        $employeeTaskModel = new EmployeeTaskModel($this->corp_id);
        $field="et.*,case when etl.user_id>0 then 1 else 0 end as is_like,re.redid,re.is_token,re.total_money,case when tg.guess_employee>0 then 1 else 0 end as is_guess,case when ett.take_employee>0 then 1 else 0 end as is_take";
        $task_list = $employeeTaskModel->getEmployeeTaskList($uid,$num,$p,$field,$order,$direction="desc",$map,'');
        //var_exp($task_list,'$task_list',1);
        $countField=["
        count(1) as `0`,
        sum((case when task_type = 1 then 1 else 0 end)) as `1`,
        sum((case when task_type =2 then 1 else 0 end)) as `2`,
        sum((case when task_type =3 then 1 else 0 end)) as `3`,
        sum((case when task_type =4 then 1 else 0 end)) as `4`
        "];//统计个数的field
        $task_count=$employeeTaskModel->getEmployeeTaskCount($uid,$countField,[]);
        for($i=1;$i<5;$i++){
            if(!$task_count[$i]){
                $task_count[$i] = 0;
            }
        }
        //var_exp($task_count,'$task_count',1);
        $this->assign('task_list',$task_list);
        $this->assign('task_count',$task_count);
        $this->assign('uid',$uid);
        $this->assign('p',$p);
        $this->assign('now_time',$this->request->time());
        $this->assign('url_args',$url_args);//搜索条件
//        var_exp($task_list);
    }
    public function hot_task()
    {
        $this->get_task_list();
        return view();
    }
    public function hot_task_load(){
        $this->get_task_list();
        return view();
    }

    /**
     * 历史任务 进行中的任务列表数据
     */
    public function get_historical_task_list($map){
        if(!isset($map["status"])){
            $map['status']=array('gt',0);
        }

        $num = input('num',10,'int');
        $p = input("p",1,"int");
        $part_type = input('task_type',1,'int');//任务参与类型，1直接参与，2间接参与，3我发起的
        $order_name=input('order_name','','string');

        $user_info = get_userinfo();
        $uid = $user_info['userid'];

        $map_str='';
        switch($part_type){
            case 1:
                //直接参与，报名参加的
                $map_str = " find_in_set($uid,take_employees) ";
                break;
            case 2:
                //间接参与 打赏的 猜输赢的
                $map_str = " find_in_set($uid,tip_employees) or find_in_set($uid,guess_employees) ";

                break;
            case 3:
                //我发起的
                $map_str = " create_employee=".$uid;
                break;
        }

        $url_args['p']=2;
        $url_args['task_type']=$part_type;
        $url_args['order_name']=$order_name;
        if($order_name){
            $order=$order_name;
        }
        else{
            $order='id';
        }
        $employeeTaskModel = new EmployeeTaskModel($this->corp_id);
        $field="et.*,case when etl.user_id>0 then 1 else 0 end as is_like,re.redid,re.is_token,re.total_money,case when tg.guess_employee>0 then 1 else 0 end as is_guess,case when ett.take_employee>0 then 1 else 0 end as is_take";
        $task_list = $employeeTaskModel->getEmployeeTaskList($uid,$num,$p,$field,$order,$direction="desc",$map,$map_str);
        $con['task_end_time']=$map['task_end_time'];
        $con['status']=$map['status'];
        $map_str1 = " find_in_set($uid,take_employees) ";
        $count1=$employeeTaskModel->getHistoricalTaskCount($uid,'*',$con,$map_str1);
        $map_str1 = " find_in_set($uid,tip_employees) or find_in_set($uid,guess_employees) ";
        $count2=$employeeTaskModel->getHistoricalTaskCount($uid,'*',$con,$map_str1);
        $map_str1 = " create_employee=".$uid;
        $count3=$employeeTaskModel->getHistoricalTaskCount($uid,'*',$con,$map_str1);
        $task_count=array(
            '1'=>$count1,
            '2'=>$count2,
            '3'=>$count3
        );
        $this->assign('task_list',$task_list);
        $this->assign('task_count',$task_count);
        $this->assign('uid',$uid);
        $this->assign('p',$p);
        $this->assign('url_args',$url_args);//搜索条件
        $this->assign('now_time',$this->request->time());

    }

    /**
     * 历史任务
     * @return \think\response\View
     */
    public function historical_task(){
        $map['task_end_time']=array('lt',time());
        $map['status']=array('gt',2);
        $this->get_historical_task_list($map);
        return view();
    }
    public function historical_task_load(){
        $map['task_end_time']=array('lt',time());
        $map['status']=array('gt',2);
        $this->get_historical_task_list($map);
        return view();
    }

    /**
     * 进行中的任务
     * @return \think\response\View
     */
    public function direct_participation(){
        $map['task_end_time']=array('egt',time());
        $map['status']=array('eq',2);
        $this->get_historical_task_list($map);
        return view();
    }
    public function direct_participation_load(){
        $map['task_end_time']=array('egt',time());
        $map['status']=array('eq',2);
        $this->get_historical_task_list($map);
        return view();
    }

    /**
     * 赞与取消赞
     */
    public function task_like(){
        $task_id=input('id');//任务id
        $unlike=input('unlike');//是否是取消赞
        $user_info = get_userinfo();
        $uid = $user_info['userid'];//操作员工id
        $con['task_id']=$task_id;
        $con['user_id']=$uid;
        $redata['success']=false;
        $redata['msg']='操作失败';
        $employeeTaskModel = new EmployeeTaskModel($this->corp_id);
        if($task_id){
            if($unlike){
                //取消赞 执行删除操作
                $result=$employeeTaskModel->delLike($con);
            }
            else{
                //赞
                $result=$employeeTaskModel->addLike($con);

            }
            if($result)
            {
                $redata['success']=true;
                $redata['msg']='操作成功';
                if (!$unlike) {
                    //发送点赞消息
                    $task_data = $employeeTaskModel->getEmployeeById($task_id);
                    $userinfos = $user_info['userinfo'];
                    $sysMsg = new SystemMessage();
                    $str = $userinfos['truename']."点赞了你发布的".$task_data['task_name']."任务";
                    $receive_uids[] = $task_data['create_employee'];
                    $sysMsg->save_msg($str,"/task/index/show/id/".$task_id,$receive_uids,3,1,$task_id);
                }
            }
        }
        return json($redata);
    }

    /**
     * 终止任务
     */
    public function task_end(){
        $task_id=input('task_id',0,'int');
        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskInfo($task_id);
        $redata['status']=0;
        $redata['info']='操作失败';
        $time = time();
        if(!$task_id){
            $redata['info'] = "参数错误！";
            return json($redata);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $task_detailInfo = $employeeTaskM->getTaskMoreInfo($uid,$task_id);
        if($task_detailInfo['status']!=2){
            $redata['info'] = "当前任务不能终止！";
            return json($redata);
        }
        if($task_detailInfo['create_employee']!=$uid){
            $redata['info'] = "无权限终止任务！";
            return json($redata);
        }
        if($task_detailInfo['task_end_time']<=$time)
        {
            $redata['info'] = "任务已结束无法终止！";
            return json($redata);
        }
        switch($task_detailInfo['task_type']){
            case 1:
                //激励任务,发布一天之内可以终止
                if(strtotime('-1 day') > $task_detailInfo['create_time']){
                    $redata['info'] = "激励任务发布超过一天无法终止！";
                    return json($redata);
                }
                break;
            case 2:
                //PK任务，仅有自己参与可以终止
                if($task_detailInfo['partin_count']>=1 && $task_detailInfo['take_employees'].''!=$uid.''){
                    $redata['info'] = "任务已有参与人无法终止！";
                    return json($redata);
                }
                break;
            case 3:
                //悬赏任务,没有参与人可以终止
                if($task_detailInfo['partin_count']>0){
                    $redata['info'] = "任务已有参与人无法终止！";
                    return json($redata);
                }
                break;

        }

        try{
            $employeeM = new Employee($this->corp_id);
            $cashM = new TakeCash($this->corp_id);
            $taskGuessM = new TaskGuessModel($this->corp_id);
            $taskTipM = new TaskTipModel($this->corp_id);
            $taskTakeM = new TaskTakeModel($this->corp_id);
            $taskRewardM = new TaskRewardModel($this->corp_id);
            $employeeTaskM->link->startTrans();
            $result=$employeeTaskM->setTaskStatus($task_id,'',0);//终止任务
            if(!$result){
                exception("更新任务状态发生错误!");
            }
            $takeList = $taskTakeM->getTaskTakeListByTaskId($task_id);
            $returnMoney = [];
            $order_datas = [];
            if($taskInfo["task_type"]==1){
                $returnMoney[$taskInfo["create_employee"]] = $taskInfo["reward_count"];
                $order_add_data = [
                    'userid'=>$taskInfo["create_employee"],
                    "take_type"=>5,
                    "take_type_sub"=>8,
                    "take_id"=>$task_id,
                    'take_money'=> $taskInfo["reward_count"],
                    'take_status'=>1,
                    'took_time'=>$time,
                    'remark' => '任务失败退回',
                    'status'=>1
                ];
                if($taskInfo["pay_type"]==1){
                    $order_add_data["money_type"] = 2;
                }else{
                    $order_add_data["money_type"] = 1;
                }
                $order_datas[] = $order_add_data;
            }elseif($taskInfo["task_type"]==2){
                $taskReward = $taskRewardM->findTaskRewardByTaskId($task_id);
//                var_exp($taskReward,'$taskReward');
                foreach ($takeList as $taskTakeEmployee){
                    $returnMoney[$taskTakeEmployee["take_employee"]] = $taskReward["reward_amount"];
                    $order_add_data = [
                        'userid'=>$taskTakeEmployee["take_employee"],
                        "take_type"=>5,
                        "take_type_sub"=>8,
                        "take_id"=>$task_id,
                        'take_money'=> $taskReward["reward_amount"],
                        'take_status'=>1,
                        'took_time'=>$time,
                        'remark' => '任务失败退回',
                        'status'=>1,
                        "money_type"=>1
                    ];
                    $order_datas[] = $order_add_data;
                }
            }elseif($taskInfo["task_type"]==3){
                $returnMoney[$taskInfo["create_employee"]] = $taskInfo["reward_count"];
                $order_add_data = [
                    'userid'=>$taskInfo["create_employee"],
                    "take_type"=>5,
                    "take_type_sub"=>8,
                    "take_id"=>$task_id,
                    'take_money'=> $taskInfo["reward_count"],
                    'take_status'=>1,
                    'took_time'=>$time,
                    'remark' => '任务失败退回',
                    'status'=>1,
                    "money_type"=>1
                ];
                $order_datas[] = $order_add_data;
            }

            $taskGuessAndTipMoneyEmployeeIdx = [];

            //返还打赏记录
            $taskTipInfoList = $taskTipM->getTipMoneyList($task_id);
            foreach($taskTipInfoList as $taskTipInfo){
                $order_add_data = [
                    'userid'=>$taskTipInfo["tip_employee"],
                    "take_type"=>6,
                    "take_type_sub"=>2,
                    "take_id"=>$task_id,
                    'take_money'=> $taskTipInfo["tip_money"],
                    'take_status'=>1,
                    'took_time'=>$time,
                    'remark' => '猜输赢任务终止退回',
                    'status'=>1,
                    "money_type"=>1
                ];
                $order_datas[] = $order_add_data;
                if(isset($taskGuessAndTipMoneyEmployeeIdx[$taskTipInfo["tip_employee"]])){
                    $taskGuessAndTipMoneyEmployeeIdx[$taskTipInfo["tip_employee"]] += $taskTipInfo["tip_money"];
                }else{
                    $taskGuessAndTipMoneyEmployeeIdx[$taskTipInfo["tip_employee"]] = $taskTipInfo["tip_money"];
                }
            }

            //返还猜输赢记录
            $taskGuessInfoList = $taskGuessM->getGuessInfoList($task_id);
            foreach($taskGuessInfoList as $taskGuessInfo){
                $order_add_data = [
                    'userid'=>$taskGuessInfo["guess_employee"],
                    "take_type"=>5,
                    "take_type_sub"=>11,
                    "take_id"=>$task_id,
                    'take_money'=> $taskGuessInfo["guess_money"],
                    'take_status'=>1,
                    'took_time'=>$time,
                    'remark' => '猜输赢任务终止退回',
                    'status'=>1,
                    "money_type"=>1
                ];
                $order_datas[] = $order_add_data;
                $taskGuessAndTipMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]] += $taskGuessInfo["guess_money"];
            }

//            var_exp($order_datas,'$order_datas',1);
            if(!empty($order_datas)){
                $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
//            var_exp($add_cash_rec,'$add_cash_rec',1);
                if (!$add_cash_rec) {
                    exception("添加任务终止退回记录发生错误!");
                }
            }


            //返还任务金额
            foreach($returnMoney as $employee_id=>$money) {
                $employeeMonyField = "";
                if($taskInfo["pay_type"]==1){
                    $employeeMonyField = "corp_";
                }
                $money = bcmul($money, 100, 0);
                $employeeInfoMap = [$employeeMonyField."frozen_money" => ["egt", $money]];
                $employeeInfo = [];
                $employeeInfo[$employeeMonyField."frozen_money"] = ['exp', $employeeMonyField."frozen_money - $money"];
                $employeeInfo[$employeeMonyField."left_money"] = ['exp', $employeeMonyField."left_money + " . $money];
                $update_user = $employeeM->setEmployeeSingleInfoById($employee_id, $employeeInfo, $employeeInfoMap);
                if (!$update_user) {
                    exception("更新返还任务冻结金额发生错误!");
                }
            }

            //返还打赏猜输赢等用户额度
            // var_exp($taskGuessAndTipMoneyEmployeeIdx,'$taskGuessAndTipMoneyEmployeeIdx');
            foreach($taskGuessAndTipMoneyEmployeeIdx as $employee_id=>$money){
                $employeeInfo["left_money"] = ['exp',"left_money + ".bcmul($money,100,0)];
                $update_user = $employeeM->setEmployeeSingleInfoById($employee_id,$employeeInfo);
                // var_exp($update_user,'$update_user',1);
                if (!$update_user) {
                    exception("返还打赏猜输赢金额发生错误!");
                }
            }
            $employeeTaskM->link->commit();
        }catch(\Exception $ex){
            $employeeTaskM->link->rollback();
//            return json($redata);
            return $ex->getTrace();
        }

        //发送消息
        $sysMsg = new SystemMessage();
        $take_arr = $employeeTaskM->getTakeTaskById($task_id);
        $guess_arr = $employeeTaskM->getGuessTaskById($task_id);
        $tip_arr = $employeeTaskM->getTipTaskById($task_id);
        $receive_uids = array_merge($take_arr,$guess_arr,$tip_arr);
        $receive_uids = array_unique($receive_uids);
        if (!empty($receive_uids)) {
            $sysMsg->save_msg("你参与的".$taskInfo['task_name']."任务已被强制终止","/task/index/show/id/".$task_id,$receive_uids,3,1,$task_id);
        }
        
        $redata['status']=1;
        $redata['info']='操作成功';
        return json($redata);
    }

    /**
     * 已帮未帮
     * @return \think\response\Json
     */
    public function task_help(){
        $task_id=input('task_id',0,"int");//参与任务的id
        $employee_id=input('employee_id',0,"int");//参与任务的员工id
        $unhelp=input('unhelp',0,"int");//是帮助了还是未帮
        $redata['success']=false;
        $redata['msg']='操作失败';
        if(!$task_id || !$employee_id){
            $redata['msg'] = "参数错误！";
            return json($redata);
        }
        $taskTakeModel = new TaskTake($this->corp_id);
        $taskTakeInfo=$taskTakeModel->getTaskTakeInfoByTaskIdAndEmployee($task_id,$employee_id);
        if(empty($taskTakeInfo)){
            $redata['msg'] = "未找到任务参与信息！";
            return json($redata);
        }
        $taskModel=new EmployeeTaskModel($this->corp_id);
        $taskInfo=$taskModel->getTaskInfo($task_id);
        if(empty($taskInfo)){
            $redata['msg'] = "未找到任务！";
            return json($redata);
        }
        if($taskInfo["task_type"]!=3){
            $redata['msg'] = "该任务不是悬赏任务！";
            return json($redata);
        }
        if($taskInfo["create_employee"]!=$this->uid){
            $redata['msg'] = "你不是该任务的发起人！";
            return json($redata);
        }
        if($taskInfo['task_end_time']<strtotime("-3 days")){
            //如果未判定三天之内可以判定，超过三天后判定并自动结算
            $redata['msg'] = "该任务不再能判定！";
            return json($redata);
        }
        if($taskInfo['status']!=2){
            $redata['msg'] = "该任务不在进行中！";
            return json($redata);
        }
        $result = false;
        if($unhelp){ //未帮
            $result=$taskTakeModel->employeeToUnhelp($task_id,$employee_id);
        }else{ //已帮
            $result=$taskTakeModel->employeeToHelp($task_id,$employee_id);
        }
        if($result){
            $redata['success']=true;
            $redata['msg']='操作成功';
        }
        return json($redata);
    }

}