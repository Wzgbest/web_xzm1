<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:84:"F:\myproject\webcall\public/../app/task\view\employee_task\direct_participation.html";i:1506159989;s:89:"F:\myproject\webcall\public/../app/task\view\employee_task\direct_participation_load.html";i:1506159989;}*/ ?>
<link rel="stylesheet" href="/task/css/task.css" />

<div class="task task_list">
    <div class="direct_participation_load">
        <header>
            <ul class="nav">
                <li class="flow" data-id="1">
                    <div>直接参与（<span><?php echo $task_count['1']; ?></span>）</div>
                </li>
                <li data-id="2">
                    <div>间接参与（<span><?php echo $task_count['2']; ?></span>）</div>
                </li>
                <li data-id="3">
                    <div>我发起的（<span><?php echo $task_count['3']; ?></span>）</div>
                </li>
            </ul>
            <div class="sort">
                <img src="/task/img/sort.png" />
                <p>排序</p>
                <div class="classify">
                    <p data-id="create_time">按时间</p>
                    <p data-id="reward_amount">按奖金</p>
                    <p data-id="like_count">按热度</p>
                </div>
            </div>
            <div class="xinjian">
                <i class="fa fa-plus fa-2x"></i>
                <p>新建任务</p>
            </div>
        </header>

        <article id="direct_participation">
            
<?php if(!(empty($task_list) || (($task_list instanceof \think\Collection || $task_list instanceof \think\Paginator ) && $task_list->isEmpty()))): if(is_array($task_list) || $task_list instanceof \think\Collection || $task_list instanceof \think\Paginator): $i = 0; $__LIST__ = $task_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['task_type'] == '1'): ?>
<div class="dv1">
    <div class="left">
        <img src="/task/img/incentive_task.png" class="img1" />
        <div class="border-right">
            <p>发布人</p>
            <div class="name"><?php echo $vo['truename']; ?>
                <div class="roll">
                    <img src="/task/img/phone.png" />
                    <img src="/task/img/note.png" />
                </div>
            </div>
            <p>￥100</p>
            <p>预计奖金</p>
        </div>
    </div>
    <div class="center">
        <p class="theme"><?php echo $vo['task_name']; ?></p>
        <div>
            <div class="details">
                <div class="len">
                    <p class="small">统计周期</p>
                    <p class="big"><?php echo date("Y.m.d",$vo['task_start_time']); ?>-<?php echo date("Y.m.d",$vo['task_end_time']); ?></p>
                </div>
                <div>
                    <p class="small">考核项目</p>
                    <p class="big"><?php echo get_target_type_name($vo['target_type']); ?></p>
                </div>
                <div>
                    <p class="small">达标要求</p>
                    <p class="big">前<?php echo $vo['reward_end']; ?>名</p>
                </div>
                <div class="very  flo">
                    <p class="small">面向群体</p>
                    <p class="very big"><?php $array=explode(',',$vo['public_to_truename']); if(count($array)>1){echo $array[0].','.$array[1].'等';}elseif($array){echo $array[0];}else{echo '';} ?>（共<?php echo count($array) ?>人，已参与<?php echo $vo['partin_count']; ?>人）</p>
                    <div class="flotage">
                        <?php if(is_array($array) || $array instanceof \think\Collection || $array instanceof \think\Paginator): $i = 0; $__LIST__ = $array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><span><?php echo $v; ?></span><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                </div>
            </div>
            <div class="details">
                <div class="len">
                    <p class="small">任务领取截止时间</p>
                    <p class="big"><?php echo date("Y.m.d/H:m:s",$vo['task_take_end_time']); ?></p>
                </div>
                <div class="fu">
                    <p class="small">奖金</p>
                    <p class="big"><?php echo $vo['re_amount']; ?>元/人</p>
                    <?php $rewardArray=explode(',',$vo['ranking']); ?>
                    <div class="xuanfu">
                        <?php if(is_array($rewardArray) || $rewardArray instanceof \think\Collection || $rewardArray instanceof \think\Paginator): $i = 0; $__LIST__ = $rewardArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?>
                        <P><span><?php echo $v1; ?></span></P>
                        <?php endforeach; endif; else: echo "" ;endif; ?>

                    </div>
                </div>
                <div>
                    <p class="small">当前名次</p>
                    <p class="big">第26名</p>

                </div>
                <div class="very">
                    <p class="small">当前达标人数</p>
                    <div class="big ouside">
                        <div class="inside"></div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div class="right">
        <div class="within">
            <div class="give">
                <p class="p2">终止任务</p>
                <p class="p2 jili">查看详情</p>
                <p class="p2 tip" task_id="<?php echo $vo['id']; ?>">打赏</p>
            </div>
            <div class="comment">
                <div class="comment_incentive">
                    <!--<a href="incentive_details.html"><img src="/task/img/comment.png" /></a>-->
                    <img src="/task/img/comment.png" class="task_details comment_incentive" task_id="<?php echo $vo['id']; ?>" />
                    <span><?php echo $vo['comment_count']; ?></span>
                </div>
                <div style="text-align: right;">
                    <?php if($vo['is_like'] == '1'): ?>
                    <img src="/task/img/praise.png" class="add" task_id="<?php echo $vo['id']; ?>" index_img="2"/>
                    <?php else: ?>
                    <img src="/task/img/zan.png" class="add" task_id="<?php echo $vo['id']; ?>" index_img="1"/>
                    <?php endif; ?>
                    <span class="yi"><?php echo $vo['like_count']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; if($vo['task_type'] == '2'): ?>
