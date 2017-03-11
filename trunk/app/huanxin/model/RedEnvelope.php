<?php
/**
 * Created by messhair
 * Date: 17-3-11
 */
namespace app\huanxin\model;

use app\common\model\Base;

class RedEnvelope extends Base
{
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'red_envelope';
        parent::__construct($corp_id);
    }

    public function createRedId($userid,$num,$total_money)
    {
//        $total_money
//        $this->model->table($this->table)->
    }
}