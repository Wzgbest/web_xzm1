<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"F:\myproject\webcall\public/../app/task\view\index\reward_details.html";i:1506412527;}*/ ?>
<link rel="stylesheet" href="/task/css/task.css" />

<div class="task task_details">
	<article>
		<div class="dv1">
			<div class="left">
				<img src="/task/img/reward_task.png" class="img1" />
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
				<p class="theme"><?php echo $task_info["task_name"]; ?></p>
				<div>
					<div class="details">
						<div class="len">
							<p class="small">任务领取截止时间</p>
							<p class="big"><?php echo date("Y.m.d/H:m:s",$task_info['task_take_end_time']); ?><?php echo day_format($task_info["task_take_start_time"],'Y.m.d'); ?>/<?php echo day_format($task_info["task_take_end_time"],'Y.m.d'); ?></p>
						</div>

						<div class="very">
							<p class="small">帮跟客户</p>
							<p class="very big  blue"><?php echo $task_info['customer_name']; ?></p>
						</div>
					</div>
					<div class="details">
						<div class="len">
							<p class="small">求助上限人数</p>
							<p class="big"><?php echo $task_info['reward_num']; ?>人</p>
						</div>
						<div class="very">
							<p class="small">悬赏金</p>
							<p class="big"><?php echo $task_info['reward_amount']; ?>元/人</p>
						</div>

					</div>
				</div>
			</div>
			
			
			
			
			<div class="right">
        <div class="within">
            <div class="active details">
                <!--<?php if($task_info['status'] == '2'): if(in_array($uid,explode(',',$task_info['public_to_take'])) && !in_array($uid,explode(',',$task_info['take_employees'])) && $now_time < $task_info['task_take_end_time']): ?>
						<p class="p2 get_reward" data-id="<?php echo $task_info['id']; ?>" task-type="<?php echo $task_info['task_type']; ?>" task-money="<?php echo $task_info['reward_amount']; ?>">领任务</p>
						<?php elseif(in_array($uid,explode(',',$task_info['take_employees']))): ?>
						<p>已参与</p>
						<?php else: ?>
						<p>报名已截止</p>
						<?php endif; ?>
						<p class="p2 tip" task_id="{task_info.id}">打赏</p>
						<?php endif; ?>-->
                <p class="p2">终止任务</p>
                <p class="p2">打赏</p>
                <p class="p2">领取</p>
            </div>
            <div class="comment">
                <div>
                    <img src="/task/img/comment.png" class="task_details comment_reward" task_id="<?php echo $task_info['id']; ?>" />
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
					<p class="title">悬赏任务</p>
					<ul class="list_head">
						<li>序号</li>
						<li>姓名</li>
						<li>部门</li>
						<li>资金</li>
						<li>状态</li>
					</ul>
					<div class="box">
					</div>
					<p class="addition"><span class="red">已领取任务</span>：你已完成任务指标，排名奖金还需超越<span class="orange">2</span>人才能获得，距离获得奖金还需拨打<span class="orange">5</span>次电话才有机会！</p>
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