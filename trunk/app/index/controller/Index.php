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

    public function testMail()
    {
        $b = send_mail('wangqiwen@winbywin.com','通信项目邮件测试','通信项目邮件内容',
            ['path'=>'/home/joshua/img.jpg','name'=>'图片查看']);
        dump($b);
    }
}
