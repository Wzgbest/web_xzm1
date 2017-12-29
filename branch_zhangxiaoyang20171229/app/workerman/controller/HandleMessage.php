<?php
/**
 * Created by messhair
 * Date: 17-3-30
 */
namespace app\workerman\controller;

class HandleMessage
{
    public function recordCall()
    {
        $info = [
            'status' =>true,
            'message' => '记录成功'
        ];
        return $info;
    }
}