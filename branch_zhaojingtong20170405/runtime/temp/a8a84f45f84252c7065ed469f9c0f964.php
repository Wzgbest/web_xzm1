<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"F:\myproject\webcall\public/../app/task\view\task_comment\show.html";i:1507789172;}*/ ?>
<?php if(is_array($comment_list) || $comment_list instanceof \think\Collection || $comment_list instanceof \think\Paginator): $i = 0; $__LIST__ = $comment_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
    <div class="one">
        <img src="<?php echo $vo['replyer_pic']; ?>" />
        <div class="comment_right">
            <p>
                <span class="name_1" comment_id="<?php echo $vo['id']; ?>"><?php echo $vo["replyer_name"]; ?></span>
                <?php if($vo['reviewer_id'] > '0'): ?>
                回复 <span class="name"><?php echo $vo["reviewer_name"]; ?></span>
                <?php endif; ?>
                <span>:</span>
                <span><?php echo $vo["reply_content"]; ?></span>
            </p>
            <p class="reply">
                <span><?php echo time_format($vo["comment_time"]); ?></span>
        		<!--<div class="comment"><i class="fa fa-commenting"></i><span>回复</span></div>-->
                
            </p>
        </div>
        <div class="comment"><i class="fa fa-commenting"></i><span>回复</span></div>
    </div>
<?php endforeach; endif; else: echo "" ;endif; ?>