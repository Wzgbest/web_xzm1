<?php
/**
 * Created by: messhair
 * Date: 2017/5/6
 */
namespace app\common\controller;

use think\Controller;

class Initialize extends Controller
{
    protected $corp_id;
    public function _initialize()
    {
        $userinfo = session('userinfo');
        if (empty($userinfo)) {
            $this->redirect('/login/index/index');
        }
        $this->corp_id = get_corpid();
//        $this->corp_id = 'sdzhonghu';
    }
}