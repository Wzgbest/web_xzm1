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
use app\task\model\TaskTake;
use app\huanxin\model\TakeCash;
use app\task\model\TaskGuess as TaskGuessModel;
use app\task\model\TaskTip as TaskTipModel;

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
//        var_exp($task_list);
        $countField=["
        count(1) as `0`,
        sum((case when task_type = 1 then 1 else 0 end)) as `1`,
        sum((case when task_type =2 then 1 else 0 end)) as `2`,
        sum((case when task_type =3 then 1 else 0 end)) as `3`,
        sum((case when task_type =4 then 1 else 0 end)) as `4`
        "];//统计个数的field
        $task_count=$employeeTaskModel->getEmployeeTaskCount($uid,$countField,[]);
        //var_exp($task_list,'$task_list',1);
        $this->assign('task_list',$task_list);
        $this->assign('task_count',$task_count);
        $this->assign('uid',$uid);
        $this->assign('now_time',$this->request->time());
//        var_exp($task_list);
    }
    public function reward_task()
    {
        return view();
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
                unset($map['status']);
                $map_str = " create_employee=".$uid;
                break;
        }

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
        if($part_type!=3){
            $con['status']=$map['status'];
        }
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
            }
        }
        return json($redata);
    }
    public function pk_pay(){
        $money = input('money',0,'int');
        if (!$money) {
            $this->error("输入的金额有误!");
        }
        $this->assign("fr",input('fr','','string'));
        $userinfo = get_userinfo();
        $this->assign('user_money',$userinfo["userinfo"]['left_money']/100);
        $this->assign('money',$money);
        return view();
    }

    /**
     * 终止任务
     */
    public function task_end(){
        $task_id=input('task_id',0,'int');
        $employeeTaskM = new EmployeeTaskModel($this->corp_id);
        $taskInfo = $employeeTaskM->getTaskInfo($task_id);
        $redata['success']=false;
        $redata['msg']='操作失败';
        $time = time();
        try{
            $employeeM = new Employee($this->corp_id);
            $cashM = new TakeCash($this->corp_id);
            $taskGuessM = new TaskGuessModel($this->corp_id);
            $taskTipM = new TaskTipModel($this->corp_id);
            $employeeTaskM->link->startTrans();
            $result=$employeeTaskM->setTaskStatus($task_id,'',0);//终止任务
            if(!$result){
                exception("更新任务状态发生错误!");
            }
            $returnMoney = $taskInfo["reward_count"];

            $order_datas = [];

            $order_add_data = [
                'userid'=>$taskInfo["create_employee"],
                'take_money'=> $returnMoney,
                'status'=>1,
                'took_time'=>$time,
                'remark' => '任务终止退回'
            ];
            $order_datas[] = $order_add_data;
            $taskGuessAndTipMoneyEmployeeIdx = [];

            //返还打赏记录
            $taskTipInfoList = $taskTipM->getTipMoneyList($task_id);
            foreach($taskTipInfoList as $taskTipInfo){
                $order_add_data = [
                    'userid'=>$taskTipInfo["tip_employee"],
                    'take_money'=> $taskTipInfo["tip_money"],
                    'status'=>1,
                    'took_time'=>$time,
                    'remark' => '猜输赢任务终止退回'
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
                    'take_money'=> $taskGuessInfo["guess_money"],
                    'status'=>1,
                    'took_time'=>$time,
                    'remark' => '猜输赢任务终止退回'
                ];
                $order_datas[] = $order_add_data;
                $taskGuessAndTipMoneyEmployeeIdx[$taskGuessInfo["guess_employee"]] += $taskGuessInfo["guess_money"];
            }

            //var_exp($order_datas,'$order_datas_task_fail');
            $add_cash_rec = $cashM->addMutipleOrderNumber($order_datas);
            if (!$add_cash_rec) {
                exception("添加任务终止退回记录发生错误!");
            }

            //返还任务金额
            $returnMoney = bcmul($returnMoney,100,0);
            $employeeInfoMap = ["frozen_money"=>["egt",$returnMoney]];
            $employeeInfo["frozen_money"] = ['exp',"frozen_money - $returnMoney"];
            $employeeInfo["left_money"] = ['exp',"left_money + ".$returnMoney];
            $update_user = $employeeM->setEmployeeSingleInfoById($taskInfo["create_employee"],$employeeInfo,$employeeInfoMap);
            if (!$update_user) {
                exception("更新返还任务冻结金额发生错误!");
            }

            //返还打赏猜输赢等用户额度
            foreach($taskGuessAndTipMoneyEmployeeIdx as $employee_id=>$money){
                $employeeInfo["left_money"] = ['exp',"left_money + ".bcmul($money,100,0)];
                $update_user = $employeeM->setEmployeeSingleInfoById($employee_id,$employeeInfo,$employeeInfoMap);
                if (!$update_user) {
                    exception("返还打赏猜输赢金额发生错误!");
                }
            }
            $employeeTaskM->link->commit();
        }catch(\Exception $ex){
            $employeeTaskM->link->rollback();
            return json($redata);
        }
        $redata['success']=true;
        $redata['msg']='操作成功';
        return json($redata);
    }

    /**
     * 已帮未帮
     * @return \think\response\Json
     */
    public function task_help(){
        $take_id=input('take_id');//参与任务的id
        $unhelp=input('unhelp');//是帮助了还是未帮
        $redata['success']=false;
        $redata['msg']='操作失败';
        $taskTakeModel = new TaskTake($this->corp_id);
        $con['id']=$take_id;
        $taskTakeInfo=$taskTakeModel->getTaskTakeInfoById($take_id);
        if(empty($taskTakeInfo)){
            $redata['msg'] = "未找到任务！";
            return json($redata);
        }
        $task_id=$taskTakeInfo['task_id'];
        $taskModel=new EmployeeTaskModel($this->corp_id);
        $taskInfo=$taskModel->getTaskInfo($task_id);
        if($taskInfo['status']!=2){
            $redata['msg'] = "该任务不在进行中！";
            return json($redata);
        }
        if($taskInfo['task_end_time']<time()){
            $redata['msg'] = "该任务已结束！";
            return json($redata);
        }


        if($take_id){
            if($unhelp){
                //未帮
                $result=$taskTakeModel->toUnhelp($con);
            }
            else{
                //已帮
                $result=$taskTakeModel->tohelp($con);
            }
            if($result)
            {
                $redata['success']=true;
                $redata['msg']='操作成功';
            }
        }
        return json($redata);
    }

}