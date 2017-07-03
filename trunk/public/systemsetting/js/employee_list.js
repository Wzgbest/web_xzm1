function employee_list_del(ids){
	$.ajax({
		url: '/systemsetting/employee/deleteMultipleEmployee.html',
		type: 'post',
		data: "ids="+ids,
		success: function(data) {
			//console.log(data);
			alert(data.message);
			if(data.status) {
				employee_list_list_manage.reload_list();
			}
		},
		error: function() {
			alert("删除员工信息时发生错误!");
		}
	});
}
var employee_list_nav_base = "#staff-managementfr .employee_list .m-secNav";
employee_list_list_manage.listenSelect("exportCustomer");
$(employee_list_nav_base+" .exportCustomer").click(function(){
	var ids = employee_list_list_manage.getAllSelectVal(" ",",");
	if(ids==""){
		return;
	}
	console.log(ids);
	window.open("/systemsetting/employee_import/exportEmployee/ids/"+ids);
	// $.ajax({
	// 	url: '/systemsetting/employee_import/exportEmployee',
	// 	type: 'post',
	// 	data: "ids="+ids,
	// 	success: function(data) {
	// 		//console.log(data);
	// 		alert(data.info);
	// 		if(data.status) {
	// 			employee_list_list_manage.reload_list();
	// 		}
	// 	},
	// 	error: function() {
	// 		alert("导出员工时发生错误!");
	// 	}
	// });
});
employee_list_list_manage.listenSelect("delete");
$(employee_list_nav_base+" .delete").click(function(){
	var ids = employee_list_list_manage.getAllSelectVal();
	if(ids==""){
		return;
	}
	//console.log(ids);
	$.ajax({
		url: '/systemsetting/employee/del',
		type: 'post',
		data: ids,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				employee_list_list_manage.reload_list();
			}
		},
		error: function() {
			alert("删除员工时发生错误!");
		}
	});
});

var employee_list_hide_panel = 'staff-managementfr .sys_employee_list .employee_list_panel';
function employee_list_show(id){
	var url = "/systemsetting/employee/show/id/"+id+"/fr/employee_list";
	var panel = 'staff-managementfr .sys_employee_list .employee_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+employee_list_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取员工信息失败!");
		}
	});
}
function employee_list_edit(id){
	var url = "/systemsetting/employee/edit/id/"+id+"/fr/employee_list";
	var panel = 'staff-managementfr .sys_employee_list .employee_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+employee_list_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取员工信息失败!");
		}
	});
}
function employee_list_edit_update(id){
	var employee_list_edit_from_data = $(".employee_list_edit_from").serialize();
	employee_list_edit_from_data += "&user_id="+id;
	console.log(employee_list_edit_from_data);
	$.ajax({
		url: '/systemsetting/employee/editemployee.html',
		type: 'post',
		data: employee_list_edit_from_data,
		success: function(data) {
			//console.log(data);
			alert(data.message);
			if(data.status) {
				employee_list_show(id);
			}
		},
		error: function() {
			alert("保存员工信息时发生错误!");
		},
	});
}
function employee_list_show_list(){
	$('#frames #'+employee_list_hide_panel).addClass("hide");
	$('#frames #staff-managementfr .sys_employee_list .employee_list').removeClass("hide");
}
/***************************/
$(".blackBg").height(window.innerHeight);
/*****************************************************************/
/*新建*/
function employee_list_newClient(){
	document.getElementById("employee_list_newClient").classList.remove("hide");
	document.getElementById("employee_list_blackBg").classList.remove("hide");
//	document.getElementsByTagName("body")[0].classList.add("hiddenY");
}
function employee_list_removeNewClient(){
	document.getElementById("employee_list_newClient").classList.add("hide");
	document.getElementById("employee_list_blackBg").classList.add("hide");
	employee_list_new_employee();
}

function employee_list_check_form_html5(eles){
	var ele;
	for(var i = 0;i<eles.length;i++){
		ele = eles[i];
		if(ele.name){
			if(!ele.checkValidity()){
				ele.focus();
				return false;
			}
		}
	}
	return true;
}
function employee_list_add_employee(){
	if(!employee_list_check_form_html5($("#employee_list_newClientForm").get(0).elements)){
		return;
	}
	var employee_list_add_employee_from_data = $("#employee_list_newClientForm").serialize();
	var url = '/systemsetting/employee/addEmployee';
	//console.log(employee_list_add_employee_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: employee_list_add_employee_from_data,
		success: function(data) {
			alert(data.message);
			if(data.status) {
				employee_list_removeNewClient();
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function employee_list_new_employee(){
	console.log("new_start");
	$("#employee_list_newClientForm :text").val("");
	$("#employee_list_newClientForm textarea").val("");
	$("#employee_list_newClientForm").removeClass("hide");
	$("#employee_list_newClientForm select").find("option:first").removeAttr("selected",true);
	$("#employee_list_newClientForm select").find("option:first").attr("selected",true);
	console.log("new_end");
}