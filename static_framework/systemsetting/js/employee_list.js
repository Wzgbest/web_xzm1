function employee_list_listNumChange(){
	//console.log($(".employee_list .u-tabControlRow select").val());
	var num = $(".employee_list .u-tabControlRow select").val();
	employee_list_change_page(1,num);
}
function employee_list_previous_page(p,num){
	if(p-1<1){
		return;
	}
	employee_list_change_page(p-1,num);
}
function employee_list_next_page(p,num,max){
	if(p+1>max){
		return;
	}
	employee_list_change_page(p+1,num);
}
function employee_list_jump_page(num,max){
	console.log($(".employee_list_jump_page").val());
	var p = $(".employee_list_jump_page").val();
	if(p+1>max || p-1<1){
		return;
	}
	employee_list_change_page(p,num);
}
function employee_list_change_page(p,num){
	loadPage(get_employee_list_url(p,num),"staff-managementfr");
}
function employee_list_search(p,num){
	var url = get_employee_list_url(p,num);
	var take_type = $("#employee_list_search_take_type").val();
	if(take_type!=""){
		url += "/take_type/"+take_type;
	}
	var grade = $("#employee_list_search_grade").val();
	if(grade!=""){
		url += "/grade/"+grade;
	}
	var sale_chance = $("#employee_list_search_sale_chance").val();
	if(sale_chance!=""){
		url += "/sale_chance/"+sale_chance;
	}
	var comm_status = $("#employee_list_search_comm_status").val();
	if(comm_status!=""){
		url += "/comm_status/"+comm_status;
	}
	var employee_name = $("#employee_list_search_employee_name").val();
	if(employee_name!=""){
		url += "/employee_name/"+employee_name;
	}
	var contact_name = $("#employee_list_search_contact_name").val();
	if(contact_name!=""){
		url += "/contact_name/"+contact_name;
	}
	loadPage(url,"staff-managementfr");
}
function get_employee_list_url(p,num){
	return "/systemsetting/employee/manage/p/"+p+"/num/"+num;
}
function employee_list_del(ids,p,num){
	$.ajax({
		url: '/systemsetting/employee/deleteMultipleEmployee',
		type: 'post',
		data: "ids="+ids,
		success: function(data) {
			//console.log(data);
			alert(data.message);
			if(data.status) {
				employee_list_search(p,num);
			}
		},
		error: function() {
			alert("删除员工信息时发生错误!");
		},
	});
}

var employee_list_hide_panel = 'staff-managementfr .sys_employee_list .employee_list_panel';
function employee_list_show(id){
	var url = "/systemsetting/employee/show.html?s=/id/"+id+"/fr/employee_list";
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
	var url = "/systemsetting/employee/edit.html?s=/id/"+id+"/fr/employee_list";
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
		url: '/systemsetting/employee/editEmployee',
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
