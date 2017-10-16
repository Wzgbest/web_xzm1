<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\index\controller;

use app\common\controller\Initialize;
use think\Db;
use think\Controller;

class Index extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        $userinfo = get_userinfo();
        if (empty($userinfo)) {
            $this->redirect('/login/index/index');
        }
        $this->assign("userinfo",$userinfo);
        return view();
    }

    public function map(){
        return view();
    }

    public function select_window(){
        return view();
    }

    public function developing(){
        return view();
    }
}
