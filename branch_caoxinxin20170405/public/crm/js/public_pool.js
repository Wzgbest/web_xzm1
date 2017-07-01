var public_pool_hide_panel = 'high-seafr .crm_public_pool .public_pool_panel';
function public_pool_list_show_list(){
	$('#frames #'+public_pool_hide_panel).addClass("hide");
	$('#frames #high-seafr .crm_public_pool .public_pool').removeClass("hide");
}
function public_pool_general(id){
	var url = "/crm/customer/general/id/"+id+"/fr/public_pool";
	var panel = 'high-seafr .crm_public_pool .customer_general';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+public_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户概要失败!");
		}
	});
}
function public_pool_show(id){
	var url = "/crm/customer/show/id/"+id+"/fr/public_pool";
	var panel = 'high-seafr .crm_public_pool .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+public_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function public_pool_edit(id){
	var url = "/crm/customer/edit/id/"+id+"/fr/public_pool";
	var panel = 'high-seafr .crm_public_pool .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+public_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function public_pool_edit_update(id){
	var public_pool_edit_from_data = $(".public_pool_edit").serialize();
	public_pool_edit_from_data += "&id="+id+"&fr=public_pool";
	console.log(public_pool_edit_from_data);
	$.ajax({
		url: '/crm/customer/update',
		type: 'post',
		data: public_pool_edit_from_data,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				public_pool_show(id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function public_pool_contact_show(id){
	var url = "/crm/customer_contact/show/customer_id/"+id+"/fr/public_pool";
	var panel = 'high-seafr .crm_public_pool .customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+public_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取联系人失败!");
		}
	});
}
function public_pool_contact_add(customer_id){
	var url = "/crm/customer_contact/add_page/customer_id/"+customer_id+"/fr/public_pool";
	var panel = 'high-seafr .crm_public_pool .customer_contact .new_customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('.public_pool_contact_add_panel').html(data);
			$('.public_pool_contact_add_panel').removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function public_pool_contact_add_send(customer_id){
	var public_pool_contact_add_from = $(".public_pool_contact_add_from").serialize();
	public_pool_contact_add_from += "&customer_id="+customer_id+"&fr=public_pool";
	console.log(public_pool_contact_add_from);
	$.ajax({
		url: '/crm/customer_contact/add',
		type: 'post',
		data: public_pool_contact_add_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				public_pool_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function public_pool_contact_edit(id){
	console.log(id);
	var url = "/crm/customer_contact/edit_page/id/"+id+"/fr/public_pool";
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			var html = '<div class="public_pool_contact_edit_panel">';
			html+= data;
			html+= '</div>';
			$('.public_pool_contact_'+id).addClass("hide");
			$('.public_pool_contact_'+id).before(html);
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function public_pool_contact_edit_update(id,customer_id){
	var public_pool_contact_edit_from = $(".public_pool_contact_edit_from").serialize();
	public_pool_contact_edit_from += "&id="+id+"&fr=public_pool";
	console.log(public_pool_contact_edit_from);
	$.ajax({
		url: '/crm/customer_contact/update',
		type: 'post',
		data: public_pool_contact_edit_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				public_pool_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function public_pool_sale_chance_show(customer_id){
	var url = "/crm/sale_chance/show/customer_id/"+customer_id+"/fr/public_pool";
	var panel = 'high-seafr .crm_public_pool .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+public_pool_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取销售机会失败!");
		}
	});
}
function public_pool_trace_show(customer_id){
	var url = "/crm/customer_trace/show/customer_id/"+customer_id+"/fr/public_pool";
	var panel = 'high-seafr .crm_public_pool .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+public_pool_hide_panel).addClass("hide");
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
function public_pool_newClient(){
	document.getElementById("public_pool_newClient").classList.remove("hide");
	document.getElementById("public_pool_blackBg").classList.remove("hide");
//	document.getElementsByTagName("body")[0].classList.add("hiddenY");
}
function public_pool_removeNewClient(){
	document.getElementById("public_pool_newClient").classList.add("hide");
	document.getElementById("public_pool_blackBg").classList.add("hide");
	public_pool_new_customer();
}

function public_pool_check_form_html5(eles){
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
var public_pool_new_customer_id = 0;
var public_pool_new_customer_contact_id = 0;
var public_pool_new_customer_sale_chance_id = 0;
function public_pool_add_customer(next_status){
	if(!public_pool_check_form_html5($("#public_pool_newClientForm").get(0).elements)){
		return;
	}
	var public_pool_add_customer_from_data = $("#public_pool_newClientForm").serialize();
	var url = '/crm/customer/add';
	if(public_pool_new_customer_id>0){
		url = '/crm/customer/update';
		public_pool_add_customer_from_data += "&id="+public_pool_new_customer_id;
	}
	//console.log(public_pool_add_customer_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: public_pool_add_customer_from_data,
		success: function(data) {
			if(data.status) {
				if(public_pool_new_customer_id==0) {
					public_pool_new_customer_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert(data.info);
				}
				public_pool_next_status(next_status,public_pool_removeNewClient,public_pool_next_contact);
			}else{
				alert(data.info);
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function public_pool_add_contact(next_status){
	var public_pool_add_customer_contact_from_data = $("#public_pool_newClientContactForm").serialize();
	public_pool_add_customer_contact_from_data += "&customer_id="+public_pool_new_customer_id;
	var url = '/crm/customer_contact/add';
	if(public_pool_new_customer_contact_id>0){
		url = '/crm/customer_contact/update';
		public_pool_add_customer_contact_from_data += "&id="+public_pool_new_customer_contact_id;
	}
	//console.log(public_pool_add_customer_contact_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: public_pool_add_customer_contact_from_data,
		success: function(data) {
			if(data.status) {
				if(public_pool_new_customer_contact_id==0) {
					public_pool_new_customer_contact_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert("保存成功!");
				}
				public_pool_next_status(next_status,public_pool_pre_customer,public_pool_next_sale_chance);
			}else{
				alert(data.info);
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function public_pool_add_sale_chance(next_status){
	var public_pool_add_customer_sale_chance_from_data = $("#public_pool_newClientSaleChanceForm").serialize();
	public_pool_add_customer_sale_chance_from_data += "&customer_id="+public_pool_new_customer_id;
	var url = '/crm/sale_chance/add';
	if(public_pool_new_customer_sale_chance_id>0){
		url = '/crm/sale_chance/update';
		public_pool_add_customer_sale_chance_from_data += "&id="+public_pool_new_customer_sale_chance_id;
	}
	//console.log(public_pool_add_customer_sale_chance_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: public_pool_add_customer_sale_chance_from_data,
		success: function(data) {
			if(data.status) {
				if(public_pool_new_customer_sale_chance_id==0) {
					public_pool_new_customer_sale_chance_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert("保存成功!");
				}
				public_pool_next_status(next_status,public_pool_pre_contact,public_pool_removeNewClient);
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
function public_pool_next_status(next_status,func_previous,func_next){
	if(!next_status){
		public_pool_new_customer();
	}else if(next_status==1){
		func_previous();
	}else if(next_status==2){
		func_next();
	}else if(next_status==3){
		public_pool_removeNewClient();
	}
}
function public_pool_new_customer(){
	console.log("new_start");

	public_pool_new_customer_id = 0;
	public_pool_new_customer_contact_id = 0;
	public_pool_new_customer_sale_chance_id = 0;

	$("#public_pool_newClientForm :text").val("");
	$("#public_pool_newClientContactForm :text").val("");
	$("#public_pool_newClientSaleChanceForm :text").val("");

	$("#public_pool_newClientForm textarea").val("");
	$("#public_pool_newClientContactForm textarea").val("");
	$("#public_pool_newClientSaleChanceForm textarea").val("");

	$("#public_pool_newClientForm").removeClass("hide");
	$("#public_pool_newClientContactForm").addClass("hide");
	$("#public_pool_newClientSaleChanceForm").addClass("hide");


	$("#public_pool_newClientForm select").find("option:first").removeAttr("selected",true);
	$("#public_pool_newClientContactForm select").find("option:first").removeAttr("selected",true);
	$("#public_pool_newClientSaleChanceForm select").find("option:first").removeAttr("selected",true);

	$("#public_pool_newClientForm select").find("option:first").attr("selected",true);
	$("#public_pool_newClientContactForm select").find("option:first").attr("selected",true);
	$("#public_pool_newClientSaleChanceForm select").find("option:first").attr("selected",true);

	$("#public_pool_newClientForm :radio[name='belongs_to'][value='3']").attr("checked",true);
	$("#public_pool_newClientContactForm :radio[name='sex'][value='3']").attr("checked",true);
	$("#public_pool_newClientContactForm :radio[name='key_decide'][value='3']").attr("checked",true);

	console.log("new_end");
}
/*新建翻页*/
function public_pool_next_contact(){
	$("#form1").addClass("hide");
	$("#form2").removeClass("hide");
}
function public_pool_next_sale_chance(){
	$("#form2").addClass("hide");
	$("#form3").removeClass("hide");
}
function public_pool_pre_customer(){
	$("#form2").addClass("hide");
	$("#form1").removeClass("hide");
}
function public_pool_pre_contact(){
	$("#form3").addClass("hide");
	$("#form2").removeClass("hide");
}