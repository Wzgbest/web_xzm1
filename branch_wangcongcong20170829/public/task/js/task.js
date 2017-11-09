var content = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t = 10;
for(var i = 0; i < t; i++) {
	$("#myModalone .table").append(content);
}

var content2 = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t = 10;
for(var i = 0; i < t; i++) {
	$("#myModaltwo .table").append(content2);
}
//猜输赢弹出框添加ul
var content3 = "<ul class='number2'><li></li><li></li><li></li></ul>";
var t;
t = 2;
for(var i = 0; i < t; i++) {
	$("#myModalsix .list").append(content3);
}

//领红包
//$(".dv1 .grade .get").click(function() {
//	if($(this).hasClass("p1")) {
//		return;
//	}
//
//	$(this).hide();
//	$(".dv1 .mengceng").addClass("m_c");
//	var a = '<img src="/task/img/redPacket.png" class="picture"/>';
//	$(".mengceng").append(a);
//	$('.picture').click(function() {
//
//		$(this).parent().removeClass("m_c")
//		$(this).remove()
//
//		$(".dv1 .grade .get").show()
//		$(".dv1 .grade .get").addClass('p1').removeClass("p2").text("已领取100元")
//
//	})
//
//})
//$(".dv1 .grade .p1").removeClass("get")

//点击span时加.motai
$('.grade .show_ranking_task').click(function() {
	$(this).parent().parent().siblings('.motai').addClass('motai1');
	$(this).parents().addClass("change");
	var revealObj = $(".myModalone").reveal("{data-animation:'fade'}");
	revealObj.setCloseHandle(function() {
		$('.grade .show_ranking_task').parent().parent().siblings('.motai').removeClass('motai1');
		$('.grade .show_ranking_task').parents().removeClass("change");
	});
});
$('.grade .show_ranking_incentive').click(function() {
	$(this).parent().parent().siblings('.motai').addClass('motai1');
	$(this).parents().addClass("change");
	var revealObj = $(".myModaltwo").reveal("{data-animation:'fade'}");
	revealObj.setCloseHandle(function() {
		$('.grade .show_ranking_incentive').parent().parent().siblings('.motai').removeClass('motai1');
		$('.grade .show_ranking_incentive').parents().removeClass("change");
	});
});
$('.grade .show_ranking_reward').click(function() {
	$(this).parent().parent().siblings('.motai').addClass('motai1');
	$(this).parents().addClass("change");
	var revealObj = $(".myModalthree").reveal("{data-animation:'fade'}");
	revealObj.setCloseHandle(function() {
		$('.grade .show_ranking_reward').parent().parent().siblings('.motai').removeClass('motai1');
		$('.grade .show_ranking_reward').parents().removeClass("change");
	});
});

function task_like(id, like, fun) {
	var post_data = "id=" + id;
	if(!like) {
		post_data += "&unlike=1";
	}
	$.ajax({
		url: '/task/employee_task/task_like',
		type: 'post',
		data: post_data,
		dataType: "json",
		success: function(data) {
			if(data.success == 1) {
				fun(data);
			} else {
				layer.msg(data.msg, {
					icon: 2
				});
			}
		},
		error: function() {
			layer.msg('操作出现错误', {
				icon: 2
			})
		}
	});
}

function task_end(id, fun) {
	var post_data = "task_id=" + id;
	$.ajax({
		url: '/task/employee_task/task_end',
		type: 'post',
		data: post_data,
		dataType: "json",
		success: function(data) {
			if(data.status == 1) {
				fun(data);
			} else {
				layer.msg(data.info, {
					icon: 2
				});
			}
		},
		error: function() {
			console.log('操作出现错误', {
				icon: 2
			});
		},
	});
}

function task_tip(id, tip_money, paypassword, callback) {
	$.ajax({
		url: '/task/task_tip/tip/task_id/' + id + '/money/' + tip_money + '/paypassword/' + paypassword,
		type: 'post',
		data: {
			'task_id': id,
			"money": tip_money,
			"paypassword": paypassword
		},
		success: function(data) {
			callback(data);
		},
		error: function() {
			layer.msg('打赏支付出现错误', {
				icon: 2
			});
		}
	});
}

function task_take(id, paypassword, callback) {
	//提交领取任务接口
	$.ajax({
		url: '/task/index/take',
		type: 'post',
		data: {
			'task_id': id,
			"paypassword": paypassword
		},
		success: function(data) {
			callback(data);
		},
		error: function() {
			layer.msg('申请时发生错误!', {
				icon: 2
			});
		}
	});
}

function task_guess(task_id, employee_id, guess_money, paypassword, callback) {
	//提交领取任务接口
	$.ajax({
		url: '/task/task_guess/guess',
		type: 'post',
		data: {
			'task_id': task_id,
			"take_employee_id": employee_id,
			"money": guess_money,
			"paypassword": paypassword
		},
		success: function(data) {
			callback(data);
		},
		error: function() {
			layer.msg('申请时发生错误!', {
				icon: 2
			});
		}
	});
}

function task_add(new_task_form_data, callback) {
	$.ajax({
		url: '/task/index/add',
		type: 'POST',
		dataType: 'json',
		data: new_task_form_data,
		success: function(data) {
			callback(data);
		},
		error: function() {
			layer.msg('打赏失败!', {
				icon: 2
			});
		}
	});
}

