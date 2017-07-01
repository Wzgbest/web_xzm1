var anonymous_pool_hide_panel = 'high-seafr .crm_anonymous_pool .anonymous_pool_panel';
function anonymous_pool_list_show_list(){
	$('#frames #'+anonymous_pool_hide_panel).addClass("hide");
	$('#frames #high-seafr .crm_anonymous_pool .anonymous_pool').removeClass("hide");
}
function anonymous_pool_general(id){
	var url = "/crm/customer/general/id/"+id+"/fr/anonymous_pool";
	var panel = 'high-seafr .crm_anonymous_pool .customer_general';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+anonymous_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户概要失败!");
		}
	});
}
function anonymous_pool_show(id){
	var url = "/crm/customer/show/id/"+id+"/fr/anonymous_pool";
	var panel = 'high-seafr .crm_anonymous_pool .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+anonymous_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function anonymous_pool_edit(id){
	var url = "/crm/customer/edit/id/"+id+"/fr/anonymous_pool";
	var panel = 'high-seafr .crm_anonymous_pool .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+anonymous_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function anonymous_pool_edit_update(id){
	var anonymous_pool_edit_from_data = $(".anonymous_pool_edit").serialize();
	anonymous_pool_edit_from_data += "&id="+id+"&fr=anonymous_pool";
	console.log(anonymous_pool_edit_from_data);
	$.ajax({
		url: '/crm/customer/update',
		type: 'post',
		data: anonymous_pool_edit_from_data,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				anonymous_pool_show(id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function anonymous_pool_contact_show(id){
	var url = "/crm/customer_contact/show/customer_id/"+id+"/fr/anonymous_pool";
	var panel = 'high-seafr .crm_anonymous_pool .customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+anonymous_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取联系人失败!");
		}
	});
}
function anonymous_pool_contact_add(customer_id){
	var url = "/crm/customer_contact/add_page/customer_id/"+customer_id+"/fr/anonymous_pool";
	var panel = 'high-seafr .crm_anonymous_pool .customer_contact .new_customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('.anonymous_pool_contact_add_panel').html(data);
			$('.anonymous_pool_contact_add_panel').removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function anonymous_pool_contact_add_send(customer_id){
	var anonymous_pool_contact_add_from = $(".anonymous_pool_contact_add_from").serialize();
	anonymous_pool_contact_add_from += "&customer_id="+customer_id+"&fr=anonymous_pool";
	console.log(anonymous_pool_contact_add_from);
	$.ajax({
		url: '/crm/customer_contact/add',
		type: 'post',
		data: anonymous_pool_contact_add_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				anonymous_pool_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function anonymous_pool_contact_edit(id){
	console.log(id);
	var url = "/crm/customer_contact/edit_page/id/"+id+"/fr/anonymous_pool";
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			var html = '<div class="anonymous_pool_contact_edit_panel">';
			html+= data;
			html+= '</div>';
			$('.anonymous_pool_contact_'+id).addClass("hide");
			$('.anonymous_pool_contact_'+id).before(html);
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function anonymous_pool_contact_edit_update(id,customer_id){
	var anonymous_pool_contact_edit_from = $(".anonymous_pool_contact_edit_from").serialize();
	anonymous_pool_contact_edit_from += "&id="+id+"&fr=anonymous_pool";
	console.log(anonymous_pool_contact_edit_from);
	$.ajax({
		url: '/crm/customer_contact/update',
		type: 'post',
		data: anonymous_pool_contact_edit_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				anonymous_pool_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function anonymous_pool_sale_chance_show(customer_id){
	var url = "/crm/sale_chance/show/customer_id/"+customer_id+"/fr/anonymous_pool";
	var panel = 'high-seafr .crm_anonymous_pool .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+anonymous_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取销售机会失败!");
		}
	});
}
function anonymous_pool_trace_show(customer_id){
	var url = "/crm/customer_trace/show/customer_id/"+customer_id+"/fr/anonymous_pool";
	var panel = 'high-seafr .crm_anonymous_pool .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+anonymous_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户跟踪信息失败!");
		}
	});
}
/***************************/
$(".blackBg").height(window.innerHeight);
/*****************************************************************/
/*新建*/
function anonymous_pool_newClient(){
	document.getElementById("anonymous_pool_newClient").classList.remove("hide");
	document.getElementById("anonymous_pool_blackBg").classList.remove("hide");
//	document.getElementsByTagName("body")[0].classList.add("hiddenY");
}
function anonymous_pool_removeNewClient(){
	document.getElementById("anonymous_pool_newClient").classList.add("hide");
	document.getElementById("anonymous_pool_blackBg").classList.add("hide");
	anonymous_pool_new_customer();
}

function anonymous_pool_check_form_html5(eles){
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
var anonymous_pool_new_customer_id = 0;
var anonymous_pool_new_customer_contact_id = 0;
var anonymous_pool_new_customer_sale_chance_id = 0;
function anonymous_pool_add_customer(next_status){
	if(!anonymous_pool_check_form_html5($("#anonymous_pool_newClientForm").get(0).elements)){
		return;
	}
	var anonymous_pool_add_customer_from_data = $("#anonymous_pool_newClientForm").serialize();
	var url = '/crm/customer/add';
	if(anonymous_pool_new_customer_id>0){
		url = '/crm/customer/update';
		anonymous_pool_add_customer_from_data += "&id="+anonymous_pool_new_customer_id;
	}
	//console.log(anonymous_pool_add_customer_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: anonymous_pool_add_customer_from_data,
		success: function(data) {
			if(data.status) {
				if(anonymous_pool_new_customer_id==0) {
					anonymous_pool_new_customer_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert(data.info);
				}
				anonymous_pool_next_status(next_status,anonymous_pool_removeNewClient,anonymous_pool_next_contact);
			}else{
				alert(data.info);
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function anonymous_pool_add_contact(next_status){
	var anonymous_pool_add_customer_contact_from_data = $("#anonymous_pool_newClientContactForm").serialize();
	anonymous_pool_add_customer_contact_from_data += "&customer_id="+anonymous_pool_new_customer_id;
	var url = '/crm/customer_contact/add';
	if(anonymous_pool_new_customer_contact_id>0){
		url = '/crm/customer_contact/update';
		anonymous_pool_add_customer_contact_from_data += "&id="+anonymous_pool_new_customer_contact_id;
	}
	//console.log(anonymous_pool_add_customer_contact_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: anonymous_pool_add_customer_contact_from_data,
		success: function(data) {
			if(data.status) {
				if(anonymous_pool_new_customer_contact_id==0) {
					anonymous_pool_new_customer_contact_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert("保存成功!");
				}
				anonymous_pool_next_status(next_status,anonymous_pool_pre_customer,anonymous_pool_next_sale_chance);
			}else{
				alert(data.info);
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function anonymous_pool_add_sale_chance(next_status){
	var anonymous_pool_add_customer_sale_chance_from_data = $("#anonymous_pool_newClientSaleChanceForm").serialize();
	anonymous_pool_add_customer_sale_chance_from_data += "&customer_id="+anonymous_pool_new_customer_id;
	var url = '/crm/sale_chance/add';
	if(anonymous_pool_new_customer_sale_chance_id>0){
		url = '/crm/sale_chance/update';
		anonymous_pool_add_customer_sale_chance_from_data += "&id="+anonymous_pool_new_customer_sale_chance_id;
	}
	//console.log(anonymous_pool_add_customer_sale_chance_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: anonymous_pool_add_customer_sale_chance_from_data,
		success: function(data) {
			if(data.status) {
				if(anonymous_pool_new_customer_sale_chance_id==0) {
					anonymous_pool_new_customer_sale_chance_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert("保存成功!");
				}
				anonymous_pool_next_status(next_status,anonymous_pool_pre_contact,anonymous_pool_removeNewClient);
			}else{
				alert(data.info);
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
//next_status 0:新建;1:上一页;2:下一页;3:退出;
//func_previous 上一页的方法
//func_next 下一页的方法
function anonymous_pool_next_status(next_status,func_previous,func_next){
	if(!next_status){
		anonymous_pool_new_customer();
	}else if(next_status==1){
		func_previous();
	}else if(next_status==2){
		func_next();
	}else if(next_status==3){
		anonymous_pool_removeNewClient();
	}
}
function anonymous_pool_new_customer(){
	console.log("new_start");

	anonymous_pool_new_customer_id = 0;
	anonymous_pool_new_customer_contact_id = 0;
	anonymous_pool_new_customer_sale_chance_id = 0;

	$("#anonymous_pool_newClientForm :text").val("");
	$("#anonymous_pool_newClientContactForm :text").val("");
	$("#anonymous_pool_newClientSaleChanceForm :text").val("");

	$("#anonymous_pool_newClientForm textarea").val("");
	$("#anonymous_pool_newClientContactForm textarea").val("");
	$("#anonymous_pool_newClientSaleChanceForm textarea").val("");

	$("#anonymous_pool_newClientForm").removeClass("hide");
	$("#anonymous_pool_newClientContactForm").addClass("hide");
	$("#anonymous_pool_newClientSaleChanceForm").addClass("hide");


	$("#anonymous_pool_newClientForm select").find("option:first").removeAttr("selected",true);
	$("#anonymous_pool_newClientContactForm select").find("option:first").removeAttr("selected",true);
	$("#anonymous_pool_newClientSaleChanceForm select").find("option:first").removeAttr("selected",true);

	$("#anonymous_pool_newClientForm select").find("option:first").attr("selected",true);
	$("#anonymous_pool_newClientContactForm select").find("option:first").attr("selected",true);
	$("#anonymous_pool_newClientSaleChanceForm select").find("option:first").attr("selected",true);

	$("#anonymous_pool_newClientForm :radio[name='belongs_to'][value='3']").attr("checked",true);
	$("#anonymous_pool_newClientContactForm :radio[name='sex'][value='3']").attr("checked",true);
	$("#anonymous_pool_newClientContactForm :radio[name='key_decide'][value='3']").attr("checked",true);

	console.log("new_end");
}
/*新建翻页*/
function anonymous_pool_next_contact(){
	$("#form1").addClass("hide");
	$("#form2").removeClass("hide");
}
function anonymous_pool_next_sale_chance(){
	$("#form2").addClass("hide");
	$("#form3").removeClass("hide");
}
function anonymous_pool_pre_customer(){
	$("#form2").addClass("hide");
	$("#form1").removeClass("hide");
}
function anonymous_pool_pre_contact(){
	$("#form3").addClass("hide");
	$("#form2").removeClass("hide");
}