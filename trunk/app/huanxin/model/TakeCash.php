<?php
/**
 * Created by messhair
 * Date: 17-3-17
 */
namespace app\huanxin\model;

use app\common\model\Base;
import('');

class TakeCash extends Base
{

    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'take_cash';
        parent::__construct($corp_id);
    }
    public function getCash()
    {
//        $this->model->
    }
}