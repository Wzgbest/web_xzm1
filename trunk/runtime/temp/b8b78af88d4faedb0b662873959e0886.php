<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"F:\myproject\webcall\public/../app/task\view\index\pk_details.html";i:1506412314;}*/ ?>
<link rel="stylesheet" href="/task/css/task.css" />

<div class="task task_details">

	<article>
		<div class="dv1">
			<div class="left">
				<img src="/task/img/task.png" class="img1" />
				<div class="border-right">
				<p>发布人</p>
				<div class="name"><?php echo $task_info['truename']; ?>
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
				<p class="theme"><?php echo $task_info['task_name']; ?></p>
				<div>
					<div class="details">
						<div class="len">
							<p class="small">统计周期</p>
							<p class="big"><?php echo date("Y.m.d",$task_info['task_start_time']); ?>-<?php echo date("Y.m.d",$task_info['task_end_time']); ?></p>
						</div>
						<div>
							<p class="small">PK项目</p>
							<p class="big">通话数<?php echo get_target_type_name($task_info['target_type']); ?></p>
						</div>
						<div class="very">
							<p class="small">鉴定人</p>
							<p class="very big"><?php echo $task_info['appraiser_truename']; ?></p>
						</div>
					</div>
					<div class="details">
						<div class="len">
							<p class="small">任务领取截止时间</p>
							<p class="big"><?php echo date("Y.m.d/H:m:s",$task_info['task_take_end_time']); ?></p>
						</div>
						<div>
							<p class="small">PK金额</p>
							<p class="big">
								<?php if($task_info['reward_type'] == '2'): ?><?php echo $task_info['reward_amount']; endif; if($task_info['reward_type'] == '1'): ?><?php echo floor($task_info['reward_amount']/$task_info['partin_count']); endif; ?>
								元
							</p>
						</div>
						<div class="very flo">
							<p class="small">面向群体</p>
							<p class="big"><?php $array=explode(',',$task_info['public_to_truename']); if(count($array)>1){echo $array[0].','.$array[1].'等';}elseif($array){echo $array[0];}else{echo '';} ?>（共<?php echo count($array) ?>人，已参与<?php echo $task_info['partin_count']; ?>人）</p>
							<div class="flotage">
								<?php if(!(empty($array) || (($array instanceof \think\Collection || $array instanceof \think\Paginator ) && $array->isEmpty()))): if(is_array($array) || $array instanceof \think\Collection || $array instanceof \think\Paginator): $i = 0; $__LIST__ = $array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><span><?php echo $v; ?></span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			
			
			
			<div class="right">
        <div class="within">
            <div class="active details">
                <!--<?php if($task_info['status'] == '2'): if(in_array($uid,explode(',',$task_info['public_to_take'])) && !in_array($uid,explode(',',$task_info['take_employees'])) && $now_time < $task_info['task_take_end_time'] && $task_info['is_guess'] == 0): ?>
					<p class="p2 get_reward" data-id="<?php echo $task_info['id']; ?>" task-type="<?php echo $task_info['task_type']; ?>" task-money="<?php echo $task_info['reward_amount']; ?>">领任务</p>
					<?php elseif(in_array($uid,explode(',',$task_info['take_employees']))): ?>
					<p>已参与</p>
					<?php else: ?>
					<p>报名已截止</p>
					<?php endif; if(!in_array($uid,explode(',',$task_info['take_employees']))): ?>
					<p class="p2 guess" task_id="<?php echo $task_info['id']; ?>">猜输赢</p>
					<?php endif; ?>
					<p class="p2 tip" task_id="<?php echo $task_info['id']; ?>">打赏</p>
					<?php endif; ?>-->
			
                <p class="p2">终止任务</p>
                <p class="p2">打赏</p>
				<p class="p2">领取</p>
				<p class="p2">猜输赢</p>
            </div>

            <div class="comment">
                <div>
                    <img src="/task/img/comment.png" class="task_details comment_pk" task_id="<?php echo $task_info['id']; ?>" />
                    <span><?php echo $task_info['like_count']; ?></span>
                </div>
                <div style="text-align: right;">
                    <?php if($task_info['is_like'] == '1'): ?>
                    <img src="/task/img/praise.png" class="add" task_id="<?php echo $task_info['id']; ?>" index_img="2"/>
                    <?php else: ?>
                    <img src="/task/img/zan.png" class="add" task_id="<?php echo $task_info['id']; ?>" index_img="1"/>
                    <?php endif; ?>
                    <span class="yi"><?php echo $task_info['like_count']; ?></span>
                </div>
            </div>
        </div>
    </div>
		</div>
		<div class="dv2">
			<div class="left">
				<div>
					<p class="title">PK排行榜</p>
					<ul class="list_head">
						<li>名次</li>
						<li>姓名</li>
						<li>部门</li>
						<li>通话次数</li>
						<li>猜输赢支持</li>
					</ul>
					<div class="box">
					</div>
					<p class="addition"><span class="red">已参与猜输赢</span>：你的任务已有<span class="orange">5</span>人支持，不要辜负他们的期望哦！</p>
				</div>
			</div>
			<div class="right">
				<div class="hezi">
					<p class="title">打赏详情</p>

				</div>
				<p class="explain">任务已得到奖金
					<span class="orange"><?php echo $all_tip_money; ?></span>元
					<span class="gray">
						<?php if($my_tip_money > '0'): ?>
							（你已打赏<?php echo $my_tip_money; ?>元）
						<?php else: ?>
							（你还未进行打赏）
						<?php endif; ?>
					</span>
				</p>
				<p class="rate tip">
					<?php if($my_tip_money > '0'): ?>
						继续打赏
					<?php else: ?>
						打赏
					<?php endif; ?>
				</p>
				<div class="particulars">
				</div>
			</div>
		</div>
		<div class="dv3">
			<p class="title">评论<span>（<?php echo $task_info['comment_count']; ?>）</span></p>
			<div class="up">
				<!--<input type="text" placeholder="回复刘大宝" />-->
				<textarea placeholder="请输入评论" class="content"></textarea>
				<div class="like">
					<div class="left">
						<img src="/task/img/face.png" />
					</div>
					<div class="right">
						<p data-id="<?php echo $task_info['id']; ?>" now-truename="<?php echo $truename; ?>">评论</p>
						<?php if($task_info['is_like'] == '1'): ?>
						<img src="/task/img/praise.png" class="add" task_id="<?php echo $task_info['id']; ?>" index_img="2"/>
						<?php else: ?>
						<img src="/task/img/zan.png" class="add" task_id="<?php echo $task_info['id']; ?>" index_img="1"/>
						<?php endif; ?>
						<span class="yi"><?php echo $task_info['like_count']; ?></span>
					</div>
				</div>
			</div>
			<div class="down">
				<div class="review">
				</div>
			</div>
		</div>

		<div class="reveal-modal reveal_six guess_ui">
		</div>

		<div class="reveal-modal reveal_seven tip_ui">
		</div>

		<div class="reveal-modal reveal_eight pay_ui pay-pop">
		</div>
	</article>

	<script type="text/javascript" src="/task/js/task.js"></script>
	<script type="text/javascript">
		new task_details("<?php echo $fr; ?>","<?php echo $id; ?>","<?php echo $task_type; ?>");
	</script>