$(".m-firNav li").click(function(){
	$(".m-firNav li").removeClass("current");
	$(this).addClass("current");
});
//var content1 = "<ul class='u-tabList'><li class='u-tabCheckbox'><input type='checkbox'/></li><li class='u-tabCilentName'></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabLinkWay'></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabOperation'><span>详情</span><span>编辑</span><span>释放</span></li><div class='clearfix'></div></ul>";
var content2 = "<ul class='u-tabList'><li class='u-tabCheckbox'><input type='checkbox'/></li><li class='u-tabCilentName'></li><li></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabLinkWay'></li><li></li><li></li><li></li><li class='u-tabOperation'><span>详情</span><span>编辑</span><span>释放</span></li><div class='clearfix'></div></ul>";

var t;
t=5;
for(var i=0;i<t;i++){
	//$(".table1").append(content1);
	$(".table2").append(content2);
}
function listNumChange(p,in_column){
	console.log($(".u-tabControlRow select").val());
	num = $(".u-tabControlRow select").val();
	my_customer_change_page(p,num,in_column);
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
/***************************/
$("#blackBg").height(window.innerHeight);
/*****************************************************************/
/*新建*/
function newClient(){
	document.getElementById("newClient").classList.remove("hide");
	document.getElementById("blackBg").classList.remove("hide");
//	document.getElementsByTagName("body")[0].classList.add("hiddenY");
}
function removeNewClient(){
	document.getElementById("newClient").classList.add("hide");
	document.getElementById("blackBg").classList.add("hide");
	my_customer_new_customer();
}

/**********************************/
//详情
//$(".page-client").hide();
//	$(".page-info").show();
	
/*$(".page-info").hide();
$(".tab-list .tab-operation").eq(0).click(function(){
	$(".page-client").hide();
	$(".page-info").show();
});*/
/*焦点获取失去的交互*/
/*$(".m-form input").focus(function(){
	$(this).addClass("focus");
});
$(".m-form input").blur(function(){
	$(this).removeClass("focus");
});
$(".m-form textarea").focus(function(){
	$(this).addClass("focus");
});
$(".m-form textarea").blur(function(){
	$(this).removeClass("focus");
});*/
/*客户详情——客户信息——编辑*/
$("#clientInformation .edit i").click(function(){
	$("#clientInformation").addClass("hide");
	$("#clientInformationEdit").removeClass("hide");
});
/*客户详情-联系人-新建联系人*/
$("#new-linkman").click(function(){
	$("#creat-linkman").removeClass("hide");
});
$("#creat-linkman .close").click(function(){
	$("#creat-linkman").addClass("hide");
});
function fun1(){
	console.log($("#fff").val());
	var val=$("#fff").val();
	console.log(val);
	if(val==1){
		$(".sale-chance").addClass("hide");
		$(".sale-chance-intentional").removeClass("hide");
	}else if(val==2){
		$(".sale-chance").addClass("hide");
		$(".sale-chance-visit").removeClass("hide");
	}else if(val==3){
		$(".sale-chance").addClass("hide");
		$(".sale-chance-finish").removeClass("hide");
	}
}
$("#new-sale-chance").click(function(){
	$("#creat-sale-chance").removeClass("hide");
});
/*跟踪记录*/
$("#new-trace").click(function(){
	$("#creat-traceRecord").removeClass("hide");
});
function check_form_html5(eles){
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
	if(!check_form_html5($(".newClientForm").get(0).elements)){
		return;
	}
	var my_customer_add_customer_from_data = $(".newClientForm").serialize();
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
				my_customer_next_status(next_status,removeNewClient,my_customer_next_contact);
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
	var my_customer_add_customer_contact_from_data = $(".newClientContactForm").serialize();
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
	var my_customer_add_customer_sale_chance_from_data = $(".newClientSaleChanceForm").serialize();
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
				my_customer_next_status(next_status,my_customer_pre_contact,removeNewClient);
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
		removeNewClient();
	}
}
function my_customer_new_customer(){
	console.log("new_start");

	my_customer_new_customer_id = 0;
	my_customer_new_customer_contact_id = 0;
	my_customer_new_customer_sale_chance_id = 0;

	$(".newClientForm :text").val("");
	$(".newClientContactForm :text").val("");
	$(".newClientSaleChanceForm :text").val("");

	$(".newClientForm textarea").val("");
	$(".newClientContactForm textarea").val("");
	$(".newClientSaleChanceForm textarea").val("");

	$("#form1").removeClass("hide");
	$("#form2").addClass("hide");
	$("#form3").addClass("hide");


	$(".newClientForm select").find("option:first").removeAttr("selected",true);
	$(".newClientContactForm select").find("option:first").removeAttr("selected",true);
	$(".newClientSaleChanceForm select").find("option:first").removeAttr("selected",true);

	$(".newClientForm select").find("option:first").attr("selected",true);
	$(".newClientContactForm select").find("option:first").attr("selected",true);
	$(".newClientSaleChanceForm select").find("option:first").attr("selected",true);

	$(".newClientForm :radio[name='belongs_to'][value='3']").attr("checked",true);
	$(".newClientContactForm :radio[name='sex'][value='3']").attr("checked",true);
	$(".newClientContactForm :radio[name='key_decide'][value='3']").attr("checked",true);

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