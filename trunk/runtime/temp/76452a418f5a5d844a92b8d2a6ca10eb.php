<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"F:\myproject\webcall\public/../app/task\view\index\get_ranking_page.html";i:1507714548;}*/ ?>
<?php if($task_type == '1'): if(is_array($rankingdata) || $rankingdata instanceof \think\Collection || $rankingdata instanceof \think\Paginator): $key = 0; $__LIST__ = $rankingdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?>
        <ul class="<?php if($self_idx == $key-1): ?>backdrop<?php endif; ?>">
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
    <?php endforeach; endif; else: echo "" ;endif; endif; if($task_type == '2'): if(is_array($rankingdata) || $rankingdata instanceof \think\Collection || $rankingdata instanceof \think\Paginator): $key = 0; $__LIST__ = $rankingdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?>
        <ul class="<?php if($self_idx == $key-1): ?>backdrop<?php endif; ?>">
            <li><?php echo $key; ?></li>
            <li><?php echo $vo["truename"]; ?></li>
            <li>销售一部</li>
            <li><span><?php echo $vo["num"]; ?></span>次</li>
            <li><span><?php echo $vo["guess_num"]; ?></span>人支持<span><?php echo $vo["guess_money"]; ?></span>元</li>
        </ul>
    <?php endforeach; endif; else: echo "" ;endif; endif; if($task_type == '3'): if(is_array($rankingdata) || $rankingdata instanceof \think\Collection || $rankingdata instanceof \think\Paginator): $key = 0; $__LIST__ = $rankingdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?>
        <ul class="<?php if($self_idx == $key-1): ?>backdrop<?php endif; ?>">
	        <li><?php echo $key; ?></li>
	        <li><?php echo $vo["truename"]; ?></li>
	        <li>销售一部</li>
	        <li>
	            <?php if(is_array($vo['red_info'])): if($vo['untook'] == '1'): ?>
	            <span class="turn">待领取</span><img src="/task/img/small.png"/>
	            <?php else: ?>
	            <span class="turn">已领取</span>
	            <?php endif; else: ?>
	            <span class="turn">未获得</span>
	            <?php endif; ?>
	        </li>
	        <?php if($vo['whether_help'] == 0 && $create_employee==$uid): ?>
	        <li data-id="<?php echo $vo['take_id']; ?>"><a class="unhelp"><span>未帮</span></a>|<a class="help"><span>帮了</span></a></li>
	        <?php else: ?>
	        <li>
	            <span><?php if($vo['whether_help'] == '0'): ?>正在参与任务<?php endif; ?></span>
	            <span><?php if($vo['whether_help'] == '1'): ?>判定已帮<?php endif; ?></span>
	            <span><?php if($vo['whether_help'] == '-1'): ?>判定未帮<?php endif; ?></span>
	        </li>
	        <?php endif; ?>
	    </ul>
    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
<input type="hidden" class="self_idx" value="<?php echo $self_idx; ?>" />