<div class="dv1">
    <?php if($vo['is_token'] == '0'): ?>
    <div class="mengceng m_c" hongbao_id="<?php echo $vo['redid']; ?>"><?php endif; ?>
        <div class="left">
            <img src="/task/img/task.png" class="img1" />
            <div class="border-right">
                <p>发布人</p>
                <div class="name"><?php echo $vo['truename']; ?>
                    <div class="roll">
                        <img src="/task/img/phone.png" />
                        <img src="/task/img/note.png" />
                    </div>
                </div>
                <p>￥100</p>
                <p>预计奖金</p>
            </div>
        </div>
        <div class="center">
            <p class="theme"><?php echo $vo['task_name']; ?></p>
            <div>
                <div class="details">
                    <div class="len">
                        <p class="small">统计周期</p>
                        <p class="big"><?php echo date("Y.m.d",$vo['task_start_time']); ?>-<?php echo date("Y.m.d",$vo['task_end_time']); ?></p>
                    </div>
                    <div>
                        <p class="small">PK项目</p>
                        <p class="big"><?php echo get_target_type_name($vo['target_type']); ?></p>
                    </div>
                    <div class="very">
                        <p class="small">鉴定人</p>
                        <p class="very big"><?php echo $vo['appraiser_truename']; ?></p>
                    </div>
                </div>
                <div class="details">
                    <div class="len">
                        <p class="small">任务领取截止时间</p>
                        <p class="big"><?php echo date("Y.m.d/H:m:s",$vo['task_take_end_time']); ?></p>
                    </div>
                    <div>
                        <p class="small">PK金额</p>
                        <p class="big">
                            <?php if($vo['reward_type'] == '2'): ?><?php echo $vo['reward_amount']; endif; if($vo['reward_type'] == '1'): ?><?php echo floor($vo['reward_amount']/$vo['partin_count']); endif; ?>
                            元
                        </p>
                    </div>
                    <div class="very flo">
                        <p class="small">面向群体</p>
                        <p class="very big"><?php $array=explode(',',$vo['public_to_truename']); if(count($array)>1){echo $array[0].','.$array[1].'等';}elseif($array){echo $array[0];}else{echo '';} ?>（共<?php echo count($array) ?>人，已参与<?php echo $vo['partin_count']; ?>人）</p>
                        <div class="flotage">
                            <?php if(!(empty($array) || (($array instanceof \think\Collection || $array instanceof \think\Paginator ) && $array->isEmpty()))): if(is_array($array) || $array instanceof \think\Collection || $array instanceof \think\Paginator): $i = 0; $__LIST__ = $array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><span><?php echo $v; ?></span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="within">
                <div class="give grade details">
                    <p class="p2 task_details" task_id="<?php echo $vo['id']; ?>">查看排名</p>
                    <?php if(!in_array($uid,explode(',',$vo['take_employees']))): ?>
                    <p class="p2 guess" task_id="<?php echo $vo['id']; ?>">猜输赢</p>
                    <?php endif; ?>
                    <p class="p2 tip" task_id="<?php echo $vo['id']; ?>">打赏</p>
                </div>

                <div class="comment">
                    <input type="hidden" value="1" />
                    <div class="comment_pk">
                        <img src="/task/img/comment.png" class="task_details xiangqing comment_pk" task_id="<?php echo $vo['id']; ?>" />
                        <span><?php echo $vo['comment_count']; ?></span>
                    </div>
                    <div style="text-align: right;">
                        <?php if($vo['is_like'] == '1'): ?>
                        <img src="/task/img/praise.png" class="add" task_id="<?php echo $vo['id']; ?>" index_img="2"/>
                        <?php else: ?>
                        <img src="/task/img/zan.png" class="add" task_id="<?php echo $vo['id']; ?>" index_img="1"/>
                        <?php endif; ?>
                        <span class="yi"><?php echo $vo['like_count']; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php if($vo['is_token'] == '0'): ?>
        <img src="/task/img/redPacket.png" class="picture"/>
        </div><?php endif; ?>
