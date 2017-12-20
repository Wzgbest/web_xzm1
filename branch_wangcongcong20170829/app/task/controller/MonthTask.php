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
use app\common\model\Employee;
use app\huanxin\model\TakeCash;
use app\common\model\Corporation;
use app\task\model\EmployeeTask as EmployeeTaskModel;
use app\task\model\TaskTarget as TaskTargetModel;
use app\task\model\TaskReward as TaskRewardModel;
use app\task\model\TaskTake as TaskTakeModel;
use app\task\model\TaskGuess as TaskGuessModel;
use app\task\model\TaskComment as TaskCommentModel;
use app\task\model\TaskTip as TaskTipModel;
use app\task\service\EmployeeTask as EmployeeTaskService;
use app\common\model\Structure;
use app\huanxin\service\RedEnvelope as RedEnvelopeService;
use app\huanxin\model\RedEnvelope as RedEnvelopeModel;
use app\crm\model\Customer as CustomerModel;
use think\View;

class MonthTask extends Initialize{
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
    }
}