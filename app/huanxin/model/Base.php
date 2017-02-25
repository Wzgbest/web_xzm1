<?php
/**
 * Created by messhair.
 * Date: 2017/2/14
 */
namespace app\huanxin\model;

use think\Db;

class Base
{
    protected $model;
    public function __construct($corp_id)
    {
        config('db_config1.database',config('db_common_prefix').$corp_id);
        $this->model = Db::connect('db_config1');
    }
}