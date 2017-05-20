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
    protected $corp_id;

    public function __construct($corp_id = null)
    {
        if (is_null($corp_id)) {
            $this->corp_id = get_corpid();
        } else {
            $this->corp_id = $corp_id;
        }
        config('db_config1.database',config('db_common_prefix').$this->corp_id);
        $this->model = Db::connect(config('db_config1'));
        $this->link =$this->model->getConnection();
    }
}