<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

namespace app\Task\model;

use app\common\model\Base;

class TaskTake extends Base{
    protected $dbprefix;
    public function __construct($corp_id = null){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix') . 'employee_task_take';
        parent::__construct($corp_id);
    }

    /**
     * 添加一条任务参与信息
     * @param  array $data 任务信息
     * @return int 任务ID
     */
    public function addTaskTake($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 添加多条任务参与信息
     * @param  array $datas 任务信息
     * @return int 任务ID
     */
    public function addMutipleTaskTake($datas){
        return $this->model->table($this->table)->insertAll($datas);
    }

    /**
     * 获取某个任务的所有参与信息
     * @param  int $task_id 任务ID
     * @return array 任务信息
     */
    public function getTaskTakeListByTaskId($task_id){
        return $this->model->table($this->table)
            ->where("task_id",$task_id)
            ->select();
    }

    /**
     * 获取某个任务的所有参与员工id
     * @param  int $task_id 任务ID
     * @return array 任务信息
     */
    public function getTaskTakeIdsByTaskId($task_id){
        return $this->model->table($this->table)
            ->where("task_id",$task_id)
            ->order("id asc")
            ->column("take_employee");
    }

    /**
     * 获取某条参与信息
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getTaskTakeInfoById($id){
        return $this->model->table($this->table)
            ->where("id",$id)
            ->find();
    }

    /**
     * 已帮
     * @param $con
     * @return int|string
     */
    public function toHelp($con){
        $data['whether_help']=1;
        $result=$this->model->table($this->table)->where($con)->data($data)->update();
        return $result;
    }

    /**
     * 未帮
     * @param $con
     * @return int|string
     */
    public function toUnhelp($con){
        $data['whether_help']=-1;
        $result=$this->model->table($this->table)->where($con)->data($data)->update();
        return $result;

    }

    /**
     * 悬赏任务参与人排行榜列表
     * @param $start_time int
     * @param $end_time int
     * @param $uids array 任务参与人id的数组集合
     * @param $task_id int 任务id
     * @param $standard int
     * @param $num int
     * @param $page int
     * @param $map array
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getEmployeeRanking($start_time,$end_time,$uids,$task_id,$standard=0,$num=10,$page=0,$map=null){
        if(empty($uids)){
            return [];
        }
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $map['e.id']=array('in',$uids);
        $map['t.task_id']=$task_id;
        $rankingList=$this->model->table($this->dbprefix.'employee e')
            ->join($this->dbprefix.'employee_task_take t','e.id=t.take_employee','LEFT')
            ->where($map)
            //->limit($offset,$num)
            ->field("e.id as employee_id,e.telephone,e.truename,t.whether_help,t.id as take_id")
            ->select();
        return $rankingList;
    }
}