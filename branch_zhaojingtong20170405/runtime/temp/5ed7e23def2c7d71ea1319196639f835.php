<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"F:\myproject\webcall\public/../app/task\view\index\incentive_details.html";i:1507958628;}*/ ?>
<link rel="stylesheet" href="/task/css/task.css" />

<div class="task task_details">
	<article>
		<div class="dv1">
			<div class="left">
				<img src="/task/img/incentive_task.png" class="img1" />
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
						<div>
							<p class="small">考核项目</p>
							<p class="big"><?php echo get_target_type_name($task_info['target_type']); ?></p>
						</div>
						<div class="fu">
							<p class="small">奖金</p>
							<p class="big"><?php echo $task_info['re_amount']; ?>元/人</p>
							<?php $rewardArray=explode(',',$task_info['ranking']); ?>
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
							<p class="big"><?php echo date("Y.m.d/H:i",$task_info['task_start_time']); ?>-<?php echo date("Y.m.d/H:i",$task_info['task_end_time']); ?></p>
						</div>
						<div class="len flo">
							<p class="small">面向群体</p>
							<p class="very big"><?php $array=explode(',',$task_info['public_to_truename']); if(count($array)>1){echo $array[0].','.$array[1].'等';}elseif($array){echo $array[0];}else{echo '';} ?>（共<?php echo count(explode(',',$task_info['public_to_take'])) ?>人参与）</p>
							<div class="flotage">
								<?php if(!(empty($task_info['public_to_take_array']) || (($task_info['public_to_take_array'] instanceof \think\Collection || $task_info['public_to_take_array'] instanceof \think\Paginator ) && $task_info['public_to_take_array']->isEmpty()))): if(is_array($task_info['public_to_take_array']) || $task_info['public_to_take_array'] instanceof \think\Collection || $task_info['public_to_take_array'] instanceof \think\Paginator): if( count($task_info['public_to_take_array'])==0 ) : echo "" ;else: foreach($task_info['public_to_take_array'] as $k=>$v): ?><span class="<?php if(in_array(($k), is_array($task_info['take_employees'])?$task_info['take_employees']:explode(',',$task_info['take_employees']))): ?>color-blue<?php endif; ?>"><?php echo $v; ?></span><?php endforeach; endif; else: echo "" ;endif; endif; ?>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="right">
        <div class="within">
            <div class="active details">
				<?php if($task_info['status'] == '2'): if($now_time <= $task_info['task_end_time']): ?>
				<!--<p class="p2 tip" task_id="<?php echo $task_info['id']; ?>"><?php if(in_array($uid,explode(',',$task_info['tip_employees']))): ?>继续打赏<?php else: ?>打赏<?php endif; ?></p>-->

				<?php if($uid == $task_info['create_employee']): if(strtotime('-1 day') < $task_info['create_time'] && $now_time < $task_info['task_end_time']): ?>
				<p class="p2 end_task" data-id="<?php echo $task_info['id']; ?>">终止任务</p>
				<?php endif; if(!in_array($uid,explode(',',$task_info['take_employees']))): ?>
				<p class="p1">任务进行中</p>
				<?php endif; endif; if(in_array($uid,explode(',',$task_info['take_employees']))): ?>
				<p class="p1">正在参与任务</p>
				<?php endif; endif; endif; if($task_info['is_token'] == '1'): ?>
				<p class="p6">已领<?php echo $task_info['total_money']; ?>元</p>
				<?php endif; if($task_info['status'] == '0'): ?><p class="p3">任务被终止</p><?php endif; if($task_info['status'] == '1'): ?><p class="p4">任务未生效</p><?php endif; if($now_time > $task_info['task_end_time']): ?>
				<p class="p4">任务结束</p>
				<?php endif; ?>
            </div>
            <!--<div class="comment">-->
                <!--<div>-->
                    <!--<img src="/task/img/comment.png" class="task_details comment_incentive" task_id="<?php echo $task_info['id']; ?>" />-->
                    <!--<span><?php echo $task_info['comment_count']; ?></span>-->
                <!--</div>-->
                <!--<div style="text-align: right;">-->
                    <!--<?php if($task_info['is_like'] == '1'): ?>-->
                    <!--<img src="/task/img/praise.png" class="add" task_id="<?php echo $task_info['id']; ?>" index_img="2"/>-->
                    <!--<?php else: ?>-->
                    <!--<img src="/task/img/zan.png" class="add" task_id="<?php echo $task_info['id']; ?>" index_img="1"/>-->
                    <!--<?php endif; ?>-->
                    <!--<span class="yi"><?php echo $task_info['like_count']; ?></span>-->
                <!--</div>-->
            <!--</div>-->
        </div>
    </div>
		</div>
		<div class="dv2 ">
			<div class="left ">
	
				<div>
					<p class="title ">激励排行榜</p>
					<ul class="list_head ">
						<li>名次</li>
						<li>姓名</li>
						<li>部门</li>
						<li><?php echo get_target_type_name($task_info['target_type']); ?></li>
						<li>奖金</li>
					</ul>
					<div class="box ">
					</div>
					<!--<p class="addition "><span class="red ">已领取任务</span>：你已完成任务指标，排名奖金还需超越<span class="orange ">2</span>人才能获得，距离获得奖金还需拨打<span class="orange ">5</span>次电话才有机会！</p>-->
				</div>
		
			</div>

			<div class="right ">
				<div class="hezi ">
					<p class="title ">打赏详情</p>

				</div>
				<p class="explain ">任务已得到奖金
					<span class="orange "><?php echo $all_tip_money; ?></span>元
					<span class="gray ">
						<?php if($my_tip_money > '0 '): ?>
							（你已打赏<span class="my_tip_money"><?php echo $my_tip_money; ?></span>元）
						<?php else: ?>
							（你还未进行打赏）
						<?php endif; ?>
					</span>
				</p>
				<?php if($task_info['status'] == '2'): if($now_time <= $task_info['task_end_time']): ?>
				<p class="rate tip ">
					<?php if($my_tip_money > '0 '): ?>
						继续打赏
					<?php else: ?>
						打赏
					<?php endif; ?>
				</p>
				<?php endif; endif; ?>
				<div class="particulars">
				</div>
			</div>
		</div>
		<div class="dv3 ">
			<p class="title ">评论<span>（<?php echo $task_info['comment_count']; ?>）</span></p>
			<div class="up ">
				<!--<input type="text " placeholder="回复刘大宝 " />-->
				<textarea placeholder="请输入评论 " class="content "></textarea>
				<div class="like ">
					<div class="left ">
						<img src="/task/img/face.png " />
					</div>
					<div class="right ">
						<p data-id="<?php echo $task_info['id']; ?> " now-truename="<?php echo $truename; ?> ">评论</p>
						<?php if($task_info['is_like'] == '1 '): ?>
						<img src="/task/img/praise.png " class="add " task_id="<?php echo $task_info['id']; ?> " index_img="2 "/>
						<?php else: ?>
						<img src="/task/img/zan.png " class="add " task_id="<?php echo $task_info['id']; ?> " index_img="1 "/>
						<?php endif; ?>
						<span class="yi "><?php echo $task_info['like_count']; ?></span>
					</div>
				</div>
			</div>
			<div class="down ">
				<div class="review ">
				</div>
			</div>
		</div>


		<div class="reveal-modal reveal_six guess_ui ">
		</div>

		<div class="reveal-modal reveal_seven tip_ui ">
		</div>

		<div class="reveal-modal reveal_eight pay_ui pay-pop ">
		</div>
	</article>

	<script type="text/javascript " src="/task/js/task.js "></script>
	<script type="text/javascript ">
		new task_details("<?php echo $fr; ?> ","<?php echo $id; ?> ","<?php echo $task_type; ?>","<?php echo $uid; ?>");
	</script>
</div>