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
use app\task\model\DayTask as DayTaskModel;

class Setting extends Initialize{
    public  function index(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $dayTaskM = new DayTaskModel($this->corp_id);
        $dayTaskInfos = $dayTaskM->getDayTaskByCreateEmployee($uid);
        $this->assign('day_task_list',$dayTaskInfos);
        return view();
    }
    public  function template(){
        return view();
    }
    public  function task_list(){
        return view();
    }
}
