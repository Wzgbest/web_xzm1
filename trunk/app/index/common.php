<?php
/**
 * Created by messhair
 * Date: 17-2-22
 */
use think\Db;

Db::listen( function ($sql, $time, $explain) {
    echo $sql.' [execute time '.$time.'s]';
//    dump($explain);
});
