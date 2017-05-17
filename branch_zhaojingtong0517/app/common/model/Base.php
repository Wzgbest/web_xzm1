<?php
/**
 * Created by messhair
 * Date: 17-3-11
 */
namespace app\common\model;

use think\Db;

class Base
{
    protected $model;
    public $link;
    public function __construct($corp_id)
    {
        config('db_config1.database',config('db_common_prefix').$corp_id);
        $this->model = Db::connect('db_config1');
        $this->link =$this->model->getConnection();
    }
}