</div>
<?php endif; if($vo['task_type'] == '3'): ?>
<div class="dv1">
    <div class="left">
        <img src="/task/img/reward_task.png" class="img1" />
        <div class="border-right">
            <p>发布人</p>
            <div class="name"><?php echo $vo['truename']; ?>
                <div class="roll">
                    <img src="/task/img/phone.png" />
                    <img src="/task/img/note.png" />
                </div>
            </div>
            <p>￥100</p>
            <p>预计奖金</p>
        </div>
    </div>
    <div class="center">
        <p class="theme"><?php echo $vo['task_name']; ?></p>
        <div>
            <div class="details">
                <div class="len">
                    <p class="small">任务领取截止时间</p>
                    <p class="big"><?php echo date("Y.m.d/H:m:s",$vo['task_take_end_time']); ?></p>
                </div>

                <div class="very">
                    <p class="small">对应客户</p>
                    <p class="very big  blue"><?php echo $vo['customer_name']; ?></p>
                </div>
            </div>
            <div class="details">
                <div class="len">
                    <p class="small">求助上限人数</p>
                    <p class="big"><?php echo $vo['reward_num']; ?>人</p>
                </div>
                <div class="very">
                    <p class="small">悬赏金</p>
                    <p class="big"><?php echo $vo['reward_amount']; ?>元/人</p>
                </div>

            </div>
        </div>
    </div>
    <div class="right">
        <div class="within">
            <div class="active">
                <p class="p2">终止任务</p>
                <p class="p2 xuanshang">查看详情</p>
                <p class="p2 tip" task_id="<?php echo $vo['id']; ?>">打赏</p>
            </div>
            <div class="comment">
                <div class="comment_reward">
                    <img src="/task/img/comment.png" class="task_details comment_reward" task_id="<?php echo $vo['id']; ?>" />
                    <span><?php echo $vo['comment_count']; ?></span>
                </div>
                <div style="text-align: right;">
                    <?php if($vo['is_like'] == '1'): ?>
                    <img src="/task/img/praise.png" class="add" task_id="<?php echo $vo['id']; ?>" index_img="2"/>
                    <?php else: ?>
                    <img src="/task/img/zan.png" class="add" task_id="<?php echo $vo['id']; ?>" index_img="1"/>
                    <?php endif; ?>
                    <span class="yi"><?php echo $vo['like_count']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
        </article>

        <div class="reveal-modal reveal_six guess_ui">
        </div>

        <div class="reveal-modal reveal_seven tip_ui">
        </div>

        <div class="reveal-modal reveal_eight pay_ui pay-pop">
        </div>
    </div>
</div>
<div class="task_info_panel new_task_panel hide">
    <header>
        <div class="top">
            <ul class="firNav">
                <li class="current">
                    <div>间接参与（<span>7</span>）</div>
                </li>
                <i class="fa fa-angle-right fa-2x"></i>
                <span class="span-info">新建任务</span>
            </ul>
        </div>
    </header>
    <div class="new_task_info_panel"></div>
</div>
<div class="task_info_panel task_direct_panel hide">
    <header>
        <div class="top">
            <ul class="firNav">
                <li class="current">
                    <div>间接参与（<span>7</span>）</div>
                </li>
                <i class="fa fa-angle-right fa-2x"></i>
                <span class="span-info">任务详情</span>
            </ul>
        </div>
    </header>
    <div class="task_direct_info_panel"></div>
</div>
<script type="text/javascript" src="/task/js/task.js"></script>
<script>
    new task_list("going-taskfr");
</script>