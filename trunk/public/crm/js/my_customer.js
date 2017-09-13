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

var my_customer_base = "#frames #myclietsfr .crm_my_customer";
var my_customer_nav_base = my_customer_base+" .my_customer .m-secNav";
$(my_customer_base+" .customer_import_record .m-firNav .current").click(function(){
	$(my_customer_base+" .customer_import_record").addClass("hide");
});
$(my_customer_nav_base+" .customer_import").click(function(){
	my_customer_import.load_list();
});

$(my_customer_base+" .my_customer_import_ui .my_customer_import_templet_download").click(function(){
	window.open("/download/templet/Customer.xlsx");
});
$(my_customer_base+" .my_customer_import_ui .my_customer_import_submit_btn").click(function(){
	var formData = new FormData($(my_customer_base+" .my_customer_import_ui .my_customer_import_from")[0]);
	var url = "/index/import_file/upload/";

	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: formData,
		processData: false,  // 告诉jQuery不要去处理发送的数据
		contentType: false,
		success:function(data){
			if (data.status!=1) {
				alert(data.info);
				return;
			}
			var file_id = data.data[0].id;
			$.ajax({
				url: '/crm/customer_import/importCustomer',
				type: 'post',
				data: "file_id="+file_id+"&import_to=3",
				dataType: 'json',
				success: function(data) {
					//console.log(data);
					alert(data.info);
					if(data.status) {
						my_customer_list_manage.reload_list();
					}
				},
				error: function() {
					alert("导入员工时发生错误!");
				}
			});
		},
		error:function(){
			alert("上传文件失败!");
		}
	});
});
$(my_customer_base+" .my_customer_import_ui .my_customer_import_cancel_btn").click(function(){
	$(my_customer_base+" .my_customer_import_ui").trigger('reveal:close');
});

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
			alert("导出客户时发生错误!");
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
/*新建客户中的顶部状态*/