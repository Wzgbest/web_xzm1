function customer_info_manage(from,target,list_manage){
	//当前列表变量
	this.id = 0;
	this.from = from;
	this.target = target;
	this.list_manage = list_manage;
	this.panel_base = '#frames #'+this.target+' .crm_'+this.from;
	this.hide_panel = this.panel_base+' .panel';
	var self = this;

	//事件绑定
	$(this.panel_base+" ."+this.from+" .u-tabList .customer_info_name_show").click(function(){
		var id = $(this).siblings().children(":checkbox").val();
		self.general(id);
	});
	$(this.panel_base+" ."+this.from+" .u-tabList li .customer_info_show").click(function(){
		var id = $(this).parent().siblings().children(":checkbox").val();
		self.general(id);
	});
	$(this.panel_base+" ."+this.from+" .u-tabList .u-tabOperation .release_customers").click(function(){
		var id = $(this).parent().siblings().children(":checkbox").val();
		self.release_customers(id);
	});

	//列表方法
	this.release_customers=function(ids){
		$.ajax({
			url: '/crm/customer/release_customers',
			type: 'post',
			data: "ids="+ids,
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
	},

	//弹出框方法
	this.close=function(){
		$(this.panel_base+" .customer_info_panel").addClass("hide");
	},
	this.listen_nav_click=function(panel){
		$(panel+" .page-info .m-firNav .back").click(function(){
			self.close();
		});
		$(panel+" .m-pageInfoNav .customer_info_show").click(function(){
			self.show(self.id);
		});
		$(panel+" .m-pageInfoNav .customer_contact_show").click(function(){
			self.contact_show(self.id);
		});
		$(panel+" .m-pageInfoNav .customer_sale_chance_show").click(function(){
			self.sale_chance_show(self.id);
		});
		$(panel+" .m-pageInfoNav .customer_trace_show").click(function(){
			self.trace_show(self.id);
		});
	},
	this.general=function(id){
		this.id = id;
		var url = "/crm/customer/general/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_general';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(panel).html(data);
				$(panel).height(window.innerHeight);
				self.listen_nav_click(panel);
				$(panel).removeClass("hide");
			},
			error:function(){
				alert("获取客户概要失败!");
			}
		});
	},
	this.show=function(id){
		var url = "/crm/customer/show/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_info';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(panel).html(data);
				$(panel).removeClass("hide");
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	},
	this.edit=function(id){
		var url = "/crm/customer/edit/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_info';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(panel).html(data);
				$(panel).removeClass("hide");
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	},
	this.edit_update=function(id){
		var edit_from_data = $(".edit").serialize();
		edit_from_data += "&id="+id+"&fr="+this.from;
		console.log(edit_from_data);
		$.ajax({
			url: '/crm/customer/update',
			type: 'post',
			data: edit_from_data,
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					show(id);
				}
			},
			error: function() {
				alert("保存客户信息时发生错误!");
			}
		});
	},
	this.contact_show=function(id){
		var url = "/crm/customer_contact/show/customer_id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_contact';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(panel).html(data);
				$(panel).removeClass("hide");
			},
			error:function(){
				alert("获取联系人失败!");
			}
		});
	},
	this.contact_add=function(customer_id){
		var url = "/crm/customer_contact/add_page/customer_id/"+customer_id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_contact';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(panel+' .contact_add_panel').html(data);
				$(panel+' .contact_add_panel').removeClass("hide");
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	},
	this.contact_add_send=function(customer_id){
		var contact_add_from = $(".contact_add_from").serialize();
		contact_add_from += "&customer_id="+customer_id+"&fr="+this.from;
		console.log(contact_add_from);
		$.ajax({
			url: '/crm/customer_contact/add',
			type: 'post',
			data: contact_add_from,
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.contact_show(customer_id);
				}
			},
			error: function() {
				alert("保存客户信息时发生错误!");
			},
		});
	},
	this.contact_edit=function(id){
		console.log(id);
		var url = "/crm/customer_contact/edit_page/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_contact';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				var html = '<div class="contact_edit_panel">';
				html+= data;
				html+= '</div>';
				$(panel+' .contact_'+id).addClass("hide");
				$(panel+' .contact_'+id).before(html);
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	},
	this.contact_edit_update=function(id,customer_id){
		var contact_edit_from = $(".contact_edit_from").serialize();
		contact_edit_from += "&id="+id+"&fr="+this.from;
		console.log(contact_edit_from);
		$.ajax({
			url: '/crm/customer_contact/update',
			type: 'post',
			data: contact_edit_from,
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.contact_show(customer_id);
				}
			},
			error: function() {
				alert("保存客户信息时发生错误!");
			},
		});
	},
	this.sale_chance_show=function(customer_id){
		var url = "/crm/sale_chance/show/customer_id/"+customer_id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_sale_chance';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(panel).html(data);
				$(panel).removeClass("hide");
			},
			error:function(){
				alert("获取销售机会失败!");
			}
		});
	},
	this.trace_show=function(customer_id){
		var url = "/crm/customer_trace/show/customer_id/"+customer_id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_sale_chance';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(panel).html(data);
				$(panel).removeClass("hide");
			},
			error:function(){
				alert("获取客户跟踪信息失败!");
			}
		});
	}
}