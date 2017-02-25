<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return 'index/index';
    }

    /**
     * think unit test
     * @return string
     */
    public function test()
    {
        return 'hello world';
    }

    public function showAuth()
    {
        $uid = 1;
        $rule = 'huanxin/user';
        dump(check_auth($rule,$uid));
    }
}
