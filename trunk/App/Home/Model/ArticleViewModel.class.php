<?php
/**
 * Created by messhair.
 * Date: 2017/1/14
 */
namespace Home\Model;

use Think\Model\ViewModel;

class ArticleViewModel extends ViewModel{
    public $viewFields = array(
        'article'=>array('aid','title','content','uid'),
        'user'=>array('username','_on'=>'article.uid=user.uid'),
    );
}