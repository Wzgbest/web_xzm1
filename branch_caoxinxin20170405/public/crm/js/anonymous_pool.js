function anonymous_pool_listNumChange(){
	console.log($(".u-tabControlRow select").val());
	var num = $(".u-tabControlRow select").val();
	anonymous_pool_change_page(1,num);
}
function anonymous_pool_previous_page(p,num){
	if(p-1<1){
		return;
	}
	anonymous_pool_change_page(p-1,num);
}
function anonymous_pool_next_page(p,num,max){
	if(p+1>max){
		return;
	}
	anonymous_pool_change_page(p+1,num);
}
function anonymous_pool_jump_page(num,max){
	console.log($(".anonymous_pool_jump_page").val());
	var p = $(".anonymous_pool_jump_page").val();
	if(p+1>max || p-1<1){
		return;
	}
	anonymous_pool_change_page(p,num);
}
function anonymous_pool_change_page(p,num){
	loadPage(get_anonymous_pool_url(p,num),"high-seafr");
}
function anonymous_pool_search(p,num){
	var url = get_anonymous_pool_url(p,num);
	var take_type = $("#anonymous_pool_search_take_type").val();
	if(take_type!=""){
		url += "/take_type/"+take_type;
	}
	var grade = $("#anonymous_pool_search_grade").val();
	if(grade!=""){
		url += "/grade/"+grade;
	}
	var sale_chance = $("#anonymous_pool_search_sale_chance").val();
	if(sale_chance!=""){
		url += "/sale_chance/"+sale_chance;
	}
	var comm_status = $("#anonymous_pool_search_comm_status").val();
	if(comm_status!=""){
		url += "/comm_status/"+comm_status;
	}
	var customer_name = $("#anonymous_pool_search_customer_name").val();
	if(customer_name!=""){
		url += "/customer_name/"+customer_name;
	}
	var contact_name = $("#anonymous_pool_search_contact_name").val();
	if(contact_name!=""){
		url += "/contact_name/"+contact_name;
	}
	loadPage(url,"high-seafr");
}
function get_anonymous_pool_url(p,num){
	return "/crm/customer/public_customer_pool/p/"+p+"/num/"+num;
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