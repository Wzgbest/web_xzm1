<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:79:"F:\myproject\webcall\public/../app/task\view\employee_task\historical_task.html";i:1507888548;s:84:"F:\myproject\webcall\public/../app/task\view\employee_task\historical_task_load.html";i:1508138739;}*/ ?>
<link rel="stylesheet" href="/task/css/task.css" />

<div class="task task_list">
    <div class="historical_task_load">
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

    <article id="historical_task">
    <?php if(!(empty($task_list) || (($task_list instanceof \think\Collection || $task_list instanceof \think\Paginator ) && $task_list->isEmpty()))): if(is_array($task_list) || $task_list instanceof \think\Collection || $task_list instanceof \think\Paginator): $i = 0; $__LIST__ = $task_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['task_type'] == '1'): ?>
<div id="tasks" style="overflow-y: auto;">
<div class="dv1 item" task_id="<?php echo $vo['id']; ?>">
    <?php if($vo['is_token'] == '0'): ?>
    <div class="mengceng m_c" hongbao_id="<?php echo $vo['redid']; ?>"><?php endif; ?>
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
                    <div>
                        <p class="small">考核项目</p>
                        <p class="big"><?php echo get_target_type_name($vo['target_type']); ?></p>
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
                    <div class="len">
                        <p class="small">奖金规则</p>
                        <p class="big">任务结束后，按完成任务量的最终排名</p>
                    </div>


                </div>
                <div class="details">
                    <div class="len">
                        <p class="small">统计周期</p>
                        <p class="big"><?php echo date("Y.m.d/H:i",$vo['task_start_time']); ?>-<?php echo date("Y.m.d/H:i",$vo['task_end_time']); ?></p>
                    </div>
                    <div class="very flo">
                        <p class="small">面向群体</p>
                        <p class="very big"><?php $array=explode(',',$vo['public_to_truename']); if(count($array)>1){echo $array[0].','.$array[1].'等';}elseif($array){echo $array[0];}else{echo '';} ?>（共<span class="partin_count"><?php echo count(explode(',',$vo['public_to_take'])) ?></span>人参与）</p>
                        <div class="flotage">
                            <?php if(!(empty($vo['public_to_take_array']) || (($vo['public_to_take_array'] instanceof \think\Collection || $vo['public_to_take_array'] instanceof \think\Paginator ) && $vo['public_to_take_array']->isEmpty()))): if(is_array($vo['public_to_take_array']) || $vo['public_to_take_array'] instanceof \think\Collection || $vo['public_to_take_array'] instanceof \think\Paginator): if( count($vo['public_to_take_array'])==0 ) : echo "" ;else: foreach($vo['public_to_take_array'] as $k=>$v): ?><span class="user_<?php echo $k; if(in_array(($k), is_array($vo['take_employees'])?$vo['take_employees']:explode(',',$vo['take_employees']))): ?> color-blue<?php endif; ?>"><?php echo $v; ?></span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <div class="right">
        <div class="within">
            <div class="give details">
                <!--<p class="p3">任务失败</p>-->
                <p class="p2 task_details" task_id="<?php echo $vo['id']; ?>">查看详情</p>
            </div>
            <div class="comment">
                <div>
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
    <?php if($vo['is_token'] == '0'): ?>
    <img src="/task/img/redPacket.png" class="picture"/>
    </div><?php endif; ?>
