$("#frames #myclietsfr .crm_my_customer .my_customer .u-tabList .u-tabOperation .release_customers").click(function(){
	var id = $(this).parent().siblings().children(":checkbox").val();
	$.ajax({
		url: '/crm/customer/release_customers',
		type: 'post',
		data: "ids="+id,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				my_customer_list_manage.reload_list();
			}
		},
		error: function() {
			alert("释放客户时发生错误!");
		}
	});
});
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