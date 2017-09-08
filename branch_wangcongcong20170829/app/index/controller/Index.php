<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\index\controller;

use think\Db;
use app\huanxin\service\Api;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
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

    public function developing(){
        return view();
    }
}
