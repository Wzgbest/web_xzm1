function customer_manage_listNumChange(){
	//console.log($(".u-tabControlRow select").val());
	var num = $(".customer_manage .u-tabControlRow select").val();
	customer_manage_change_page(1,num);
}
function customer_manage_previous_page(p,num){
	if(p-1<1){
		return;
	}
	customer_manage_change_page(p-1,num);
}
function customer_manage_next_page(p,num,max){
	if(p+1>max){
		return;
	}
	customer_manage_change_page(p+1,num);
}
function customer_manage_jump_page(num,max){
	console.log($(".customer_manage_jump_page").val());
	var p = $(".customer_manage_jump_page").val();
	if(p+1>max || p-1<1){
		return;
	}
	customer_manage_change_page(p,num);
}
function customer_manage_change_page(p,num){
	loadPage(get_customer_manage_url(p,num),"cilents-managefr");
}
function customer_manage_search(p,num){
	var url = get_customer_manage_url(p,num);
	var take_type = $("#customer_manage_search_take_type").val();
	if(take_type!=""){
		url += "/take_type/"+take_type;
	}
	var grade = $("#customer_manage_search_grade").val();
	if(grade!=""){
		url += "/grade/"+grade;
	}
	var sale_chance = $("#customer_manage_search_sale_chance").val();
	if(sale_chance!=""){
		url += "/sale_chance/"+sale_chance;
	}
	var comm_status = $("#customer_manage_search_comm_status").val();
	if(comm_status!=""){
		url += "/comm_status/"+comm_status;
	}
	var customer_name = $("#customer_manage_search_customer_name").val();
	if(customer_name!=""){
		url += "/customer_name/"+customer_name;
	}
	var contact_name = $("#customer_manage_search_contact_name").val();
	if(contact_name!=""){
		url += "/contact_name/"+contact_name;
	}
	loadPage(url,"cilents-managefr");
}
function get_customer_manage_url(p,num){
	return "/crm/customer/customer_manage/p/"+p+"/num/"+num;
}

var customer_manage_hide_panel = 'cilents-managefr .crm_customer_manage .customer_manage_panel';
function customer_manage_list_show_list(){
	$('#frames #'+customer_manage_hide_panel).addClass("hide");
	$('#frames #cilents-managefr .crm_customer_manage .customer_manage').removeClass("hide");
}
function customer_manage_general(id){
	var url = "/crm/customer/general/id/"+id+"/fr/customer_manage";
	var panel = 'cilents-managefr .crm_customer_manage .customer_general';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+customer_manage_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户概要失败!");
		}
	});
}
function customer_manage_show(id){
	var url = "/crm/customer/show/id/"+id+"/fr/customer_manage";
	var panel = 'cilents-managefr .crm_customer_manage .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+customer_manage_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function customer_manage_edit(id){
	var url = "/crm/customer/edit/id/"+id+"/fr/customer_manage";
	var panel = 'cilents-managefr .crm_customer_manage .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+customer_manage_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function customer_manage_edit_update(id){
	var customer_manage_edit_from_data = $(".customer_manage_edit").serialize();
	customer_manage_edit_from_data += "&id="+id+"&fr=customer_manage";
	console.log(customer_manage_edit_from_data);
	$.ajax({
		url: '/crm/customer/update',
		type: 'post',
		data: customer_manage_edit_from_data,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				customer_manage_show(id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function customer_manage_contact_show(id){
	var url = "/crm/customer_contact/show/customer_id/"+id+"/fr/customer_manage";
	var panel = 'cilents-managefr .crm_customer_manage .customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+customer_manage_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取联系人失败!");
		}
	});
}
function customer_manage_contact_add(customer_id){
	var url = "/crm/customer_contact/add_page/customer_id/"+customer_id+"/fr/customer_manage";
	var panel = 'cilents-managefr .crm_customer_manage .customer_contact .new_customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('.customer_manage_contact_add_panel').html(data);
			$('.customer_manage_contact_add_panel').removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function customer_manage_contact_add_send(customer_id){
	var customer_manage_contact_add_from = $(".customer_manage_contact_add_from").serialize();
	customer_manage_contact_add_from += "&customer_id="+customer_id+"&fr=customer_manage";
	console.log(customer_manage_contact_add_from);
	$.ajax({
		url: '/crm/customer_contact/add',
		type: 'post',
		data: customer_manage_contact_add_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				customer_manage_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function customer_manage_contact_edit(id){
	console.log(id);
	var url = "/crm/customer_contact/edit_page/id/"+id+"/fr/customer_manage";
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			var html = '<div class="customer_manage_contact_edit_panel">';
			html+= data;
			html+= '</div>';
			$('.customer_manage_contact_'+id).addClass("hide");
			$('.customer_manage_contact_'+id).before(html);
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function customer_manage_contact_edit_update(id,customer_id){
	var customer_manage_contact_edit_from = $(".customer_manage_contact_edit_from").serialize();
	customer_manage_contact_edit_from += "&id="+id+"&fr=customer_manage";
	console.log(customer_manage_contact_edit_from);
	$.ajax({
		url: '/crm/customer_contact/update',
		type: 'post',
		data: customer_manage_contact_edit_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				customer_manage_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function customer_manage_sale_chance_show(customer_id){
	var url = "/crm/sale_chance/show/customer_id/"+customer_id+"/fr/customer_manage";
	var panel = 'cilents-managefr .crm_customer_manage .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+customer_manage_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取销售机会失败!");
		}
	});
}
function customer_manage_trace_show(customer_id){
	var url = "/crm/customer_trace/show/customer_id/"+customer_id+"/fr/customer_manage";
	var panel = 'cilents-managefr .crm_customer_manage .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+customer_manage_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户跟踪信息失败!");
		}
	});
}
/***************************/
$("#blackBg").height(window.innerHeight);
/*****************************************************************/