function new_task_form(load_table) {
	this.load_table = load_table;
	//  console.log("load_table");
	//  console.log(load_table);
	this.paypassword = '';
	this.reward_list = [];
	this.reward_edit_idx = [];
	var self = this;

	this.get_form_data = function() {
		var form_sel = "#" + self.load_table + " .task .new_task_form";
		var task_name = $(form_sel + " [name='task_name']").val();
		var target_method = $(form_sel + " [name='target_method']:checked").val();

		var target_customer = $(form_sel + " [name='target_customer']").val(); //帮忙跟进的客户
		var target_description = $(form_sel + " [name='target_description']").val(); //其他需求

		var task_start_time = $(form_sel + " [name='task_start_time']").val();
		var task_end_time = $(form_sel + " [name='task_end_time']").val();

		if(task_name == '') {
			layer.msg('请输入任务名称!', {
				icon: 2
			});
			return false;
		}

		if(task_start_time >= task_end_time || !task_start_time || !task_end_time) {
			layer.msg('任务周期错误!', {
				icon: 2
			});
			return false;
		}

		if(target_method == 1) {
			//其他需求
			if(!target_description) {
				layer.msg('请填写其他需求!', {
					icon: 2
				});
				return false;
			}
		} else if(target_method == 2) {
			//帮忙跟进客户
			if(!target_customer) {
				layer.msg('请选择帮忙跟进客户!', {
					icon: 2
				});
				return false;
			}
		}
		//      console.log('$(form_sel)',$(form_sel));
		var new_task_form_data = $(form_sel).serialize();
		//      console.log(new_task_form_data);
		var task_type = $(form_sel + " [name='task_type']").val();
		//      console.log('task_type',task_type);

		var reward_array = [];
		var reward = '';
		var public_to_take = '';
		var public_to_view = '';
		var money = 0;
		if(task_type == 1) {
			var task_method = $(form_sel + " [name='task_method'][checked]").val();
			console.log('task_method', task_method);

			var target_num = $(form_sel + " [name='target_num']").val();
			//          console.log('target_num',target_num);
			if(target_num == '') {
				layer.msg('请输入达标要求!', {
					icon: 2
				});
				return false;
			}

			if(task_method == 1 || task_method == 3) {
				var all_money = 0;
				var $verify_idx = 0;
				for(var idx = 0; idx < self.reward_list.length; idx++) {
					var start_item = self.reward_list[idx]["start"];
					var end_item = self.reward_list[idx]["end"];
					if($verify_idx + 1 != start_item) {
						layer.msg('第' + ($verify_idx + 1) + '名没有分配规则!', {
							icon: 2
						});
						return false;
					}
					$verify_idx = end_item;
					var item_money = self.reward_list[idx]["money"];
					var item_num = end_item - start_item + 1;
					all_money = all_money + item_num * item_money;
					var reward_object = {
						reward_start: start_item,
						reward_end: end_item,
						reward_amount: item_money
					};
					reward_array.push(reward_object);
				}
				money = all_money;
				//              console.log('all_money',all_money);
			} else if(task_method == 2) {
				money = $(form_sel + " [name='reward_amount']").val();
				var reward_object = {
					reward_start: 1,
					reward_end: 1,
					reward_amount: money
				};
				reward_array.push(reward_object);
			}
		} else if(task_type == 2) {
			var task_take_start_time = $(form_sel + " [name='task_take_start_time']").val();
			var task_take_end_time = $(form_sel + " [name='task_take_end_time']").val();
			if(task_take_start_time >= task_take_end_time || !task_take_start_time || !task_take_end_time) {
				layer.msg('任务加入时间错误!', {
					icon: 2
				});
				return false;
			}
			if(task_start_time > task_take_start_time) {
				layer.msg('任务领取开始时间不能小于统计开始时间!', {
					icon: 2
				});
				return false;
			}
			if(task_take_end_time > task_end_time) {
				layer.msg('任务领取结束时间不能大于统计结束时间!', {
					icon: 2
				});
				return false;
			}
			var num = $(form_sel + " [name='reward_num']").val();
			money = $(form_sel + " [name='reward_amount']").val();
			var reward_object = {
				reward_start: 1,
				reward_end: num,
				reward_amount: money
			};
			reward_array.push(reward_object);
		} else if(task_type == 3) {
			var task_take_start_time = $(form_sel + " [name='task_take_start_time']").val();
			var task_take_end_time = $(form_sel + " [name='task_take_end_time']").val();
			if(task_take_start_time >= task_take_end_time || !task_take_start_time || !task_take_end_time) {
				layer.msg('任务加入时间错误!', {
					icon: 2
				});
				return false;
			}
			if(task_start_time > task_take_start_time) {
				layer.msg('任务领取开始时间不能小于统计开始时间!', {
					icon: 2
				});
				return false;
			}
			if(task_take_end_time > task_end_time) {
				layer.msg('任务领取结束时间不能大于统计结束时间!', {
					icon: 2
				});
				return false;
			}
			var num = $(form_sel + " [name='reward_num']").val();
			var amount = $(form_sel + " [name='reward_amount']").val();
			money = num * amount;
			var reward_object = {
				reward_start: 1,
				reward_end: num,
				reward_amount: amount
			};
			reward_array.push(reward_object);
		}
		//console.log('reward_array',reward_array);
		reward = JSON.stringify(reward_array);
		//      console.log('money',money);
		if(money <= 0) {
			layer.msg('奖金必须大于0!', {
				icon: 2
			});
			return false;
		}
		var public_to_take_str = $(form_sel + " .public_to_take").attr("data-id");
		if(public_to_take_str == '') {
			layer.msg('请选择面向群体!', {
				icon: 2
			});
			return false;
		}
		var public_to_take_arr = public_to_take_str.split("-");
		public_to_take = public_to_take_arr.join(",");
		public_to_view = public_to_take;
		new_task_form_data += "&reward=" + reward +
			"&public_to_take=" + public_to_take +
			"&public_to_view=" + public_to_view;
		return {
			"money": money,
			"data": new_task_form_data
		};
	};

	this.add_task = function(paypassword, pay_type) {
		//      console.log("pay_type");
		//      console.log(pay_type);
		if(paypassword == '') {
			layer.msg('请输入密码!', {
				icon: 2
			});
			return false;
		}
		var form_data = self.get_form_data();
		//      console.log(form_data);
		if(form_data === false) {
			return false;
		}
		var new_task_form_data = form_data.data + "&paypassword=" + paypassword + "&pay_type=" + pay_type;
		console.log(new_task_form_data);
		task_add(new_task_form_data, function(data) {
			if(data.status == 1) {
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				var fr = self.load_table;
				var url = "";
				if(fr == "task-hallfr") {
					url = '/task/employee_task/hot_task.html';
				} else if(fr == "going-taskfr") {
					url = '/task/employee_task/direct_participation.html';
				} else if(fr == "historical-taskfr") {
					url = '/task/employee_task/historical_task.html';
				}
				loadPage(url, fr);
			} else {
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
			}
		});
	};

	this.get_pay_password = function() {
		return self.paypassword;
	};

	$("#" + self.load_table + " .dv4 .parcel .hezi select[name='target_type']").change(function() {
		var options = $("#" + self.load_table + " select[name='target_type'] option:selected");
		var val = options.text();
		$("#" + self.load_table + " .new_task_form article .tab .len .set span").text(val);
		if(val == "成交额") {
			$("#" + self.load_table + " .dv4 .parcel .unit").html("元")
		} else {
			$("#" + self.load_table + " .dv4 .parcel .unit").html("个")
		}
	});
	
	

	$("#" + self.load_table + " .dv4 .parcel .hezi select[name='task_type']").change(function() {
		var val = $(this).val();
		      console.log(val);
		//sxp
		let par = $(this).parents(".once").attr("id");
		console.log(par);
		if(val == 1) {
			$.ajax({
				url: '/task/index/new_task/fr/' + self.load_table,
				type: 'get',
				success: function(data) {
					//console.log(data);
					//console.log($("#"+self.load_table+" .new_task_panel"));
					//console.log($("#"+self.load_table+" .new_task_panel .new_task_info_panel"));
					$("#" + self.load_table + " .new_task_panel .new_task_info_panel").html(data);
					$("#" + self.load_table + " .task_list").addClass("hide");
					$("#" + self.load_table + " .new_task_panel").removeClass("hide");
					$("#"+par+" .main-content.select-window").attr("data-selector","#"+par+" .new-task-select-window-choosen-staff");
				},
				error: function() {
					layer.msg('加载任务新建出现错误', {
						icon: 2
					});
				}
			});
			//loadPage('/task/index/new_task/fr/'+self.load_table,self.load_table);
		} else if(val == 2) {
			$.ajax({
				url: '/task/index/PKnew_task/fr/' + self.load_table,
				type: 'get',
				success: function(data) {
					//console.log(data);
					//console.log($("#"+self.load_table+" .new_task_panel"));
					//console.log($("#"+self.load_table+" .new_task_panel .new_task_info_panel"));
					$("#" + self.load_table + " .new_task_panel .new_task_info_panel").html(data);
					$("#" + self.load_table + " .task_list").addClass("hide");
					$("#" + self.load_table + " .new_task_panel").removeClass("hide");
					$("#"+par+" .main-content.select-window").attr("data-selector","#"+par+" .pknew-task-select-window-choosen-staff");
				},
				error: function() {
					layer.msg('加载任务新建出现错误', {
						icon: 2
					});
				}
			});
			//loadPage('/task/index/PKnew_task/fr/'+self.load_table,self.load_table);
		} else if(val == 3) {
			$.ajax({
				url: '/task/index/rewardnew_task/fr/' + self.load_table,
				type: 'get',
				success: function(data) {
					//console.log(data);
					//console.log($("#"+self.load_table+" .new_task_panel"));
					//console.log($("#"+self.load_table+" .new_task_panel .new_task_info_panel"));
					$("#" + self.load_table + " .new_task_panel .new_task_info_panel").html(data);
					$("#" + self.load_table + " .task_list").addClass("hide");
					$("#" + self.load_table + " .new_task_panel").removeClass("hide");
					$("#"+par+" .main-content.select-window").attr("data-selector","#"+par+" .rewardnew-task-select-window-choosen-staff");
				},
				error: function() {
					layer.msg('加载任务新建出现错误', {
						icon: 2
					});
				}
			});
			//loadPage('/task/index/rewardnew_task/fr/'+self.load_table,self.load_table);
		}

	});

	$("#" + self.load_table + " .dv4 .pknew-task-select-window-choosen-staff").focus(function(){
		console.log('pknew:'+$(this).next().attr('class'));
		$(".select-window-container").remove();
		$(this).next().load("/index/index/select_window.html");
//		$("#" + self.load_table +" .main-content.select-window").load("/index/index/select_window.html");
	});
	
	$("#" + self.load_table  + " .dv4 .new-task-select-window-choosen-staff").focus(function(){
		console.log('task:'+$(this).next().attr('class'));
		$(".select-window-container").remove();
		$(this).next().load("/index/index/select_window.html");
//		$("#" + self.load_table +" .main-content.select-window").load("/index/index/select_window.html");
	});
	$("#" + self.load_table + " .dv4 .rewardnew-task-select-window-choosen-staff").focus(function(){
		console.log('rewardnew:'+$(this).next().attr('class'));
		$(".select-window-container").remove();
		$(this).next().load("/index/index/select_window.html");
//		$("#" + self.load_table +" .main-content.select-window").load("/index/index/select_window.html");
	});
	
	
	

	this.add_reward_item = function(start, end, money) {
		//检验
		if(self.reward_list.length>0){
			var last_item = self.reward_list[self.reward_list.length-1];
			if(start<last_item.end){
				return;
			}
		}
		//添加到数组
		var item_temp = {
			start: start,
			end: end,
			money: money
		};
		self.reward_list.push(item_temp);
		console.log("self.reward_list", self.reward_list);
		this.reload_reward_list_html();
		$("#" + self.load_table + " article .num1").text(end + 1);
		$("#" + self.load_table + " article .num2").val('');
		$("#" + self.load_table + " article .num3").val('');
	};

	this.del_reward_item = function() {
		var del_item = self.reward_list.pop();
		this.reload_reward_list_html();
		$("#" + self.load_table + " article .num1").text(del_item.start);
	};

	this.reload_reward_list_html = function() {
		var all_money = 0;
		var max_item = 0;
		var item_list_html = '';
		for(var idx = 0; idx < self.reward_list.length; idx++) {
			var start = self.reward_list[idx]["start"];
			var end = self.reward_list[idx]["end"];
			var money = self.reward_list[idx]["money"];
			var num = end - start + 1;
			all_money = all_money + num * money;
			if(max_item < end) {
				max_item = end;
			}
			var item_html = "<li>第<span>" + start + "</span>~<span>" + end + "</span>名，各奖励<span>" + money + "</span>元<i class='fa fa-trash-o trash'></i></li>";
			item_list_html += item_html;
		}
		var item_html = '<li>奖励前<span class="largest">' + max_item + '</span>名，将支付<span class="total">' + all_money + '</span>元作为激励奖金</li>';
		item_list_html += item_html;
		$("#" + self.load_table + " article .dv4 ul").html(item_list_html);
	};

	//新建里边点击加号ul显示
	$("#" + self.load_table + " article .dv4 .parcel .add").click(function() {
		console.log("add");
		var start = parseInt($("#" + self.load_table + " article .num1").text());
		if(!start > 0) {
			layer.msg('请正确填写开始名次', {
				icon: 2
			});
			return;
		}
		var end = parseInt($("#" + self.load_table + " article .num2").val());
		if(!end > 0) {
			layer.msg('请正确填写结束名次', {
				icon: 2
			});
			return;
		}
		if(start > end) {
			layer.msg('结束名次不能小于开始名次', {
				icon: 2
			});
			return;
		}
		var money = parseInt($("#" + self.load_table + " article .num3").val());
		if(!money > 0) {
			layer.msg('请正确填写奖金数', {
				icon: 2
			});
			return;
		}

		self.add_reward_item(start, end, money);
	});
	//点击删除,删除数组中的条目,更新页面
	$("#" + self.load_table + " article .dv4 ul").on("click", ".trash", function() {
		console.log("item_trash");
		self.del_reward_item();
	});

	//新建tab切换
	$("#" + self.load_table + " article .dv4 .xuanze input").click(function() {
		$(this).parent().siblings().children("input[type='radio']").attr("checked", false);
		$(this).parent().siblings().children("input[type='radio']").prop("checked", false);
		$(this).attr("checked", true);
		$(this).prop("checked", true);
		if($(this).parent(".choice").index() == 1) {
			$("#" + self.load_table + " article .dv4 .tab1").css("display", "none");
			$("#" + self.load_table + " article .dv4 .tab2").css("display", "block");
		} else {
			$("#" + self.load_table + " article .dv4 .tab1").css("display", "block");
			$("#" + self.load_table + " article .dv4 .tab2").css("display", "none");
		}
	});

	$("#" + self.load_table + " article .dv4 .xuanze input").click(function() {
		$(this).siblings("input[type='radio']").attr("checked", false);
		$(this).siblings("input[type='radio']").prop("checked", false);
		$(this).attr("checked", true);
		$(this).prop("checked", true);
		if($(this).index() == 1) {
			$("#" + self.load_table + " article .dv4 .switch1").css("display", "block");
			$("#" + self.load_table + " article .dv4 .switch2").css("display", "none");
		} else {
			$("#" + self.load_table + " article .dv4 .switch1").css("display", "none");
			$("#" + self.load_table + " article .dv4 .switch2").css("display", "block");
		}
	});

	$("#" + self.load_table + " .task .issue .new_task_submit").click(function() {
		console.log("new_task_submit");
		var form_data = self.get_form_data();
		console.log(form_data);
		if(form_data === false) {
			return;
		}
		var task_type = $("#" + self.load_table + " .task .new_task_form" + " [name='task_type']").val();
		var type = 0;
		if(task_type == 1) {
			type = 2;
		}
		console.log("type");
		console.log(type);
		var money = form_data.money;
		$.ajax({
			url: "/task/index/pay/money/" + money + "/type/" + type + "/fr/" + self.load_table,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($("#"+self.load_table+" .new_task_info_panel .pay_ui"));
				$("#" + self.load_table + " .new_task_info_panel .pay_ui").html(data);
				$("#" + self.load_table + " .new_task_info_panel .pay_ui .payPwd").payPwd({
					max: 6,
					type: "password",
					callback: function(paypassword) {
						var pay_type = $("#" + self.load_table + " .new_task_info_panel .pay_ui input[type='radio']:checked").val();
						self.paypassword = paypassword;
						//self.add_task(paypassword,pay_type);
					}
				});
				$("#" + self.load_table + " .new_task_info_panel .pay_ui").reveal("{data-animation:'fade'}");
			},
			error: function() {
				layer.msg('加载支付出现错误', {
					icon: 2
				});
			}
		});
	});
	$("#" + self.load_table + " .new_task_panel .pay_ui").on("click", ".pop-submit-btn", function() {
		console.log("pop-submit-btn");
		var pay_type = $("#" + self.load_table + " .new_task_info_panel .pay_ui input[type='radio']:checked").val();
		self.add_task(self.get_pay_password(), pay_type);
	});
	$("#" + self.load_table + " .new_task_panel").on("click", ".new_task_info_panel .new_task_cancel", function() {
		$("#" + self.load_table + " .task_info_panel").addClass("hide");
		$("#" + self.load_table + " .task_list").removeClass("hide");
	});
}

