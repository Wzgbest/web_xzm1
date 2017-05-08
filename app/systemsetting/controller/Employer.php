<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Employer as EmployerModel;

class Employer extends Initialize
{
    public function index()
    {}

    /**
     * 查看员工详情
     * @param $user_id 员工id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function showSingleEmployerInfo($user_id)
    {
        $corp_id = get_corpid();
        $employerM = new EmployerModel($corp_id);
        $info = $employerM->getEmployerByUserid($user_id);
        return $info;
    }
}