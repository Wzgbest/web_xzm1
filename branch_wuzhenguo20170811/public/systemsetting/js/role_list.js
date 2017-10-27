// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
var role_list_panel_base = "#frames #role-managementfr .systemsetting_role .content";
var panel = role_list_panel_base + " .dv2";
$(window).resize(function() {
	//$("#frames .once").width(window.innerWidth-220);
	//$("#frames .once").height(window.innerHeight-80);
});

function findActivityRoleId() {
	return $(role_list_panel_base + " .dv1 .role_item_list .activity").attr("role_id");
}

function findRoleId(target) {
	return $(target).parent().attr("role_id");
}
$(panel).on('click', ".one .role_manage", function() {
	var role_id = findActivityRoleId();
	loadRuleManage(role_id);
});
$(panel).on('click', ".one .employee_manage", function() {
	var role_id = findActivityRoleId();
	loadRoleEmployeeTable(role_id);
});

function loadRuleManage(role_id) {
	if(!role_id > 0) {
		role_id = findActivityRoleId();
	}
	var url = "/systemsetting/role/role_manage/id/" + role_id;
	$.ajax({
		url: url,
		type: 'get',
		async: false,
		success: function(data) {
			$(panel).html(data);
		},
		error: function() {
			layer.msg('获取权限失败!', {
				icon: 2
			});
		}
	});
}

function loadRoleEmployeeTable(role_id) {
	if(!role_id > 0) {
		role_id = findActivityRoleId();
	}
	var url = "/systemsetting/role/employee_list/id/" + role_id;
	$.ajax({
		url: url,
		type: 'get',
		async: false,
		success: function(data) {
			$(panel).html(data);
		},
		error: function() {
			layer.msg('获取成员失败!', {
				icon: 2
			});
		}
	});
}
var role_item_list_panel = '.systemsetting_role .content .dv1 .role_item_list';
var role_add_employee_panel = role_list_panel_base + ' .addEmployeeModal';
$(".systemsetting_role .top .add").click(function() {
	var role_id = findActivityRoleId();
	console.log(role_id);
	if(role_id > 0) {
		var url = "/systemsetting/role/not_role_employee_list/id/" + role_id;
		$.ajax({
			url: url,
			type: 'get',
			async: false,
			success: function(data) {
				$(role_add_employee_panel + ' .add_employee_list').html(data);
				$(role_add_employee_panel).reveal("{data-animation:'fade'}");
			},
			error: function() {
				layer.msg('获取员工信息失败!', {
					icon: 2
				});
			}
		});
	}
});