function task_list(target, now_uid, base_url) {
	this.target = target;
	this.base_url = base_url;
	this.now_sel_id = 0;
	this.now_sel_type = 0;
	this.now_sel_employee = 0;
	this.paypassword = '';
	this.task_type = '';
	this.order_name = '';
	this.infinite_scroll = null;
	var self = this;
	var task_list_sel = "#" + self.target + " .task_list";

	this.pay = function(paypassword) {
		//self.paypassword = '';
		if(self.now_sel_type == 'tip') {
			var money = $(task_list_sel + " .tip_ui .tip_money").val();
			console.log("money", money);
			if(!money || money <= 0) {
				layer.msg('打赏金额必须大于零!', {
					icon: 2
				});
				return;
			}
			task_tip(self.now_sel_id, money, paypassword, function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status == 1) {
					$(task_list_sel + " .pay_ui").trigger('reveal:close');
					$('.task_' + self.now_sel_id + " .tip").html('继续打赏');
				}
			});
		} else if(self.now_sel_type == 'take') {
			task_take(self.now_sel_id, paypassword, function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status == 1) {
					$(task_list_sel + " .pay_ui").trigger('reveal:close');
					//不能猜输赢了
					$('.task_' + self.now_sel_id + " .get_reward").parent().append("<p class='p1'>正在参与任务</p>");
					$('.task_' + self.now_sel_id + " .get_reward").hide();
					$('.task_' + self.now_sel_id + " .guess").hide();
					var partin_count = Number($('.task_' + self.now_sel_id + " .partin_count").text()) + 1;
					$('.task_' + self.now_sel_id + " .partin_count").text(partin_count);

					$('.task_' + self.now_sel_id + " .user_" + now_uid).addClass('color-blue');

				}
			});
		} else if(self.now_sel_type == 'guess') {
			var money = $(task_list_sel + " .pay_ui .pay_money").val();
			console.log("money", money);
			if(!money || money <= 0) {
				layer.msg('猜输赢金额必须大于零!', {
					icon: 2
				});
				return;
			}
			task_guess(self.now_sel_id, self.now_sel_employee, money, paypassword, function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status == 1) {
					$(task_list_sel + " .pay_ui").trigger('reveal:close');
					//不能领取任务了
					$('.task_' + self.now_sel_id + " .guess").parent().append("<p class='p1'>正在参与猜输赢</p>");
					$('.task_' + self.now_sel_id + " .guess").hide();
					$('.task_' + self.now_sel_id + " .get_reward").hide();
				}
			});
		}
	};
	this.get_pay_password = function() {
		return self.paypassword;
	};

	this.get_url = function(p) {
		var url = "/task/employee_task/" + this.base_url + "_load";
		url += "/p/" + p;
		if(this.task_type){
            url += "/task_type/" + this.task_type;
		}
		if(this.order_name){
            url += "/order_name/" + this.order_name;
		}
		return url;
	};
	//下拉显示更多
	this.init_infinite_scroll = function() {
		var infinite_scroll_config = {
			binder: $("#" + self.target), //滚动条所在对象
			navSelector: "." + self.base_url + "_load .more", //导航的选择器，会被隐藏
			nextSelector: "." + self.base_url + "_load .more a", //包含下一页链接的选择器
			itemSelector: ".dv1", //你将要取回的选项(内容块)
			debug: true, //启用调试信息
			//默认采用："http://www.infinite-scroll.com/loading.gif"
			animate: true, //当有新数据加载进来的时候，页面是否有动画效果，默认没有
			extraScrollPx: 50, //滚动条距离底部多少像素的时候开始加载，默认150
			bufferPx: 40, //载入信息的显示时间，时间越大，载入信息显示时间越短
			errorCallback: function() {}, //当出错的时候，比如404页面的时候执行的函数
			pathParse: undefined,
			loading: {
				img: "/static/images/load.png",
				finishedMsg: "没有更多了",
				msgText: "页面正在加载，请稍后..."
			},
			state: {
				currPage: 1,
				isPaused: true
			},
			contentSelector: task_list_sel + " ." + self.base_url + "_load", //列表的样式名称或ID名称
			localMode: true //是否允许载入具有相同函数的页面，默认为false
		};
		this.infinite_scroll = $(task_list_sel + " ." + self.base_url + "_load").infinitescroll(infinite_scroll_config);
	};

	this.update_infinite_scroll = function(path, num) {
		console.log("update_infinite_scroll");
		this.reset_scroll();
		$(task_list_sel + " ." + self.base_url + "_load").infinitescroll("update", {
			path: path,
			state: {
				isDone: false,
				isDuringAjax: false,
				currPage: num
			}
		});
		$(task_list_sel + " ." + self.base_url + "_load").infinitescroll("bind");
	};

	this.pause_infinite_scroll = function() {
		$(task_list_sel + " ." + self.base_url + "_load").infinitescroll("pause");
	};

	this.resume_infinite_scroll = function() {
		$(task_list_sel + " ." + self.base_url + "_load").infinitescroll("resume");
	};

	this.reset_scroll = function() {
		$("#" + self.target).scrollTop(0);
	};

	var dv1_count = $(task_list_sel + " ." + self.base_url + "_load").children('.dv1').length;
	console.log('dv1_count:' + dv1_count);
	this.init_infinite_scroll();
	if(dv1_count >= 10) {
		this.resume_infinite_scroll();
	}

	//最上层的任务分类导航 排序规则
	$(task_list_sel + " .nav li").click(function() {
		console.log("change_task_type");
		$(task_list_sel + " .nav li").removeClass("flow");
		$(this).addClass("flow");
		self.task_type = $(this).attr('data-id') || '';
		var url = self.get_url(1);

		self.pause_infinite_scroll();
		loadPage(url, self.base_url);
		var dv1_count = $(task_list_sel + " ." + self.base_url + "_load").children('.dv1').length;
		console.log('change_task_type:' + dv1_count);
		self.update_infinite_scroll(self.get_url(2), 1);
		if(dv1_count >= 10) {
			self.resume_infinite_scroll();
		}

	});

	//排序
	$(task_list_sel + " header .sort .classify p").click(function() {
        $(this).css("background-color", "#e1ebf9");
        $(this).siblings().css("background-color", "#fff");
		console.log("change_order_name");
		self.order_name = $(this).attr('data-id') || '';
		var url = self.get_url(1);

		self.pause_infinite_scroll();
		loadPage(url, self.base_url);
		var dv1_count = $(task_list_sel + " ." + self.base_url + "_load").children('.dv1').length;
		console.log('change_order_name:' + dv1_count);
		self.update_infinite_scroll(self.get_url(2), 1);
		if(dv1_count >= 10) {
			self.resume_infinite_scroll();
		}

	});
	//领取红包
	$(task_list_sel + " article").on('click', '.picture', function(e) {
		e.stopPropagation();
		take_envelopes($(this), $(this).parent('.mengceng').attr('hongbao_id'));
	});
	//红包不领取 mengceng禁用
	$(task_list_sel + " article").on('click', '.m_c', function(e) {
		e.stopPropagation();
	});

	//领取红包 公共方法
	function take_envelopes(that, id) {
		$.ajax({
			url: '/task/index/getRedEnvelope',
			type: 'post',
			data: {
				'redid': id
			},
			dataType: "json",
			success: function(data) {
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status) {
					//领取成功
					that.parent().removeClass("m_c");
					var text = '<p class="p6">已领' + data.data.money + '元</p>';
					that.siblings('.right').children('.within').children('.details').prepend(text);
					that.remove();
				}
			},
			error: function() {
				console.log('操作出现错误', {
					icon: 2
				});
			}
		});
	}

	$(task_list_sel + " article").on("click", ".dv1", function() {
		var id = $(this).attr("task_id");
		var nowflag = $(task_list_sel + " header ul li.flow div").text();
		//now_uid;
		$.ajax({
			url: '/task/index/show/id/' + id + '/fr/' + self.target,
			type: 'get',
			success: function(data) {
				self.pause_infinite_scroll();
				$("#" + self.target + " .task_direct_panel .task_direct_info_panel").html(data);
				$("#" + self.target + " .task_direct_panel header div ul li.current div").text(nowflag);
				$("#" + self.target + " .task_list").addClass("hide");
				self.reset_scroll();
				$("#" + self.target + " .task_direct_panel").removeClass("hide");
			},
			error: function() {
				layer.msg('加载任务详情出现错误', {
					icon: 2
				});
			}
		});
	});

	$(task_list_sel + " header .xinjian ").click(function() {
		//loadPage('/task/index/new_task/fr/'+self.target,self.target);
		let par = $(this).parents(".once").attr("id");
		console.log(par);
		$.ajax({
			url: '/task/index/new_task/fr/' + self.target,
			type: 'get',
			success: function(data) {
				//console.log(data);
				self.pause_infinite_scroll();
				//console.log($("#"+self.target+" .new_task_panel"));
				//console.log($("#"+self.target+" .new_task_panel .new_task_info_panel"));
				$("#" + self.target + " .new_task_panel .new_task_info_panel").html(data);
				$("#" + self.target + " .task_list").addClass("hide");
				self.reset_scroll();
				$("#" + self.target + " .new_task_panel").removeClass("hide");
				$("#"+par+" .main-content.select-window").attr("data-selector","#"+par+" .new-task-select-window-choosen-staff");
			},
			error: function() {
				layer.msg('加载任务新建出现错误', {
					icon: 2
				});
			}
		});
	});
	

	$("#" + self.target + " .task_info_panel .top .current").click(function() {
		$("#" + self.target + " .task_info_panel").addClass("hide");
		self.reset_scroll();
		$("#" + self.target + " .task_list").removeClass("hide");
		self.resume_infinite_scroll();
	});
	$(task_list_sel + " article").on("click", ".right .get_reward", function(e) {
		e.stopPropagation();
		var that = $(this);
		var type = that.attr('task-type');
		var money = that.attr('task-money');
		var task_id = that.attr('data-id');
		self.now_sel_id = task_id;
		if(type == 2) {
			self.now_sel_type = 'take';
			$.ajax({
				url: '/task/index/pay/money/' + money,
				type: 'get',
				success: function(data) {
					//console.log(data);
					//console.log($(task_list_sel+" .tip_ui .mid"));
					$(task_list_sel + " .pay_ui").html(data);
					$(task_list_sel + " .pay_ui .payPwd").payPwd({
						max: 6,
						type: "password",
						callback: function(paypassword) {
							self.paypassword = paypassword;
							//self.pay(paypassword);

						}
					});
					$(task_list_sel + " .pay_ui").reveal("{data-animation:'fade'}");
					$(task_list_sel + " .tip_ui").trigger('reveal:close');
				},
				error: function() {
					layer.msg('加载参与任务支付出现错误', {
						icon: 2
					});
				}
			});
		} else {
			task_take(self.now_sel_id, "", function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status == 1) {
					that.parent().append("<p class='p1'>正在参与任务</p>");
					that.hide();

					var partin_count = Number($('.task_' + self.now_sel_id + " .partin_count").text()) + 1;
					$('.task_' + self.now_sel_id + " .partin_count").text(partin_count); //已参与人数量变化
					$('.task_' + self.now_sel_id + " .user_" + now_uid).addClass('color-blue'); //以参与人标记
				}
			});
		}
	});
	$(task_list_sel + " article").on("click", ".right .guess", function(e) {
		e.stopPropagation();
		console.log("guess");
		var id = $(this).attr("task_id");
		self.now_sel_id = id;
		self.now_sel_type = 'guess';
		console.log(id);
		$.ajax({
			url: '/task/task_guess/show_guess_ui/task_id/' + id,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_list_sel+" .guess_ui"));
				$(task_list_sel + " .guess_ui").html(data);
				$(task_list_sel + " .guess_ui").reveal("{data-animation:'fade'}");
				$.ajax({
					url: '/task/task_guess/show/task_id/' + id,
					type: 'get',
					success: function(data) {
						//console.log(data);
						//console.log($(task_list_sel+" .guess_ui .box"));
						$(task_list_sel + " .guess_ui .box").html(data);
					},
					error: function() {
						layer.msg('加载已下注列表出现错误', {
							icon: 2
						});
					}
				});
			},
			error: function() {
				layer.msg('加载猜输赢出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_list_sel + " .guess_ui").on("click", ".list .fill", function() {
		console.log("guess_sel");
		var task_id = $(this).attr("task_id");
		var employee_id = $(this).attr("employee_id");
		self.now_sel_id = task_id;
		self.now_sel_type = 'guess';
		self.now_sel_employee = employee_id;
		console.log("employee_id", employee_id);
		$.ajax({
			url: '/task/index/pay/type/1',
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_details_sel+" .tip_ui .mid"));
				$(task_list_sel + " .pay_ui").html(data);
				$(task_list_sel + " .pay_ui .payPwd").payPwd({
					max: 6,
					type: "password",
					callback: function(paypassword) {
						self.paypassword = paypassword;
						//self.pay(paypassword);
					}
				});
				$(task_list_sel + " .pay_ui").reveal("{data-animation:'fade'}");
				$(task_list_sel + " .guess_ui").trigger('reveal:close');
			},
			error: function() {
				layer.msg('加载打赏支付出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_list_sel + " article").on("click", ".right .tip", function(e) {
		e.stopPropagation();
		console.log("tip");
		var id = $(this).attr("task_id");
		self.now_sel_id = id;
		self.now_sel_type = 'tip';
		console.log(id);
		$.ajax({
			url: '/task/task_tip/show_tip_ui/id/' + id,
			type: 'get',
			success: function(data) {
				//console.log(data);
				console.log($(task_list_sel + " .tip_ui"));
				$(task_list_sel + " .tip_ui").html(data);
				$(task_list_sel + " .tip_ui").reveal("{data-animation:'fade'}");
				$.ajax({
					url: '/task/task_tip/show/id/' + id,
					type: 'get',
					success: function(data) {
						//console.log(data);
						//console.log($(task_list_sel+" .tip_ui .mid"));
						$(task_list_sel + " .tip_ui .mid").html(data);
					},
					error: function() {
						layer.msg('加载已打赏列表出现错误', {
							icon: 2
						});
					}
				});
			},
			error: function() {
				layer.msg('加载打赏出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_list_sel + " .tip_ui").on("click", ".decide .tip_go", function() {
		console.log("tip_go");
		var tip_money = $(task_list_sel + " .tip_ui .tip_money").val();
		console.log("tip_money", tip_money);
		if(!tip_money || tip_money <= 0) {
			layer.msg('打赏金额必须大于零!', {
				icon: 2
			});
			return;
		}
		$.ajax({
			url: '/task/index/pay/money/' + tip_money,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_list_sel+" .tip_ui .mid"));
				$(task_list_sel + " .pay_ui").html(data);
				$(task_list_sel + " .pay_ui .payPwd").payPwd({
					max: 6,
					type: "password",
					callback: function(paypassword) {
						self.paypassword = paypassword;
						//self.pay(paypassword);
					}
				});
				$(task_list_sel + " .pay_ui").reveal("{data-animation:'fade'}");
				$(task_list_sel + " .tip_ui").trigger('reveal:close');
			},
			error: function() {
				layer.msg('加载打赏支付出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_list_sel + " .tip_ui").on("click", ".decide .tip_cancel", function() {
		console.log("tip_cancel");
		$(task_list_sel + " .tip_ui").trigger('reveal:close');
	});
	$(task_list_sel + " .pay_ui").on("click", ".pop-submit-btn", function() {
		console.log("tip_submit");
		var money_element = $(task_list_sel + " .pay_ui .pay_money");
		if(money_element.length > 0) {
			var money = money_element.val();
			if(!money || money <= 0) {
				layer.msg('金额必须大于零', {
					icon: 2
				});
				return false;
			}
		}
		var paypassword = self.get_pay_password();
		console.log("paypassword", paypassword);
		if(paypassword == '') {
			layer.msg('请输入完整密码!', {
				icon: 2
			});
			return false;
		}
		self.pay(paypassword);
	});
	$(task_list_sel + " .pay_ui").on("click", ".pop-close-btn", function() {
		console.log("tip_cancel");
		$(task_list_sel + " .pay_ui").trigger('reveal:close');
	});

	$(task_list_sel + " article").on("click", ".right .add", function(e) {
		e.stopPropagation();
		var qw = $(this).attr('index_img');
		var task_id = $(this).attr('task_id');
		var p = parseInt($(this).siblings().text());
		console.log(p);
		var that = this;
		console.log(that);
		var i = parseInt(qw);
		if(i % 2) {
			task_like(task_id, true, function(data) {
				console.log(task_id);
				layer.msg('点赞成功', {
					icon: 1
				});
				$(that).attr('src', '/task/img/praise.png');
				var q = p + 1;
				$(that).siblings().text(q);
			});
		} else {
			task_like(task_id, false, function(data) {
				layer.msg('取消点赞成功', {
					icon: 1
				});
				$(that).attr('src', '/task/img/zan.png');
				var q = p - 1;
				$(that).siblings().text(q);
			});
		}
		i++;
		$(this).attr('index_img', i)
	});

	$(task_list_sel + " header .xinjian ").click(function() {
		//loadPage('/task/index/new_task/fr/'+self.target,self.target);
		var nowflag = $(task_list_sel + " header ul li.flow div").text();
		let par = $(this).parents(".once").attr("id");
		console.log(par);
		$.ajax({
			url: '/task/index/new_task/fr/' + self.target,
			type: 'get',
			success: function(data) {
				//console.log(data);
				self.pause_infinite_scroll();
				//console.log($("#"+self.target+" .new_task_panel"));
				//console.log($("#"+self.target+" .new_task_panel .new_task_info_panel"));
				$("#" + self.target + " .new_task_panel .new_task_info_panel").html(data);
				$("#" + self.target + " .new_task_panel header div ul li.current div").text(nowflag);
				$("#" + self.target + " .task_list").addClass("hide");
				self.reset_scroll();
				$("#" + self.target + " .new_task_panel").removeClass("hide");
				console.log(self.target);
				$("#"+par+" .main-content.select-window").attr("data-selector","#"+par+" .new-task-select-window-choosen-staff");
			},
			error: function() {
				layer.msg('加载任务新建出现错误', {
					icon: 2
				});
			}
		});
	});

	$(task_list_sel + " article").on("click", ".right .end_task", function(e) {
		e.stopPropagation();
		var that = $(this);
		// console.log('终止任务');
		task_end(that.attr('data-id'), function(data) {
			layer.msg(data.info, {
				icon: data.status == 1 ? 1 : 2
			});
			if(data.status) {
				//终止成功
				that.parents('.details').html('<p class="p3">任务被终止</p>');
			}
		});
	});

}

function task_details(load_table, id, type, now_uid) {
	this.load_table = load_table;
	this.id = id;
	this.type = type;
	this.now_sel_type = '';
	this.now_sel_employee = 0;
	this.paypassword = '';
	var self = this;
	var task_details_sel = "#" + self.load_table + " .task_details";

	this.update_ranking = function() {
		$.ajax({
			url: '/task/index/get_ranking_page/id/' + self.id,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_details_sel+" .left .box"));
				$(task_details_sel + " .left .box").html(data);
				var self_idx = $(task_details_sel + " .left .box .self_idx").val();
				if(self_idx > 0) {
					var self_heigth = self_idx * 35;
					$(task_details_sel + " .left .box").scrollTop(self_heigth);
				}
			},
			error: function() {
				layer.msg('加载排行榜出现错误', {
					icon: 2
				});
			}
		});
	};
	this.update_tip = function() {
		$.ajax({
			url: '/task/task_tip/show/id/' + self.id,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_details_sel+" .right .particulars"));
				$(task_details_sel + " .right .particulars").html(data);
			},
			error: function() {
				layer.msg('加载打赏出现错误', {
					icon: 2
				});
			}
		});
	};
	this.update_comment = function() {
		$.ajax({
			url: '/task/task_comment/show/id/' + self.id,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_details_sel+" .down .review"));
				$(task_details_sel + " .down .review").html(data);
			},
			error: function() {
				layer.msg('加载评论出现错误', {
					icon: 2
				});
			}
		});
	};
	this.pay = function(paypassword) {
		//self.paypassword = '';
		if(self.now_sel_type == 'tip') {
			var money = $(task_details_sel + " .tip_ui .tip_money").val();
			console.log("money", money);
			if(!money || money <= 0) {
				layer.msg('打赏金额必须大于零!', {
					icon: 2
				});
				return;
			}
			task_tip(self.id, money, paypassword, function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				console.log(task_details_sel);
				if(data.status == 1) {
					$(task_details_sel + " .pay_ui").trigger('reveal:close');
					//TODO 成功打赏
					$(task_details_sel + " .tip").html('继续打赏'); //详情页的状态更新
					$(".task_" + self.id + " .details .tip").html('继续打赏'); //列表页的状态更新
					var pre_money = Number($(task_details_sel + " .dv2 .explain .orange").text());
					$(task_details_sel + " .dv2 .explain .orange").text(Number(pre_money) + Number(money));
					self.update_tip();

					var pre_my_tips = $(task_details_sel + " .dv2 .explain .gray .my_tip_money").html();
					console.log(pre_my_tips);
					var my_tips;
					if(pre_my_tips) {
						my_tips = Number(pre_my_tips) + Number(money);
					} else {
						my_tips = Number(money);
					}
					$(task_details_sel + " .dv2 .explain .gray").html("（你已打赏<span class='my_tip_money'>" + my_tips + "</span>元）");

				}
			});
		} else if(self.now_sel_type == 'take') {
			task_take(self.id, paypassword, function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status == 1) {
					$(task_details_sel + " .pay_ui").trigger('reveal:close');
					//TODO 成功加入任务
					//不能猜输赢了
					//详情页的状态更新
					$(task_details_sel + " .get_reward").parent().append("<p class='p1'>正在参与任务</p>");
					$(task_details_sel + " .get_reward").hide();
					$(task_details_sel + " .guess").hide();

					//列表页的状态更新
					$(".task_" + self.id + " .details .get_reward").parent().append("<p class='p1'>正在参与任务</p>");
					$(".task_" + self.id + " .details .get_reward").hide();
					$(".task_" + self.id + " .details .guess").hide();

					//数量以及参与人的更新
					var partin_count = Number($(task_details_sel + " .partin_count").text()) + 1;
					$(task_details_sel + ' .partin_count').text(partin_count);
					$(task_details_sel + ' .user_' + now_uid).addClass('color-blue');

					$('.task_' + self.id + " .partin_count").text(partin_count);
					$('.task_' + self.id + " .user_" + now_uid).addClass('color-blue');

				}
			});
		} else if(self.now_sel_type == 'guess') {
			var money = $(task_details_sel + " .pay_ui .pay_money").val();
			console.log("money", money);
			if(!money || money <= 0) {
				layer.msg('猜输赢金额必须大于零!', {
					icon: 2
				});
				return;
			}
			task_guess(self.id, self.now_sel_employee, money, paypassword, function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status == 1) {
					$(task_details_sel + " .pay_ui").trigger('reveal:close');
					//TODO 提交猜输赢成功
					//不能领取任务了
					//详情页的状态更新
					$(task_details_sel + " .guess").parent().append("<p class='p1'>正在参与猜输赢</p>");
					$(task_details_sel + " .guess").hide();
					$(task_details_sel + " .get_reward").hide();

					//列表页的状态更新
					$(".task_" + self.id + " .details .guess").parent().append("<p class='p1'>正在参与猜输赢</p>");
					$(".task_" + self.id + " .details .guess").hide();
					$(".task_" + self.id + " .details .get_reward").hide();

				}
			});
		}
	};
	this.get_pay_password = function() {
		return self.paypassword;
	};

	// if(self.type!=3){
	//     self.update_ranking();
	// }
	self.update_ranking();
	self.update_tip();
	self.update_comment();

	//领小红包
	$(task_details_sel + " article .dv2 .left .box img").click(function() {

		var a = "<span class='two'>（100￥）</span>";
		$(this).parent().append(a);
		$(task_details_sel + " .turn").text("已领取");
		$(this).remove();
	});
	$(task_details_sel + " .right .get_reward").click(function() {
		var that = $(this);
		if(self.type == 2) {
			self.now_sel_type = 'take';
			var money = that.attr('task-money');
			console.log("money", money);
			$.ajax({
				url: '/task/index/pay/money/' + money,
				type: 'get',
				success: function(data) {
					//console.log(data);
					//console.log($(task_details_sel+" .tip_ui .mid"));
					$(task_details_sel + " .pay_ui").html(data);
					$(task_details_sel + " .pay_ui .payPwd").payPwd({
						max: 6,
						type: "password",
						callback: function(paypassword) {
							self.paypassword = paypassword;
							//self.pay(paypassword);
						}
					});
					$(task_details_sel + " .pay_ui").reveal("{data-animation:'fade'}");
					$(task_details_sel + " .tip_ui").trigger('reveal:close');


                    //TODO 成功加入任务
                    that.parent().append("<p class='p1'>正在参与任务</p>");
                    that.hide();

                    //列表页的状态更新
                    $(".task_" + self.id + " .details .get_reward").parent().append("<p class='p1'>正在参与任务</p>");
                    $(".task_" + self.id + " .details .get_reward").hide();

                    //数量以及参与人的更新
                    var partin_count = Number(that.parents('.task_details').find('.partin_count').text()) + 1;
                    that.parents('.task_details').find('.partin_count').text(partin_count);
                    that.parents('.task_details').find('.user_' + now_uid).addClass('color-blue');

                    $('.task_' + self.id + " .partin_count").text(partin_count);
                    $('.task_' + self.id + " .user_" + now_uid).addClass('color-blue');

                    self.update_ranking();

				},
				error: function() {
					layer.msg('加载参与任务支付出现错误', {
						icon: 2
					});
				}
			});
		} else {
			task_take(self.id, "", function(data) {
				//console.log(data);
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status == 1) {
					//TODO 成功加入任务
					that.parent().append("<p class='p1'>正在参与任务</p>");
					that.hide();

					//列表页的状态更新
					$(".task_" + self.id + " .details .get_reward").parent().append("<p class='p1'>正在参与任务</p>");
					$(".task_" + self.id + " .details .get_reward").hide();

					//数量以及参与人的更新
					var partin_count = Number(that.parents('.task_details').find('.partin_count').text()) + 1;
					that.parents('.task_details').find('.partin_count').text(partin_count);
					that.parents('.task_details').find('.user_' + now_uid).addClass('color-blue');

					$('.task_' + self.id + " .partin_count").text(partin_count);
					$('.task_' + self.id + " .user_" + now_uid).addClass('color-blue');

                    self.update_ranking();
				}
			});
		}
	});
	$(task_details_sel + " .right .guess").click(function() {
		console.log("guess");
		self.now_sel_type = 'guess';
		$.ajax({
			url: '/task/task_guess/show_guess_ui/task_id/' + self.id,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_list_sel+" .guess_ui"));
				$(task_details_sel + " .guess_ui").html(data);
				$(task_details_sel + " .guess_ui").reveal("{data-animation:'fade'}");
				$.ajax({
					url: '/task/task_guess/show/task_id/' + self.id,
					type: 'get',
					success: function(data) {
						//console.log(data);
						//console.log($(task_list_sel+" .guess_ui .box"));
						$(task_details_sel + " .guess_ui .box").html(data);
					},
					error: function() {
						layer.msg('加载已下注列表出现错误', {
							icon: 2
						});
					}
				});
			},
			error: function() {
				layer.msg('加载猜输赢出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_details_sel + " .guess_ui").on("click", ".list .fill", function() {
		console.log("guess_sel");
		var employee_id = $(this).attr("employee_id");
		self.now_sel_type = 'guess';
		self.now_sel_employee = employee_id;
		console.log("employee_id", employee_id);
		$.ajax({
			url: '/task/index/pay/type/1',
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_details_sel+" .tip_ui .mid"));
				$(task_details_sel + " .pay_ui").html(data);
				$(task_details_sel + " .pay_ui .payPwd").payPwd({
					max: 6,
					type: "password",
					callback: function(paypassword) {
						self.paypassword = paypassword;
						//self.pay(paypassword);
					}
				});
				$(task_details_sel + " .pay_ui").reveal("{data-animation:'fade'}");
				$(task_details_sel + " .guess_ui").trigger('reveal:close');
			},
			error: function() {
				layer.msg('加载猜输赢选择出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_details_sel + " .right .tip").click(function() {
		console.log("tip");
		$.ajax({
			url: '/task/task_tip/show_tip_ui/id/' + self.id,
			type: 'get',
			success: function(data) {
				//console.log(data);
				console.log($(task_details_sel + " .tip_ui"));
				$(task_details_sel + " .tip_ui").html(data);
				$(task_details_sel + " .tip_ui").reveal("{data-animation:'fade'}");
				$.ajax({
					url: '/task/task_tip/show/id/' + self.id,
					type: 'get',
					success: function(data) {
						//console.log(data);
						//console.log($(task_details_sel+" .tip_ui .mid"));
						$(task_details_sel + " .tip_ui .mid").html(data);
					},
					error: function() {
						layer.msg('加载打赏出现错误', {
							icon: 2
						});
					}
				});
			},
			error: function() {
				layer.msg('加载打赏出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_details_sel + " .tip_ui").on("click", ".decide .tip_go", function() {
		//      console.log("tip_go");
		self.now_sel_type = 'tip';
		var tip_money = $(task_details_sel + " .tip_ui .tip_money").val();
		//      console.log("tip_money",tip_money);
		if(!tip_money || tip_money <= 0) {
			layer.msg('打赏金额必须大于零!', {
				icon: 2
			});
			return;
		}
		$.ajax({
			url: '/task/index/pay/money/' + tip_money,
			type: 'get',
			success: function(data) {
				//console.log(data);
				//console.log($(task_details_sel+" .tip_ui"));
				$(task_details_sel + " .pay_ui").html(data);
				$(task_details_sel + " .pay_ui .payPwd").payPwd({
					max: 6,
					type: "password",
					callback: function(paypassword) {
						self.paypassword = paypassword;
						//self.pay(paypassword);
					}
				});
				$(task_details_sel + " .pay_ui").reveal("{data-animation:'fade'}");
				$(task_details_sel + " .tip_ui").trigger('reveal:close');
			},
			error: function() {
				layer.msg('加载打赏支付出现错误', {
					icon: 2
				});
			}
		});
	});
	$(task_details_sel + " .tip_ui").on("click", ".decide .tip_cancel", function() {
		//      console.log("tip_cancel");
		$(task_details_sel + " .tip_ui").trigger('reveal:close');
	});
	$(task_details_sel + " .pay_ui").on("click", ".pop-submit-btn", function() {
		//      console.log("tip_submit");
		var money_element = $(task_details_sel + " .pay_ui .pay_money");
		if(money_element.length > 0) {
			var money = money_element.val();
			if(!money || money <= 0) {
				layer.msg('金额必须大于零', {
					icon: 2
				});
				return false;
			}
		}
		var paypassword = self.get_pay_password();
		if(paypassword == '') {
			layer.msg('请输入完整密码!', {
				icon: 2
			});
			return false;
		}
		self.pay(paypassword);
	});
	$(task_details_sel + " .pay_ui").on("click", ".pop-close-btn", function() {
		//      console.log("tip_cancel");
		$(task_details_sel + " .pay_ui").trigger('reveal:close');
	});

	$(task_details_sel + " .dv3").on("click", ".right .add", function() {
		var jt = $(this).attr('index_img');
		var task_id = $(this).attr('task_id');
		var x = parseInt($(this).siblings(".yi").text());
		var j = parseInt(jt);
		var that = this;
		console.log(j);
		if(j % 2) {
			task_like(task_id, true, function(data) {
				layer.msg('点赞成功', {
					icon: 1
				});
				$(that).attr('src', '/task/img/praise.png');
				var y = x + 1;
				$(that).siblings(".yi").text(y);
				//列表页更新
				$(".task_" + task_id + " .comment .add").attr('src', '/task/img/praise.png');
				$(".task_" + task_id + " .comment .add").siblings(".yi").text(y);

			});
		} else {
			task_like(task_id, false, function(data) {
				layer.msg('取消点赞成功', {
					icon: 1
				});
				$(that).attr('src', '/task/img/zan.png');
				var y = x - 1;
				$(that).siblings(".yi").text(y);

				//列表页更新
				$(".task_" + task_id + " .comment .add").attr('src', '/task/img/zan.png');
				$(".task_" + task_id + " .comment .add").siblings(".yi").text(y);
			});
		}
		j++;
		$(this).attr('index_img', j);
	});

	$(task_details_sel + " .dv3").on("click", ".down .one .comment", function() {
		var name = $(this).siblings('div').children("p").children(".name_1").text();
		var name_true = '回复:' + name;
		$(this).parents('.dv3').children(".up").children(".content").attr('placeholder', name_true);
		var comment_id = $(this).siblings('div').children("p").children(".name_1").attr('comment_id');
		$(this).parents('.dv3').children(".up").children(".like").children('.right').children('p').attr({
			'comment_id': comment_id,
		});

	});
	$(task_details_sel + " .dv3").on("click", ".up .right p", function() {
		var that = $(this);
		var content = that.parents('div.like').prev('.content').val();
		var task_id = that.attr('data-id');
		var comment_id = that.attr('comment_id');
		var truename = that.attr('now-truename');
		$.ajax({
			url: '/task/task_comment/addTaskComment',
			type: 'post',
			data: {
				'task_id': task_id,
				'reply_content': content,
				'comment_id': comment_id
			},
			success: function(data) {
				layer.msg(data.info, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status) {
					//评论成功
					self.update_comment();
					var comment_str = $(task_details_sel + " .dv3 .title").text();
					var comment_count = Number(comment_str.substring(comment_str.indexOf("（") + 1, comment_str.indexOf("）"))) + 1;
					$(task_details_sel + " .dv3 .title").text("评论（" + Number(comment_count) + "）");

					$("#" + self.load_table + " .task_" + task_id + " .within .comment div .task_details").next('span').html(Number(comment_count));

					that.removeAttr('comment_id');
					that.parents('.up').children('.content ').val('');
					that.parents('.up').children('.content ').attr('placeholder', '请输入评论')
				}

			}
		});
	});
	$(task_details_sel + " article").on("click", ".right .end_task", function() {
		// console.log('终止任务');
		var that = $(this);
		task_end(that.attr('data-id'), function(data) {
			layer.msg(data.info, {
				icon: data.status == 1 ? 1 : 2
			});
			if(data.status) {
				//终止成功
				that.parents('.details').html('<p class="p3">任务被终止</p>');
				$(".task_" + that.attr('data-id') + " .within .details").html('<p class="p3">任务被终止</p>');
			}
		});
	});
	
	$(task_details_sel + " .dv2").on("click", ".box li .help", function() {
		var that = $(this);
		var take_id = that.parent('li').attr('data-id');
		task_whether_help(take_id, true, function(data) {
			that.parent('li').html('<span>判定已帮</span>');

		});
	});
	$(task_details_sel + " .dv2").on("click", ".box li .unhelp", function() {
		var that = $(this);
		var take_id = that.parent('li').attr('data-id');
		task_whether_help(take_id, false, function(data) {
			that.parent('li').html('<span>判定未帮</span>');
		});
	});

	function task_whether_help(take_id, type, fun) {
		var post_data = "take_id=" + take_id;
		if(!type) {
			post_data += "&unhelp=1";
		}
		$.ajax({
			url: '/task/employee_task/task_help',
			type: 'post',
			data: post_data,
			dataType: "json",
			success: function(data) {
				if(data.success) {
					fun(data);
				} else {
					layer.msg(data.msg, {
						icon: 2
					});
				}
			},
			error: function() {
				layer.msg('操作出现错误', {
					icon: 2
				});
			}
		});
	}

}

function task_whether_help(employee_id, type, fun) {
	var post_data = "task_id=" + self.id + "&employee_id=" + employee_id;
	if(!type) {
		post_data += "&unhelp=1";
	}
	$.ajax({
		url: '/task/employee_task/task_help',
		type: 'post',
		data: post_data,
		dataType: "json",
		success: function(data) {
			if(data.success) {
				fun(data);
			} else {
				layer.msg(data.msg, {
					icon: 2
				});
			}
		},
		error: function() {
			layer.msg('操作出现错误', {
				icon: 2
			});
		}
		
		
	});
}

