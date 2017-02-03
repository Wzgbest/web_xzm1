<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        echo '/home/index';
    }

    public function article() {
        $Dao = D('ArticleView'); // 实例化视图
        $article_list = $Dao->select();
        print_r($article_list);
        echo '<br /><br />';
        // 打印出执行的 SQL 语句
        echo '执行的 SQL 语句为：'.$Dao->getLastSql();
    }
}