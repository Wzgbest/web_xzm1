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