<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"F:\myproject\webcall\public/../app/task\view\task_tip\show.html";i:1506159989;}*/ ?>
<?php if(is_array($tip_list) || $tip_list instanceof \think\Collection || $tip_list instanceof \think\Paginator): $i = 0; $__LIST__ = $tip_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
    <ul>
        <li><?php echo time_format($vo["tip_time"],"m.d"); ?>&nbsp;<?php echo time_format($vo["tip_time"],"i:s"); ?></li>
        <li class="orange">
            <?php if($vo['tip_employee'] == $uid): ?>
                你
            <?php else: ?>
                <?php echo $vo["truename"]; endif; ?>
        </li>
        <li>打赏<span><?php echo $vo["tip_money"]; ?></span>元</li>
    </ul>
<?php endforeach; endif; else: echo "" ;endif; ?>