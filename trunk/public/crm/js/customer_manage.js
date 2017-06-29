function customer_manage_listNumChange(p){
	console.log($(".u-tabControlRow select").val());
	num = $(".u-tabControlRow select").val();
	customer_manage_change_page(p,num);
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
	p = $(".customer_manage_jump_page").val();
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

/***************************/
$("#blackBg").height(window.innerHeight);
/*****************************************************************/