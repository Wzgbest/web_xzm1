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