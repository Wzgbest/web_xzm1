<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

namespace app\task\controller;

use app\common\controller\Initialize;
use app\task\model\EmployeeTask as EmployeeTaskModel;
use app\task\model\TaskTarget as TaskTargetModel;
use app\task\model\TaskReward as TaskRewardModel;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\common\model\Corporation;

class TaskGuess extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function show(){
    }


    public function guess(){
    }
    public function get_list(){
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取任务猜输赢时发生错误！"];

        $result['status'] = 1;
        $result['info'] = "获取任务猜输赢成功！";
        return json($result);
    }
}