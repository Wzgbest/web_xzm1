<?php
/**
 * Created by messhair.
 * Date: 2017/1/14
 */
namespace Huanxin\Model;

use Think\Model;

class UserCorporationModel extends Model{

    /**
     * ���ݵ绰��ѯ��˾��
     * @param $tel
     * @return string
     */
    public function getUserCorp($tel) {
        return $this->where(array('telephone'=>$tel))->getField('corp_name');
    }
}
