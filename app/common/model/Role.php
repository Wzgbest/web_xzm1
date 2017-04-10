<?php
/**
 * Created by messhair
 * Date: 17-4-7
 */
namespace app\common\model;

use app\common\model\Base;

class Role extends Base
{
    /**
     * @param $corp_id 公司名代号，非id
     */
    public function __construct($corp_id)
    {
        $this->table=config('database.prefix').'role_employer';
        parent::__construct($corp_id);
    }
}