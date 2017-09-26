<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"F:\myproject\webcall\public/../app/task\view\index\get_ranking_page.html";i:1506159991;}*/ ?>
<?php if($task_type == '1'): if($self_idx >= '0'): ?>
        <ul class="backdrop">
            <li><?php echo $self_idx+1; ?></li>
            <li><?php echo $rankingdata[$self_idx]["truename"]; ?></li>
            <li>销售一部</li>
            <li><span><?php echo $rankingdata[$self_idx]["num"]; ?></span>次</li>
            <li>
                <?php if(is_array($rankingdata[$self_idx]['red_info'])): if($rankingdata[$self_idx]['untook'] == '1'): ?>
                        <span class="turn">待领取</span><img src="/task/img/small.png"/>
                    <?php else: ?>
                        <span class="turn">已领取</span>
                    <?php endif; else: ?>
                    <span class="turn">未获得</span>
                <?php endif; ?>
            </li>
        </ul>
    <?php endif; if(is_array($rankingdata) || $rankingdata instanceof \think\Collection || $rankingdata instanceof \think\Paginator): $key = 0; $__LIST__ = $rankingdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;if($self_idx != $key-1): ?>
            <ul>
                <li><?php echo $key; ?></li>
                <li><?php echo $vo["truename"]; ?></li>
                <li>销售一部</li>
                <li><span><?php echo $vo["num"]; ?></span>次</li>
                <li>
                    <?php if(is_array($vo['red_info'])): if($vo['untook'] == '1'): ?>
                            <span class="turn">待领取</span><img src="/task/img/small.png"/>
                        <?php else: ?>
                            <span class="turn">已领取</span>
                        <?php endif; else: ?>
                        <span class="turn">未获得</span>
                    <?php endif; ?>
                </li>
            </ul>
        <?php endif; endforeach; endif; else: echo "" ;endif; endif; if($task_type == '2'): if($self_idx >= '0'): ?>
        <ul class="backdrop">
            <li><?php echo $self_idx+1; ?></li>
            <li><?php echo $rankingdata[$self_idx]["truename"]; ?></li>
            <li>销售一部</li>
            <li><span><?php echo $rankingdata[$self_idx]["num"]; ?></span>次</li>
            <li><span><?php echo $rankingdata[$self_idx]["guess_num"]; ?></span>人支持<span><?php echo $rankingdata[$self_idx]["guess_money"]; ?></span>元</li>
        </ul>
    <?php endif; if(is_array($rankingdata) || $rankingdata instanceof \think\Collection || $rankingdata instanceof \think\Paginator): $key = 0; $__LIST__ = $rankingdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;if($self_idx != $key-1): ?>
            <ul>
                <li><?php echo $key; ?></li>
                <li><?php echo $vo["truename"]; ?></li>
                <li>销售一部</li>
                <li><span><?php echo $vo["num"]; ?></span>次</li>
                <li><span><?php echo $vo["guess_num"]; ?></span>人支持<span><?php echo $vo["guess_money"]; ?></li>
            </ul>
        <?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>