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