</div>
<?php endif; if($vo['task_type'] == '2'): ?>
<div class="dv1 a item" task_id="<?php echo $vo['id']; ?>">
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
                    <div>
                        <p class="small">PK项目</p>
                        <p class="big"><?php echo get_target_type_name($vo['target_type']); ?></p>
                    </div>
                    <div>
                        <p class="small">PK金额</p>
                        <p class="big">
                            <?php if($vo['reward_type'] == '2'): ?><?php echo $vo['reward_amount']; endif; if($vo['reward_type'] == '1'): ?><?php echo floor($vo['reward_amount']/$vo['partin_count']); endif; ?>

                            元
                        </p>
                    </div>
                    <div class="len">
                        <p class="small">任务领取截止时间</p>
                        <p class="big"><?php echo date("Y.m.d/H:i",$vo['task_take_end_time']); ?></p>
                    </div>


                </div>
                <div class="details">

                    <div class="len">
                        <p class="small">统计周期</p>
                        <p class="big"><?php echo date("Y.m.d/H:i",$vo['task_start_time']); ?>-<?php echo date("Y.m.d/H:i",$vo['task_end_time']); ?></p>
                    </div>
                    <div class="very flo">
                        <p class="small">面向群体</p>
                        <p class="very big"><?php $array=explode(',',$vo['public_to_truename']); if(count($array)>1){echo $array[0].','.$array[1].'等';}elseif($array){echo $array[0];}else{echo '';} ?>（共<?php echo count(explode(',',$vo['public_to_take'])) ?>人，已参与<span class="partin_count"><?php echo $vo['partin_count']; ?></span>人）</p>
                        <div class="flotage">
                            <?php if(!(empty($vo['public_to_take_array']) || (($vo['public_to_take_array'] instanceof \think\Collection || $vo['public_to_take_array'] instanceof \think\Paginator ) && $vo['public_to_take_array']->isEmpty()))): if(is_array($vo['public_to_take_array']) || $vo['public_to_take_array'] instanceof \think\Collection || $vo['public_to_take_array'] instanceof \think\Paginator): if( count($vo['public_to_take_array'])==0 ) : echo "" ;else: foreach($vo['public_to_take_array'] as $k=>$v): ?><span class="user_<?php echo $k; if(in_array(($k), is_array($vo['take_employees'])?$vo['take_employees']:explode(',',$vo['take_employees']))): ?> color-blue<?php endif; ?>"><?php echo $v; ?></span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="right">
        <div class="within">
            <div class="give details">
                <p class="p2 task_details" task_id="<?php echo $vo['id']; ?>">查看详情</p>
            </div>
            <div class="comment">
                <div>
                    <img src="/task/img/comment.png" class="task_details a comment_pk" task_id="<?php echo $vo['id']; ?>" />
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
<div class="dv1 item" task_id="<?php echo $vo['id']; ?>">
    <?php if($vo['is_token'] == '0'): ?>
    <div class="mengceng m_c" hongbao_id="<?php echo $vo['redid']; ?>"><?php endif; ?>
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
                        <?php if($vo['target_method'] == '1'): ?>
                        <p class="small">其他需求</p>
                        <p class="very big"><?php echo $vo['target_description']; ?></p>
                        <?php else: ?>
                        <p class="small">帮跟客户</p>
                        <p class="very big blue"><?php echo $vo['customer_name']; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="very">
                        <p class="small">任务领取截止时间</p>
                        <p class="big"><?php echo date("Y.m.d/H:i",$vo['task_take_end_time']); ?></p>
                    </div>
                </div>
                <div class="details">
                    <div class="len">
                        <p class="small">任务周期</p>
                        <p class="big"><?php echo date("Y.m.d/H:i",$vo['task_start_time']); ?>-<?php echo date("Y.m.d/H:i",$vo['task_end_time']); ?></p>
                    </div>
                    <div class="very flo">
                        <p class="small">面向群体</p>
                        <p class="very big"><?php $array=explode(',',$vo['public_to_truename']); if(count($array)>1){echo $array[0].','.$array[1].'等';}elseif($array){echo $array[0];}else{echo '';} ?>（共<?php echo count(explode(',',$vo['public_to_take'])) ?>人，已参与<span class="partin_count"><?php echo $vo['partin_count']; ?></span>人）</p>
                        <div class="flotage">
                            <?php if(!(empty($vo['public_to_take_array']) || (($vo['public_to_take_array'] instanceof \think\Collection || $vo['public_to_take_array'] instanceof \think\Paginator ) && $vo['public_to_take_array']->isEmpty()))): if(is_array($vo['public_to_take_array']) || $vo['public_to_take_array'] instanceof \think\Collection || $vo['public_to_take_array'] instanceof \think\Paginator): if( count($vo['public_to_take_array'])==0 ) : echo "" ;else: foreach($vo['public_to_take_array'] as $k=>$v): ?><span class="user_<?php echo $k; if(in_array(($k), is_array($vo['take_employees'])?$vo['take_employees']:explode(',',$vo['take_employees']))): ?> color-blue<?php endif; ?>"><?php echo $v; ?></span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <div class="right">
        <div class="within">
            <div class="give details">
                <p class="p2 task_details" task_id="<?php echo $vo['id']; ?>">查看详情</p>
            </div>
            <div class="comment">
                <div>
                    <img src="/task/img/comment.png"  class="task_details comment_reward" task_id="<?php echo $vo['id']; ?>" />
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
<div id="navigation">
        <a href="loadmore?page=2" ></a>
</div>
</div>
<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>

<script type="text/javascript" src="/task/js/jquery.infinitescroll.js" ></script>

<script>
	$("#tasks").infinitescroll({
    navSelector : "#navigation", //页面分页元素--成功后自动隐藏
    nextSelector : "#navigation a",
    itemSelector : ".item",
    animate : true,
    debug : false, //调试的时候，可以打开
    maxPage : 100, //加载次数
    extraScrollPx : 200,
    loading: {
        msgText:"",
        finished: function(){
            //加载完成后执行    
        },
         finishedMsg: '',//最后加载完成后的提示语 
    },
    behavior: 'local',
    binder: $('#tasks')
});

$('#tasks').infinitescroll({
          loading: ,
          state: {
            isDuringAjax: false,
            isInvalidPage: false,
            isDestroyed: false,
            isDone: false, // For when it goes all the way through the archive.
            isPaused: false,
            currPage: 1
          },
          behavior: undefined,
          binder: $(window), // 在哪个元素内滚动
          nextSelector: "div.navigation a:first",
          navSelector: "div.navigation",
          contentSelector: null, // rename to pageFragment
          extraScrollPx: 150,
          itemSelector: "div.post",
          animate: false,//加载完毕是否采用动态效果
          pathParse: undefined,
          dataType: 'html',
          appendCallback: true,
          bufferPx: 40,
          errorCallback: function () { },
          infid: 0, //Instance ID
          pixelsFromNavToBottom: undefined,
          path: undefined, // Can either be an array of URL parts (e.g. ["/page/", "/"]) or a function that accepts the page number and returns a URL
          maxPage:undefined // to manually control maximum page (when maxPage is undefined, maximum page limitation is not work)
    });


</script>
    </article>
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
    new task_list("historical-taskfr",'<?php echo $uid; ?>');
</script>