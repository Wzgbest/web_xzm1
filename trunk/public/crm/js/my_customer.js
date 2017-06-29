$(".crm_my_customer .m-firNav li").click(function(){
	$(".m-firNav li").removeClass("current");
	$(this).addClass("current");
});
function my_customer_listNumChange(in_column){
	console.log($(".u-tabControlRow select").val());
	num = $(".u-tabControlRow select").val();
	my_customer_change_page(1,num,in_column);
}
function my_customer_previous_page(p,num,in_column){
	if(p-1<1){
		return;
	}
	my_customer_change_page(p-1,num,in_column);
}
function my_customer_next_page(p,num,max,in_column){
	if(p+1>max){
		return;
	}
	my_customer_change_page(p+1,num,in_column);
}
function my_customer_jump_page(num,max,in_column){
	console.log($(".my_customer_jump_page").val());
	p = $(".my_customer_jump_page").val();
	if(p+1>max || p-1<1){
		return;
	}
	my_customer_change_page(p,num,in_column);
}
function my_customer_change_page(p,num,in_column){
	loadPage(get_my_customer_url(p,num,in_column),"myclietsfr");
}
function my_customer_search(p,num,in_column){
	var url = get_my_customer_url(p,num,in_column);
	var take_type = $("#my_customer_search_take_type").val();
	if(take_type!=""){
		url += "/take_type/"+take_type;
	}
	var grade = $("#my_customer_search_grade").val();
	if(grade!=""){
		url += "/grade/"+grade;
	}
	var sale_chance = $("#my_customer_search_sale_chance").val();
	if(sale_chance!=""){
		url += "/sale_chance/"+sale_chance;
	}
	var comm_status = $("#my_customer_search_comm_status").val();
	if(comm_status!=""){
		url += "/comm_status/"+comm_status;
	}
	var customer_name = $("#my_customer_search_customer_name").val();
	if(customer_name!=""){
		url += "/customer_name/"+customer_name;
	}
	var contact_name = $("#my_customer_search_contact_name").val();
	if(contact_name!=""){
		url += "/contact_name/"+contact_name;
	}
	loadPage(url,"myclietsfr");
}
function get_my_customer_url(p,num,in_column){
	return "/crm/customer/my_customer/p/"+p+"/num/"+num+"/in_column/"+in_column;
}

function my_customer_release_customers(ids,p,num,in_column){
	$.ajax({
		url: '/crm/customer/release_customers',
		type: 'post',
		data: "ids="+ids,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				my_customer_search(p,num,in_column);
			}
		},
		error: function() {
			alert("释放客户时发生错误!");
		},
	});
}


