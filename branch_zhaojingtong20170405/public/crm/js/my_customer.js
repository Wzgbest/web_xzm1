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

var my_customer_nav_base = "#frames #myclietsfr .crm_my_customer .my_customer .m-secNav";
my_customer_list_manage.listenSelect("exportCustomer");
$(my_customer_nav_base+" .exportCustomer").click(function(){
	var ids = my_customer_list_manage.getAllSelectVal();
	if(ids==""){
		return;
	}
	//console.log(ids);
	$.ajax({
		url: '/crm/customer_import/exportCustomer',
		type: 'post',
		data: ids,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				my_customer_list_manage.reload_list();
			}
		},
		error: function() {
			alert("导出短信时发生错误!");
		}
	});
});
my_customer_list_manage.listenSelect("send_customer_group_message");
$(my_customer_nav_base+" .send_customer_group_message").click(function(){
	var ids = my_customer_list_manage.getAllSelectVal();
	if(ids==""){
		return;
	}
	//console.log(ids);
	$.ajax({
		url: '/crm/customer/send_customer_group_message',
		type: 'post',
		data: ids,
		success: function(data) {
			//console.log(data);
			alert(data.info);
			if(data.status) {
				my_customer_list_manage.reload_list();
			}
		},
		error: function() {
			alert("群发短信时发生错误!");
		}
	});
});
my_customer_list_manage.listenSelect("release_customers");
$(my_customer_nav_base+" .release_customers").click(function(){
	var ids = my_customer_list_manage.getAllSelectVal();
	if(ids==""){
		return;
	}
	//console.log(ids);
	$.ajax({
		url: '/crm/customer/release_customers',
		type: 'post',
		data: ids,
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