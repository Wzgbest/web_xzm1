<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Business;

class BusinessFlow extends Initialize{
    public function index()    {
        $business_flow = new Business();
        return view();
    }
}