function update_add_employee_num(num) {
	$(role_add_employee_panel + " .select_num").html(num);
}
var role_add_employee_checked = role_add_employee_panel + " .add_employee_list .u-tabList .u-tabCheckbox :checked";
$(role_add_employee_panel + " .add_employee_list").on('click', '.u-tabCheckbox input[type="checkbox"]', function() {
	var role_add_employee_checked_num = $(role_add_employee_checked).length;
	update_add_employee_num(role_add_employee_checked_num);
});
$(role_add_employee_panel + " .add_employee_ok").click(function() {
	var role_id = findActivityRoleId();
	if(role_id > 0) {
		var employee_ids = '';
		var employee_ids_arr = new Array();
		$(role_add_employee_checked).each(function() {
			employee_ids_arr.push($(this).val());
		});
		employee_ids += employee_ids_arr.join(",");
		$.ajax({
			url: '/systemsetting/role/addRoleMember',
			type: 'post',
			data: "role_id=" + role_id + "&user_ids=" + employee_ids,
			dataType: "json",
			success: function(data) {
				//console.log(data);
				layer.msg(data.message, {
					icon: data.status == 1 ? 1 : 2
				});
				if(data.status) {
					$(role_add_employee_panel).trigger('reveal:close');
					loadRoleEmployeeTable(role_id);
				}
			},
			error: function() {
				layer.msg('添加职位时发生错误!', {
					icon: 2
				});
			}
		});
	}
});
$(role_add_employee_panel + " .add_employee_cancel").click(function() {
	$(role_add_employee_panel).trigger('reveal:close');
});
$(".systemsetting_role .content .dv1 .title .fa-plus").click(function() {
	console.log("add_role");
	var add_item = $(role_item_list_panel + " .add_item");
	if(add_item.length > 0) {
		//console.log(add_item.children(".add_item_text"));
		add_item.children(".add_item_text").focus();
		return;
	}
	var item_html = '';
	item_html += '<li class="role_item add_item temp_item">' +
		'<input type="text" class="item_text add_item_text" value=""/>' +
		'<i class="fa fa-check item_btn item_check add_item_check"></i>' +
		'<i class="fa fa-remove item_btn item_remove add_item_remove"></i>' +
		'</li>';
	$(role_item_list_panel).append(item_html);
});
$(role_item_list_panel).on('click', ".add_item .add_item_check", function() {
	var add_role_name = $(role_item_list_panel + " .add_item").children(".add_item_text").val();
	console.log(add_role_name);
	if(!add_role_name) {
		return;
	}
	$.ajax({
		url: '/systemsetting/role/addRole',
		type: 'post',
		data: "role_name=" + add_role_name,
		dataType: "json",
		success: function(data) {
			//console.log(data);
			layer.msg(data.message, {
				icon: data.status == 1 ? 1 : 2
			});
			if(data.status) {
				loadPage("/systemsetting/role/index", "role-managementfr");
			}
		},
		error: function() {
			layer.msg('添加职位时发生错误!', {
				icon: 2
			});
		}
	});
});
$(role_item_list_panel).on('click', ".add_item .add_item_remove", function() {
	$(role_item_list_panel + " .add_item").remove();
});
$(role_list_panel_base + " .dv1 .role_name").click(function() {
	$(role_item_list_panel + " .role_item").removeClass("activity");
	$(this).parent().addClass("activity");
	$(".systemsetting_role .top .add").removeClass("hide");
	var role_id = findRoleId(this);
	//console.log(role_id);
	loadRuleManage(role_id);
});
$(role_list_panel_base + " .dv1 .compile").click(function() {
	var edit_item = $(this).siblings(".edit_item_text");
	if(edit_item.length > 0) {
		console.log(edit_item);
		edit_item.focus();
		return;
	}
	var edit_item_text = $(this).siblings(".role_name").text();
	$(this).siblings(".role_name").addClass("hide");
	$(this).addClass("hide");
	$(this).siblings(".del").addClass("hide");
	var edit_html = '<input type="text" class="item_text edit_item_text" value="' + edit_item_text + '"/>' +
		'<i class="fa fa-check item_btn item_check edit_item_check"></i>' +
		'<i class="fa fa-remove item_btn item_remove edit_item_remove"></i>';
	$(this).parent().append(edit_html);
});
$(role_item_list_panel).on('click', ".role_item .edit_item_check", function() {
	var edit_role_name = $(this).siblings(".edit_item_text").val();
	console.log(edit_role_name);
	if(!edit_role_name) {
		return;
	}
	var role_id = findRoleId(this);
	console.log(role_id);
	$.ajax({
		url: '/systemsetting/role/editRole',
		type: 'post',
		data: "role_id=" + role_id + "&role_name=" + edit_role_name,
		dataType: "json",
		success: function(data) {
			//console.log(data);
			layer.msg(data.message, {
				icon: data.status == 1 ? 1 : 2
			});
			if(data.status) {
				loadPage("/systemsetting/role/index", "role-managementfr");
			}
		},
		error: function() {
			layer.msg('编辑职位名时发生错误!', {
				icon: 2
			});
		}
	});
});