var my_customer_hide_panel = 'myclietsfr .crm_my_customer .my_customer_panel';
function my_customer_list_show_list(){
	$('#frames #'+my_customer_hide_panel).addClass("hide");
	$('#frames #myclietsfr .crm_my_customer .my_customer').removeClass("hide");
}
function my_customer_general(id){
	var url = "/crm/customer/general/id/"+id+"/fr/my_customer";
	var panel = 'myclietsfr .crm_my_customer .customer_general';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+my_customer_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户概要失败!");
		}
	});
}
function my_customer_show(id){
	var url = "/crm/customer/show/id/"+id+"/fr/my_customer";
	var panel = 'myclietsfr .crm_my_customer .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+my_customer_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function my_customer_edit(id){
	var url = "/crm/customer/edit/id/"+id+"/fr/my_customer";
	var panel = 'myclietsfr .crm_my_customer .customer_info';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+my_customer_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function my_customer_edit_update(id){
	var my_customer_edit_from_data = $(".my_customer_edit").serialize();
	my_customer_edit_from_data += "&id="+id+"&fr=my_customer";
	console.log(my_customer_edit_from_data);
	$.ajax({
		url: '/crm/customer/update',
		type: 'post',
		data: my_customer_edit_from_data,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				my_customer_show(id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function my_customer_contact_show(id){
	var url = "/crm/customer_contact/show/customer_id/"+id+"/fr/my_customer";
	var panel = 'myclietsfr .crm_my_customer .customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+my_customer_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取联系人失败!");
		}
	});
}
function my_customer_contact_add(customer_id){
	var url = "/crm/customer_contact/add_page/customer_id/"+customer_id+"/fr/my_customer";
	var panel = 'myclietsfr .crm_my_customer .customer_contact .new_customer_contact';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('.my_customer_contact_add_panel').html(data);
			$('.my_customer_contact_add_panel').removeClass("hide");
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function my_customer_contact_add_send(customer_id){
	var my_customer_contact_add_from = $(".my_customer_contact_add_from").serialize();
	my_customer_contact_add_from += "&customer_id="+customer_id+"&fr=my_customer";
	console.log(my_customer_contact_add_from);
	$.ajax({
		url: '/crm/customer_contact/add',
		type: 'post',
		data: my_customer_contact_add_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				my_customer_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function my_customer_contact_edit(id){
	console.log(id);
	var url = "/crm/customer_contact/edit_page/id/"+id+"/fr/my_customer";
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			var html = '<div class="my_customer_contact_edit_panel">';
			html+= data;
			html+= '</div>';
			$('.my_customer_contact_'+id).addClass("hide");
			$('.my_customer_contact_'+id).before(html);
		},
		error:function(){
			alert("获取客户信息失败!");
		}
	});
}
function my_customer_contact_edit_update(id,customer_id){
	var my_customer_contact_edit_from = $(".my_customer_contact_edit_from").serialize();
	my_customer_contact_edit_from += "&id="+id+"&fr=my_customer";
	console.log(my_customer_contact_edit_from);
	$.ajax({
		url: '/crm/customer_contact/update',
		type: 'post',
		data: my_customer_contact_edit_from,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				my_customer_contact_show(customer_id);
			}
		},
		error: function() {
			alert("保存客户信息时发生错误!");
		},
	});
}
function my_customer_sale_chance_show(customer_id){
	var url = "/crm/sale_chance/show/customer_id/"+customer_id+"/fr/my_customer";
	var panel = 'myclietsfr .crm_my_customer .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+my_customer_hide_panel).addClass("hide");
			$('#frames #'+panel).html(data);
			$('#frames #'+panel).removeClass("hide");
		},
		error:function(){
			alert("获取销售机会失败!");
		}
	});
}
function my_customer_trace_show(customer_id){
	var url = "/crm/customer_trace/show/customer_id/"+customer_id+"/fr/my_customer";
	var panel = 'myclietsfr .crm_my_customer .customer_sale_chance';
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+my_customer_hide_panel).addClass("hide");
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
function my_customer_newClient(){
	document.getElementById("my_customer_newClient").classList.remove("hide");
	document.getElementById("my_customer_blackBg").classList.remove("hide");
//	document.getElementsByTagName("body")[0].classList.add("hiddenY");
}
function my_customer_removeNewClient(){
	document.getElementById("my_customer_newClient").classList.add("hide");
	document.getElementById("my_customer_blackBg").classList.add("hide");
	my_customer_new_customer();
}

