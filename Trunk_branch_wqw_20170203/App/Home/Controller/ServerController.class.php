<?php
/**
 * Created by messhair.
 * Date: 2016/12/12
 */
namespace Home\Controller;
use Think\Controller\HproseController;

class ServerController extends HproseController{
    public function receiveFile(){
        echo 'received file';
    }
}