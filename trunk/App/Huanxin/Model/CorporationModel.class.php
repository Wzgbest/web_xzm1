<?php
/**
 * Created by messhair.
 * Date: 2017/1/13
 */
namespace Huanxin\Model;

use Think\Model;

class CorporationModel extends Model{

    /**
     * ���ݹ�˾���Ų�ѯ
     * @param $corp_id
     * @return mixed
     */
    public function getCorporation($corp_id) {
        return $this->field('id,corp_id,corp_name')->where(array('corp_id'=>$corp_id))->find();
    }
}