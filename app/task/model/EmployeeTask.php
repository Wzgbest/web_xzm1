<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\Task\model;

use app\common\model\Base;
use think\Db;

class EmployeeTask extends Base{
	protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'employee_task';
        $this->viewTable=config('database.prefix').'view_employee_task';
        parent::__construct($corp_id);
    }

    /**
     * 获取一条任务信息
     * @param  int $task_id 任务id
     * @return arr          任务信息
     */
    public function getEmployeeById($task_id){
    	$employeeTaskInfo = $this->model->table($this->table)->where("id",$task_id)->find();
    	return $employeeTaskInfo;
    }

    /**
     * 添加一条任务信息
     * @param  array $data 任务信息
     * @return int 任务ID
     */
    public function addTask($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 获取一条任务信息
     * @param  int $id 任务ID
     * @return array 任务信息
     */
    public function getTaskInfo($id){
        return $this->model->table($this->table)
            ->where("id",$id)
            ->find();
    }

    /**
     * 获取一条任务信息
     * @param  int $uid 用户ID
     * @param  int $task_id 任务ID
     * @return array 任务信息
     */
    public function getTaskMoreInfo($uid,$task_id){
//        return $this->model->table($this->table)->alias('et')
//            ->join($this->dbprefix.'employee e','e.id = et.create_employee',"LEFT")
//            ->join($this->dbprefix.'employee_task_like etl',"etl.task_id = et.id and etl.user_id = '$uid'","LEFT")
//            ->where("et.id",$task_id)
//            ->group("et.id")
//            ->field("et.*,e.telephone,e.truename,e.userpic,case when etl.user_id>0 then 1 else 0 end as is_like")
//            ->find();
        $map['et.id']=$task_id;
        $field='et.*,case when etl.user_id>0 then 1 else 0 end as is_like,re.redid,re.is_token,re.total_money,case when tg.guess_employee>0 then 1 else 0 end as is_guess,case when ett.take_employee>0 then 1 else 0 end as is_take';
        $recorder= $this->model->table($this->viewTable)->alias('et')
            ->join($this->dbprefix.'employee_task_like etl',"etl.task_id = et.id and etl.user_id = '$uid'","LEFT")
            ->join($this->dbprefix.'employee_task_guess tg',"tg.task_id=et.id and tg.guess_employee=".$uid,"LEFT")
            ->join($this->dbprefix.'employee_task_take ett','ett.task_id=et.id','LEFT')
            ->join($this->dbprefix.'red_envelope re',"re.task_id = et.id and re.type = 3 and re.took_user = ".$uid,"LEFT")
            ->field($field)->where($map)->find();
        $recorder['public_to_take_array']=get_employee_truename($recorder['public_to_take']);
        return $recorder;
    }

     /**
     * 获取任务列表
     * @param  int $uid     用户id
     * @param  int $num     获取数量
     * @param  int $last_id 获取到的最大id
     * @param  int $task_type 任务类型
     * @return arr          任务列表
     */
    public function getEmployeeTaskAndRedEnvelopeList($uid,$num=10,$last_id=0,$task_type=0,$map=[]){
        $order = "et.id desc";
        $mapStr = "(find_in_set('".$uid."',et.public_to_view) and et.status>1) or create_employee=".$uid;
        if ($task_type) {
            $map['et.task_type'] = $task_type;
        }
        if($last_id){
            $map["et.id"] = ["lt",$last_id];
        }

        $employeeTaskList = $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee e','e.id = et.create_employee',"LEFT")
            ->join($this->dbprefix.'employee_task_take ettk',"ettk.task_id = et.id and ettk.take_employee = '$uid'","LEFT")
            ->join($this->dbprefix.'employee_task_reward etr','etr.task_id = et.id',"LEFT")
            ->join($this->dbprefix.'employee_task_target ett','ett.task_id = et.id',"LEFT")
            ->join($this->dbprefix.'employee_task_like etl',"etl.task_id = et.id and etl.user_id = '$uid'","LEFT")
            ->join($this->dbprefix.'red_envelope re',"re.task_id = et.id and re.type = 3 and re.took_user = ".$uid,"LEFT")
            ->join($this->dbprefix.'customer c','ett.target_customer = c.id',"LEFT")
            ->where($map)
            ->where($mapStr)
            ->order($order)
            ->limit($num)
            ->group("et.id")
            ->field("et.*,e.telephone,e.truename,e.userpic,etr.reward_amount,etr.reward_num,etr.reward_type,etr.reward_method,case when ettk.take_employee>0 then 1 else 0 end as is_take,ett.target_type,ett.target_customer,c.customer_name,ett.target_description,ett.target_num,case when etl.user_id>0 then 1 else 0 end as is_like,re.redid,re.is_token")
            ->select();

        return $employeeTaskList;
    }

    /**
     * 我的直接参与的任务
     * @param  int $uid       用户id
     * @param  int $num       获取数量默认10
     * @param  int $last_id   最后一天任务的id     
     * @param  int $task_type 任务类型（悬赏、pk）
     * @param  int $is_direct 直接任务
     * @param  int $is_indirect 间接任务
     * @param  int $is_own 我发布的任务
     * @param  int $is_old 历史任务
     * @param  array $map 筛选条件
     * @return arr            [description]
     */
    public function getMyTaskList($uid,$num=10,$last_id=0,$task_type=0,$is_direct=0,$is_indirect=0,$is_own=0,$is_old=0,$map=[]){
        $map_str = "";
        $old_map_str = "";
        $order = "et.id desc";
        if ($last_id) {
            $map['et.id'] = ['lt',$last_id];
        }
        if ($task_type) {
            $map['et.task_type'] = $task_type;
        }
        if ($is_direct) {
            $map['wett.take_employee'] = $uid;
        }
        if ($is_indirect) {
            $map_str .= "wettip.tip_employee = $uid or wetguess.guess_employee = $uid";
        }
        if ($is_own) {
            $map['et.create_employee'] = $uid;
        }
        if ($is_old) {
            //$map['et.task_end_time'] = array('lt',time());
            $old_map_str .="et.task_end_time < ".time()." or et.status > 2";
        }else{
            $map['et.task_end_time']=array('egt',time());
            $map['et.status'] = array('eq',2);
        }
        $myTaskList = $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee eown','eown.id = et.create_employee',"LEFT")
            ->join($this->dbprefix.'employee_task_take ett',"ett.task_id = et.id","LEFT")
            ->join($this->dbprefix.'employee_task_take wett',"wett.task_id = et.id and ett.take_employee = '$uid'","LEFT")
            ->join($this->dbprefix.'employee_task_reward etr','etr.task_id = et.id',"LEFT")
            ->join($this->dbprefix.'employee_task_target ettar','ettar.task_id = et.id',"LEFT")
            ->join($this->dbprefix.'employee_task_like etl',"etl.task_id = et.id and etl.user_id = '$uid'","LEFT")
            ->join($this->dbprefix.'employee_task_tip wettip',"wettip.task_id = et.id and wettip.tip_employee = '$uid'","LEFT")
            ->join($this->dbprefix.'employee_task_guess wetguess',"wetguess.task_id = et.id and wetguess.guess_employee = '$uid'","LEFT")
            ->join($this->dbprefix.'red_envelope re',"re.task_id = et.id and re.type = 3 and re.took_user = ".$uid,"LEFT")
            ->join($this->dbprefix.'customer c','ettar.target_customer = c.id',"LEFT")
            ->where($map)
            ->where($map_str)
            ->where($old_map_str)
            ->order($order)
            ->limit($num)
            ->group('et.id')
            //->fetchSql(true)
            ->field("et.*,eown.telephone as own_telephone,eown.truename as own_truename,eown.userpic as own_userpic,case when wett.take_employee>0 then 1 else 0 end as is_take,wett.take_time,etr.reward_type,etr.reward_method,etr.reward_amount,etr.reward_num,ettar.target_type,ettar.target_num,ettar.target_customer,c.customer_name,ettar.target_description,case when etl.user_id>0 then 1 else 0 end as is_like,wettip.tip_employee,wettip.tip_money,wettip.tip_time,re.redid,re.is_token")
            ->select();

        return $myTaskList;
    }
    public function getAllStandardTaskId($time){
        $map["et.task_type"] = ["eq",1];
        $map["et.task_method"] = ["eq",1];
        $map["et.task_start_time"] = ["elt",$time];
        $map["et.task_end_time"] = ["egt",$time];
        $map["et.status"] = ["eq",2];
        $order = "et.id asc";
        $standardTaskList = $this->model->table($this->table)->alias('et')
            ->where($map)
            ->order($order)
            ->group('et.id')
            ->column("et.id");
        //var_exp($standardTaskList,'$standardTaskList',1);
        return $standardTaskList;
    }
    public function getAllOverTimeTask($time){
        $map["et.task_end_time"] = ["lt",$time];
        $map["et.task_type"] = ["in",[1,2,3]];
        $map["et.status"] = 2;
        $order = "et.id asc";
        $field = ["et.*"];
        $standardTaskList = $this->model->table($this->table)->alias('et')
            ->where($map)
            ->order($order)
            ->group('et.id')
            ->field($field)
            ->select();
        return $standardTaskList;
    }
    public function getStandardTaskInfoById($id){
        $map["et.id"] = $id;
        $employeeTaskInfo = $this->model->table($this->table)->alias('et')
            ->join($this->dbprefix.'employee_task_take ett',"ett.task_id = et.id","LEFT")
            ->join($this->dbprefix.'red_envelope re',"re.task_id = et.id and re.type = 3 and re.took_user = ett.take_employee","LEFT")
            ->where($map)
            ->group('ett.take_employee')
            ->column("re.redid,re.is_token,re.money","ett.take_employee");
        return $employeeTaskInfo;
    }
    public function setTaskInfo($id,$data,$map=[]){
        $map["et.id"] = $id;
        $updateTaskResult = $this->model->table($this->table)->alias('et')
            ->where($map)
            ->data($data)
            ->update();
        return $updateTaskResult;
    }
    public function setTaskStatus($ids,$from_status='',$to_status){
        $map["et.id"] = ["in",$ids];
        if($from_status || $from_status==='0')
        {
            $map["et.status"] = $from_status;
        }
        $data["et.status"] = $to_status;
        $updateTaskResult = $this->model->table($this->table)->alias('et')
            ->where($map)
            ->data($data)
            ->update();
        return $updateTaskResult;
    }

    /**
     * @param 当前用户的id
     * @param int 每页显示的条数
     * @param int 当前页
     * @param string 查询的列
     * @param string 排序字段
     * @param string 排序方式
     * @param array 筛选条件
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getEmployeeTaskList($uid,$num=10,$page=0,$field='*',$order="id",$direction="desc",$map=[],$con_str=''){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $listOrder = [$order=>$direction];//聚合后排序
        $map_str = " (find_in_set($uid,public_to_view) and status>1) or create_employee=".$uid;
//        $employee_task_list=$this->model->table($this->viewTable)->field($field)->where($map_str)->where($map)->order($listOrder)->limit($offset,$num)->select();
        $employee_task_list=$this->model->table($this->viewTable)->alias('et')
            ->join($this->dbprefix.'employee_task_like etl',"etl.task_id = et.id and etl.user_id = '$uid'","LEFT")
            ->join($this->dbprefix.'red_envelope re',"re.task_id = et.id and re.took_user = ".$uid,"LEFT")
            ->join($this->dbprefix.'employee_task_guess tg',"tg.task_id=et.id and tg.guess_employee=".$uid,"LEFT")
            ->join($this->dbprefix.'employee_task_take ett','ett.task_id=et.id','LEFT')
            ->field($field)->where($map_str)->where($con_str)->where($map)->group('et.id')->order($listOrder)->select();
        $task_listArr=$employee_task_list;
        foreach($employee_task_list as $key=>$value){
            $task_listArr[$key]['public_to_take_array']=get_employee_truename($value['public_to_take']);
        }
        $employee_task_list = $task_listArr;
        return $employee_task_list;

    }

    /**
     * 热门任务，PK任务，激励任务，悬赏任务的数量
     * @param 当前用户的id
     * @param string 查询的列
     * @param array 筛选条件
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getEmployeeTaskCount($uid,$field='*',$map=[]){
        $map_str = " (find_in_set($uid,public_to_view) and status>1) or create_employee=".$uid;
        $employee_task_count=$this->model->table($this->table)->field($field)->where($map_str)->where($map)->find();
        return $employee_task_count;
    }

    /**
     * 历史任务模块参与的任务数量
     * @param $uid
     * @param string $field
     * @param array $map
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getHistoricalTaskCount($uid,$field='*',$map=[],$con_str){
        $map_str = " (find_in_set($uid,public_to_view) and status>1) or create_employee=".$uid;
        $historical_task_count=$this->model->table($this->viewTable)->field($field)->where($map_str)->where($con_str)->where($map)->count(1);
        return $historical_task_count;

    }

    /**
     * 点赞
     * @param $map
     */
    public function addLike($map){
        $map['like_time']=time();
        $result=$this->model->table($this->dbprefix.'employee_task_like')->insert($map);
        if($result)
        {
            $this->model->table($this->table)->where('id', $map['task_id'])
                ->setInc('like_count');
        }
        return $result;
    }
    /**
     * 取消赞
     * @param $map
     */
    public function delLike($map){
        $result=$this->model->table($this->dbprefix.'employee_task_like')->where($map)->delete();
        if($result)
        {
            $this->model->table($this->table)->where('id', $map['task_id'])
                ->setDec('like_count');
        }
        return $result;
    }
}