function my_customer_check_form_html5(eles){
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
var my_customer_new_customer_id = 0;
var my_customer_new_customer_contact_id = 0;
var my_customer_new_customer_sale_chance_id = 0;
function my_customer_add_customer(next_status){
	if(!my_customer_check_form_html5($("#my_customer_newClientForm").get(0).elements)){
		return;
	}
	var my_customer_add_customer_from_data = $("#my_customer_newClientForm").serialize();
	var url = '/crm/customer/add';
	if(my_customer_new_customer_id>0){
		url = '/crm/customer/update';
		my_customer_add_customer_from_data += "&id="+my_customer_new_customer_id;
	}
	//console.log(my_customer_add_customer_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: my_customer_add_customer_from_data,
		success: function(data) {
			if(data.status) {
				if(my_customer_new_customer_id==0) {
					my_customer_new_customer_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert(data.info);
				}
				my_customer_next_status(next_status,my_customer_removeNewClient,my_customer_next_contact);
			}else{
				alert(data.info);
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function my_customer_add_contact(next_status){
	var my_customer_add_customer_contact_from_data = $("#my_customer_newClientContactForm").serialize();
	my_customer_add_customer_contact_from_data += "&customer_id="+my_customer_new_customer_id;
	var url = '/crm/customer_contact/add';
	if(my_customer_new_customer_contact_id>0){
		url = '/crm/customer_contact/update';
		my_customer_add_customer_contact_from_data += "&id="+my_customer_new_customer_contact_id;
	}
	//console.log(my_customer_add_customer_contact_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: my_customer_add_customer_contact_from_data,
		success: function(data) {
			if(data.status) {
				if(my_customer_new_customer_contact_id==0) {
					my_customer_new_customer_contact_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert("保存成功!");
				}
				my_customer_next_status(next_status,my_customer_pre_customer,my_customer_next_sale_chance);
			}else{
				alert(data.info);
			}
		},
		error: function() {
			alert("保存失败!");
		}
	});
}
function my_customer_add_sale_chance(next_status){
	var my_customer_add_customer_sale_chance_from_data = $("#my_customer_newClientSaleChanceForm").serialize();
	my_customer_add_customer_sale_chance_from_data += "&customer_id="+my_customer_new_customer_id;
	var url = '/crm/sale_chance/add';
	if(my_customer_new_customer_sale_chance_id>0){
		url = '/crm/sale_chance/update';
		my_customer_add_customer_sale_chance_from_data += "&id="+my_customer_new_customer_sale_chance_id;
	}
	//console.log(my_customer_add_customer_sale_chance_from_data);
	$.ajax({
		url: url,
		type: 'post',
		data: my_customer_add_customer_sale_chance_from_data,
		success: function(data) {
			if(data.status) {
				if(my_customer_new_customer_sale_chance_id==0) {
					my_customer_new_customer_sale_chance_id = data.data;
				}
				if(next_status==0 || next_status==3){
					alert("保存成功!");
				}
				my_customer_next_status(next_status,my_customer_pre_contact,my_customer_removeNewClient);
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
function my_customer_next_status(next_status,func_previous,func_next){
	if(!next_status){
		my_customer_new_customer();
	}else if(next_status==1){
		func_previous();
	}else if(next_status==2){
		func_next();
	}else if(next_status==3){
		my_customer_removeNewClient();
	}
}
function my_customer_new_customer(){
	console.log("new_start");

	my_customer_new_customer_id = 0;
	my_customer_new_customer_contact_id = 0;
	my_customer_new_customer_sale_chance_id = 0;

	$("#my_customer_newClientForm :text").val("");
	$("#my_customer_newClientContactForm :text").val("");
	$("#my_customer_newClientSaleChanceForm :text").val("");

	$("#my_customer_newClientForm textarea").val("");
	$("#my_customer_newClientContactForm textarea").val("");
	$("#my_customer_newClientSaleChanceForm textarea").val("");

	$("#my_customer_newClientForm").removeClass("hide");
	$("#my_customer_newClientContactForm").addClass("hide");
	$("#my_customer_newClientSaleChanceForm").addClass("hide");


	$("#my_customer_newClientForm select").find("option:first").removeAttr("selected",true);
	$("#my_customer_newClientContactForm select").find("option:first").removeAttr("selected",true);
	$("#my_customer_newClientSaleChanceForm select").find("option:first").removeAttr("selected",true);

	$("#my_customer_newClientForm select").find("option:first").attr("selected",true);
	$("#my_customer_newClientContactForm select").find("option:first").attr("selected",true);
	$("#my_customer_newClientSaleChanceForm select").find("option:first").attr("selected",true);

	$("#my_customer_newClientForm :radio[name='belongs_to'][value='3']").attr("checked",true);
	$("#my_customer_newClientContactForm :radio[name='sex'][value='3']").attr("checked",true);
	$("#my_customer_newClientContactForm :radio[name='key_decide'][value='3']").attr("checked",true);

	console.log("new_end");
}
/*新建翻页*/
function my_customer_next_contact(){
	$("#form1").addClass("hide");
	$("#form2").removeClass("hide");
}
function my_customer_next_sale_chance(){
	$("#form2").addClass("hide");
	$("#form3").removeClass("hide");
}
function my_customer_pre_customer(){
	$("#form2").addClass("hide");
	$("#form1").removeClass("hide");
}
function my_customer_pre_contact(){
	$("#form3").addClass("hide");
	$("#form2").removeClass("hide");
}