$(role_item_list_panel).on('keydown', ".item_text", function(event) {
	if(event.keyCode == 13) {
		var edit_flag = $(this).hasClass('edit_item_text');
		//编辑
		if(edit_flag) {
			var edit_role_name = $(this).val();
			console.log(edit_role_name);
			if(!edit_role_name) {
				return;
			}
			var role_id = findRoleId(this);
			console.log(role_id);
			$.ajax({
				url: '/systemsetting/role/editRole',
				type: 'post',
				data: "role_id=" + role_id + "&role_name=" + edit_role_name,
				dataType: "json",
				success: function(data) {
					//console.log(data);
					layer.msg(data.message, {
						icon: data.status == 1 ? 1 : 2
					});
					if(data.status) {
						loadPage("/systemsetting/role/index", "role-managementfr");
					}
				},
				error: function() {
					layer.msg('编辑职位名时发生错误!', {
						icon: 2
					});
				}
			});
		}
		var add_flag = $(this).hasClass('add_item_text');
		//添加
		if(add_flag) {
			var add_role_name = $(this).val();
			console.log(add_role_name);
			if(!add_role_name) {
				return;
			}
			$.ajax({
				url: '/systemsetting/role/addRole',
				type: 'post',
				data: "role_name=" + add_role_name,
				dataType: "json",
				success: function(data) {
					//console.log(data);
					layer.msg(data.message, {
						icon: data.status == 1 ? 1 : 2
					});
					if(data.status) {
						loadPage("/systemsetting/role/index", "role-managementfr");
					}
				},
				error: function() {
					layer.msg('添加职位时发生错误!', {
						icon: 2
					});
				}
			});
		}
	}
});

$(role_item_list_panel).on('click', ".role_item .edit_item_remove", function() {
	$(this).siblings(".role_name").removeClass("hide");
	$(this).siblings(".compile").removeClass("hide");
	$(this).siblings(".del").removeClass("hide");
	$(this).siblings(".edit_item_text").remove();
	$(this).siblings(".edit_item_check").remove();
	$(this).remove();
});
$(".systemsetting_role .content .dv1 .del").click(function() {
	if(confirm("你确定要删除该职位吗?")!=true){
		return;
	}
	var role_id = findRoleId(this);
	//console.log(role_id);
	$.ajax({
		url: '/systemsetting/role/deleteRole',
		type: 'post',
		data: "role_id=" + role_id,
		dataType: "json",
		success: function(data) {
			//console.log(data);
			layer.msg(data.message, {
				icon: data.status == 1 ? 1 : 2
			});
			if(data.status) {
				loadPage("/systemsetting/role/index", "role-managementfr");
			}
		},
		error: function() {
			layer.msg('删除职位时发生错误!', {
				icon: 2
			});
		}
	});
});

function role_list_employee_show(id) {
	var url = "/systemsetting/role/employee_show/id/" + id;
	var panel = 'role-managementfr .systemsetting_role .employee_info';
	$.ajax({
		url: url,
		type: 'get',
		async: false,
		success: function(data) {
			$('#frames #' + panel).html(data);
			$('#frames #' + panel).height(window.innerHeight);
			$('#frames #' + panel).removeClass("hide");
			$('#frames #' + panel + " .firNav .current").click(function() {
				$('#frames #' + panel).addClass("hide");
			});
		},
		error: function() {
			layer.msg('获取员工信息失败!', {
				icon: 2
			});
		}
	});
}

function role_list_employee_del(role_id, user_id) {
	var url = "/systemsetting/role/deleteRoleMember/";
	$.ajax({
		url: url,
		type: 'post',
		async: false,
		dataType: "json",
		data: "role_id=" + role_id + "&user_id=" + user_id,
		success: function(data) {
			layer.msg(data.message, {
				icon: data.status == 1 ? 1 : 2
			});
			if(data.status) {
				loadRoleEmployeeTable(role_id);
			}
		},
		error: function() {
			layer.msg('移除员工职位失败!', {
				icon: 2
			});
		}
	});
}