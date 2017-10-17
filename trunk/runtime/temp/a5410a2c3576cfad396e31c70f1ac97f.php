<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:64:"F:\myproject\webcall\public/../app/task\view\index\new_task.html";i:1507864757;}*/ ?>
<link rel="stylesheet" href="/task/css/task.css" />
<link rel="stylesheet" href="/task/css/hsCheckData.css" />
<div class="task">
	<form class="new_task_form" onsubmit="return false;">
		<article>
			<div class="dv4">
				<div class="parcel">
					<div class="left hezi">
						<img src="/task/img/star.png" />
						<span>任务类型</span>
						<select name="task_type">
							<option value="1">激励任务</option>
							<option value="2">PK任务</option>
							<option value="3">悬赏任务</option>
						</select>
					</div>
					<div class="right hezi">
						<img src="/task/img/star.png" />
						<span>任务名称</span>
						<input type="text" name="task_name" placeholder="最佳通话能手" />
					</div>

				</div>
				<div class="parcel">
					<div class="left hezi">
						<img src="/task/img/star.png" style="margin-top: -20px;"/>
						<span style="vertical-align: top;">面向群体</span>
						<div id="new_take" class="public_to_take" style="width:225px;padding-right:10px;overflow: hidden;padding-left: 20px;" >
						</div>
					</div>
					<div class="right hezi">
						<img src="/task/img/star.png" />
						<span class="b">激励项目</span>
						<select name="target_type">
							<option value="1">有效通话数</option>
							<option value="2">商机数</option>
							<option value="3">成交额</option>
							<option value="4">成单数</option>
							<option value="5">拜访数</option>
							<option value="6">新增客户数</option>
						</select>
					</div>
				</div>
				<div class="xuanze">
					<div class="choice">
						<span>奖励方式</span>
						<input type="radio" name="task_method" value="1" checked="checked" index="0"/><span>按达标次序<span class="small">（统计周期时间内，达标的员工获得即时奖励）</span></span>
					</div>
					<div class="choice retract">
						<input type="radio" name="task_method" value="2" index="1"/><span>按达标结果<span class="small">（任务结束，所有达标员工平均分配奖金）</span></span>
					</div>
					<div class="choice retract">
						<input type="radio" name="task_method" value="3" index="2"/><span>按周期排名<span class="small">（任务结束后，按完成任务量最终排名发放奖金）</span></span>
					</div>
				</div>
				
				<div class="tab">
					<div class="parcel">
						<div class="len">
							<img src="/task/img/star.png" />
							<span>达标要求</span>
							<span class="set">设置最低有效通话数</span>
							<input type="text" name="target_num" placeholder="0" />
							<span>个</span>
						</div>
					</div>
				</div>
				<div class="tab1" style="display: block;">

					<div class="parcel">
						<div class="len">
							<img src="/task/img/star.png" />
							<span>分配规则</span>
							<span class="set">第</span>
							<input type="text" placeholder="1" class="num1" value="1"/>
							<span class="set">到</span>
							<input type="text" placeholder="5" class="num2" value=""/>
							<span>名</span>
							<span class="set">奖金</span>
							<input type="text" placeholder="100" class="num3" value=""/>
							<span class="set">元</span>
							<img src="/task/img/add.png" class="add" />
							<i class="fa fa-check hide item_btn item_check edit_item_check"></i>
							<i class="fa fa-remove hide item_btn item_remove edit_item_remove"></i>
						</div>
					</div>

					<ul>
						<!--<li>第<span>1</span>~<span>3</span>名，各奖励<span>300</span>元<i class="fa fa-edit"></i><i class="fa fa-trash-o"></i></li>
                        <li>第<span>4</span>~<span>6</span>名，各奖励<span>100</span>元<i class="fa fa-edit"></i><i class="fa fa-trash-o"></i></li>
                        <li>第<span>7</span>~<span>15</span>名，各奖励<span>50</span>元<i class="fa fa-edit"></i><i class="fa fa-trash-o"></i></li>-->
						<li>奖励前<span class="largest">0</span>名，将支付<span class="total">0</span>元作为激励奖金</li>
					</ul>
				</div>
				<div class="tab2">
					<div class="parcel">
						<div class=" hezi">
							<img src="/task/img/star.png" />
							<span>奖金总额</span>
							<input type="text" name="reward_amount" placeholder="0" />
							<span>元</span>
						</div>
					</div>
				</div>
				
				<div class="tab">

					<div class="parcel">
						<div class="normal">
							<img src="/task/img/star.png" />
							<span>统计周期</span>
							<input type="text" value="" maxlength="100" name="task_start_time" value="<?php echo $now_time; ?>" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')" placeholder="请填写日期" />
							<span class="set">到</span>
							<input type="text" value="" maxlength="100" name="task_end_time" value="<?php echo $now_time; ?>" onclick="SelectDate(this,'yyyy-MM-dd hh:mm:ss')" placeholder="请填写日期" />
						</div>
					</div>
					<div class="last">
						<span>任务说明</span>
						<textarea placeholder="请输入任务说明内容" name="content"></textarea>
					</div>
				</div>

				<div class="issue">
					<p class="new_task_submit">发布</p>
					<p class="new_task_cancel">取消</p>
				</div>
			</div>
		</article>
	</form>

	<div class="reveal-modal reveal_eight pay_ui pay-pop">
	</div>

	<script src="/static/js/PopUp.js"></script>
	<script type="text/javascript" src="/task/js/task.js"></script>
	<script src="/task/index/employee_data"></script>
	<script src="/task/js/hsCheckData.js"></script>
	<script src="/task/js/adddate.js"></script>
	<script>
		$('#new_take').hsCheckData({
			isShowCheckBox: true,
			data: cityData
		});
		new new_task_form("<?php echo $fr; ?>");
	</script>