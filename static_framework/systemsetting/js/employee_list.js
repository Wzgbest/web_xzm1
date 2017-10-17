var employee_list_del_ids = "";
var employee_list_base = "#staff-managementfr .employee_list";
var employee_list_nav_base = employee_list_base+" .m-secNav";
var employee_list_edit_hide_flg = 0;
var employee_list_hide_panel = 'staff-managementfr .sys_employee_list .employee_list_panel';

function employee_list_del(ids){
	employee_list_del_ids = "ids[]="+ids;
	$(employee_list_base+" .employee_delete_ui").reveal("{data-animation:'fade'}");
}

$("#frames #staff-managementfr .sys_employee_list .employee_import_record .m-firNav .current").click(function(){
	$('#frames #'+employee_list_hide_panel).addClass("hide");
	$('#frames #staff-managementfr .sys_employee_list .employee_list').removeClass("hide");
});
$(employee_list_nav_base+" .employee_import").click(function(){
	var url = "/systemsetting/employee_import/index/";
	var panel = 'staff-managementfr .sys_employee_list .employee_import_record_list';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+employee_list_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #staff-managementfr .sys_employee_list .employee_import_record').removeClass("hide");
		},
		error:function(){
			alert("获取导入员工信息失败!");
		}
	});
});

$(employee_list_base+" .employee_import_ui .employee_import_templet_download").click(function(){
	window.open("/download/templet/Employee.xlsx");
});
$(employee_list_base+" #employee_import_iframe").load(function() {
	var body = $(window.frames['employee_import_iframe'].document.body);
	//console.log(body);
	var upload_data = JSON.parse(body[0].textContent);
	//console.log(upload_data);
	if(upload_data==null){
		alert("上传文件时发生错误!");
	}
	$(employee_list_base+" .employee_import_ui").trigger('reveal:close');
	if (upload_data.status == "1") {
		var file_id = upload_data.data[0].id;
		$.ajax({
			url: '/systemsetting/employee_import/importEmployee',
			type: 'post',
			data: "file_id="+file_id,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					employee_list_list_manage.reload_list();
				}
			},
			error: function() {
				alert("导入员工时发生错误!");
			}
		});
	}else{
		alert(data.info);
	}
});
$(employee_list_base+" .employee_import_ui .employee_import_cancel_btn").click(function(){
	$(employee_list_base+" .employee_import_ui").trigger('reveal:close');
});

employee_list_list_manage.listenSelect("exportEmployee");
$(employee_list_nav_base+" .exportEmployee").click(function(){
	var ids = employee_list_list_manage.getAllSelectVal(" ",",");
	if(ids==""){
		return;
	}
	//console.log(ids);
	$(employee_list_base+" .exportEmployeeUI").reveal("{data-animation:'fade'}");
});
$(employee_list_base+" .exportEmployeeUI .exportEmployeeUIOkBtn").click(function(){
	$(employee_list_base+" .exportEmployeeUI").trigger('reveal:close');
	var ids = employee_list_list_manage.getAllSelectVal(" ",",");
	if(ids==""){
		return;
	}
	//console.log(ids);
	window.open("/systemsetting/employee_import/exportEmployee/ids/"+ids);
});
$(employee_list_base+" .exportEmployeeUI .exportEmployeeUICancelBtn").click(function(){
	$(employee_list_base+" .exportEmployeeUI").trigger('reveal:close');
});

employee_list_list_manage.listenSelect("delete");
$(employee_list_nav_base+" .delete").click(function(){
	var ids = employee_list_list_manage.getAllSelectVal();
	if(ids==""){
		return;
	}
	employee_list_del_ids = ids;
	//console.log(ids);
	$(employee_list_base+" .employee_delete_ui").reveal("{data-animation:'fade'}");
});
$(employee_list_base+" .employee_delete_ui .employee_delete_ok_btn").click(function(){
	$(employee_list_base+" .employee_delete_ui").trigger('reveal:close');
	var ids = employee_list_del_ids;
	if(ids==""){
		return;
	}
	//console.log(ids);
	$.ajax({
		url: '/systemsetting/employee/deleteMultipleEmployee',
		type: 'post',
		data: ids,
		dataType: 'json',
		success: function(data) {
			//console.log(data);
			alert(data.message);
			if(data.status) {
				employee_list_list_manage.reload_list();
			}
		},
		error: function() {
			alert("删除员工时发生错误!");
		}
	});
});
$(employee_list_base+" .employee_delete_ui .employee_delete_cancel_btn").click(function(){
	$(employee_list_base+" .employee_delete_ui").trigger('reveal:close');
});

function employee_list_panel_close(id){
	if(employee_list_edit_hide_flg){
		employee_list_show(id);
	}else{
		$('#frames #'+employee_list_hide_panel).addClass("hide");
		$('#frames #staff-managementfr .sys_employee_list .employee_list').removeClass("hide");
	}
}
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
function employee_list_edit(id,status){
	var url = "/systemsetting/employee/edit/id/"+id+"/fr/employee_list";
	var panel = 'staff-managementfr .sys_employee_list .employee_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			employee_list_edit_hide_flg = status;
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
	//console.log(employee_list_edit_from_data);
	$.ajax({
		url: '/systemsetting/employee/editemployee.html',
		type: 'post',
		data: employee_list_edit_from_data,
		dataType: 'json',
		success: function(data) {
			//console.log(data);
			alert(data.message);
			if(data.status) {
				employee_list_list_manage.reload_list();
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
/*新建*/
function employee_list_newClient(){
	$("#frames #staff-managementfr .sys_employee_list .blackBg").height(window.innerHeight);
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
		dataType: 'json',
		success: function(data) {
			alert(data.message);
			if(data.status) {
				employee_list_list_manage.reload_list();
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function employee_list_new_employee(){
	//console.log("new_start");
	$("#employee_list_newClientForm :text").val("");
	$("#employee_list_newClientForm textarea").val("");
	$("#employee_list_newClientForm").removeClass("hide");
	$("#employee_list_newClientForm select").find("option:first").removeAttr("selected",true);
	$("#employee_list_newClientForm select").find("option:first").attr("selected",true);
	//console.log("new_end");
}