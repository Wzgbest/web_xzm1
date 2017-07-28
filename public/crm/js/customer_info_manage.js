function customer_info_manage(from,target,list_manage,in_column,in_column_name,list_count){
	//当前列表变量
	this.id = 0;
	this.last = 0;
	this.from = from;
	this.reload_flg = 0;
	this.target = target;
	this.list_manage = list_manage;
	this.in_column = parseInt(in_column);
	this.in_column_name = in_column_name;
	this.list_count = parseInt(list_count);
	this.panel_base = '#frames #'+this.target+' .crm_'+this.from;
	var self = this;

	//事件绑定
	$(this.panel_base+" ."+this.from+" .u-tabList .customer_info_name_show").click(function(){
		var id = $(this).siblings().children(":checkbox").val();
		self.general(id);
	});
	$(this.panel_base+" ."+this.from+" .u-tabList li .customer_info_show").click(function(){
		var id = $(this).parent().siblings().children(":checkbox").val();
		self.show(id);
	});
	$(this.panel_base+" ."+this.from+" .u-tabList li .customer_info_edit").click(function(){
		var id = $(this).parent().siblings().children(":checkbox").val();
		self.edit(id);
	});
	//打电话事件
	$(this.panel_base+" ."+this.from+" .u-tabList .u-tabLinkWay").click(function(){
		var id = $(this).siblings().children(":checkbox").val();
		var num = $(this).text();
		self.general(id);
		$("#phone-number").val(num);
		$(".phone-box").removeClass("hide");
		phoneWidth = $(".phone-box").width()+10;
		changeFramesSize();
	});
	//弹出框方法
	this.close=function(){
		$(this.panel_base+" .customer_info_panel").addClass("hide");
		if(this.reload_flg){
			this.list_manage.reload_list();
		}
	};
	this.show_panel=function(panel,data){
		$(panel).html(data);
		$(panel).height(window.innerHeight);
		this.listen_nav_click(panel);
		$(this.panel_base+" .customer_info_panel").addClass("hide");
		$(panel).removeClass("hide");
	};
	this.listen_nav_click=function(panel){
		$(panel+" .page-info .m-firNav .back div").html(
			self.in_column_name+"（<span>"+self.list_count+"</span>）"
		);
		$(panel+" .page-info .m-firNav .back").click(function(){
			self.close();
		});
		$(panel+" .m-pageInfoNav .customer_general_show").click(function(){
			self.general(self.id);
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
	};
	this.general=function(id){
		this.id = id;
		//console.log(this.id);
		var url = "/crm/customer/general/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_general';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.show_panel(panel,data);
			},
			error:function(){
				alert("获取客户概要失败!");
			}
		});
	};
	this.show=function(id){
		this.id = id;
		//console.log(this.id);
		var url = "/crm/customer/show/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_info';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.show_panel(panel,data);
				$(panel+" .m-form .customer_info_edit_show").click(function(){
					self.edit(self.id,1);
				});
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	};
	this.edit=function(id,last){
		this.id = id;
		this.last = last;
		//console.log(this.id);
		var url = "/crm/customer/edit/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_edit';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.show_panel(panel,data);
				$(panel+" .m-form .u-submitButton .customer_edit_save").click(function(){
					self.edit_update(self.id);
				});
				$(panel+" .m-form .u-submitButton .customer_edit_cancel").click(function(){
					if(self.last==1){
						self.last = 0;
						self.show(self.id);
					}else{
						self.close();
					}
				});
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	};
	this.edit_update=function(id){
		var panel = this.panel_base+' .customer_edit';
		var edit_from_data = $(panel+" .edit").serialize();
		edit_from_data += "&id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(edit_from_data);
		$.ajax({
			url: '/crm/customer/update',
			type: 'post',
			data: edit_from_data,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.show(id);
				}
			},
			error: function() {
				alert("保存客户信息时发生错误!");
			}
		});
	};
	this.contact_show=function(id){
		this.id = id;
		//console.log(this.id);
		var url = "/crm/customer_contact/show/customer_id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_contact';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.show_panel(panel,data);
				$(panel+" .page-info .addClientInfoLinkman").click(function(){
					self.contact_add(self.id);
				});
				$(panel+" .page-info .linkman .editlinkman").click(function(){
					var edit_id = $(this).children(":input").val();
					self.contact_edit(edit_id);
				});
			},
			error:function(){
				alert("获取联系人失败!");
			}
		});
	};
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
				$(panel+" .contact_add_panel .customer_contact_add_save").click(function(){
					self.contact_add_send(self.id);
				});
				$(panel+" .contact_add_panel .customer_contact_add_cancel").click(function(){
					$(panel+" .contact_add_panel").addClass("hide");
				});
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	};
	this.contact_add_send=function(customer_id){
		var panel = this.panel_base+' .customer_contact';
		var contact_add_from = $(panel+" .contact_add_from").serialize();
		contact_add_from += "&customer_id="+customer_id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(contact_add_from);
		$.ajax({
			url: '/crm/customer_contact/add',
			type: 'post',
			data: contact_add_from,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.contact_show(customer_id);
				}
			},
			error: function() {
				alert("保存客户信息时发生错误!");
			}
		});
	};
	this.contact_edit=function(id){
		//console.log(id);
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
				//console.log($(panel));
				$(panel+' .'+self.from+'_contact_'+id).addClass("hide");
				$(panel+' .'+self.from+'_contact_'+id).before(html);
				$(panel+" .contact_edit_panel .customer_contact_edit_save").click(function(){
					self.contact_edit_update(id,self.id);
				});
				$(panel+" .contact_edit_panel .customer_contact_edit_cancel").click(function(){
					$(panel+" .contact_edit_panel").addClass("hide");
					$(panel+' .'+self.from+'_contact_'+id).removeClass("hide");
				});
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	};
	this.contact_edit_update=function(id,customer_id){
		var panel = this.panel_base+' .customer_contact';
		var contact_edit_from = $(panel+" .contact_edit_panel .contact_edit_from").serialize();
		contact_edit_from += "&id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(contact_edit_from);
		$.ajax({
			url: '/crm/customer_contact/update',
			type: 'post',
			data: contact_edit_from,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.contact_show(customer_id);
				}
			},
			error: function() {
				alert("保存客户信息时发生错误!");
			}
		});
	};
	this.sale_chance_show=function(customer_id){
		this.id = customer_id;
		//console.log(this.id);
		var url = "/crm/sale_chance/show/customer_id/"+customer_id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_sale_chance';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.show_panel(panel,data);
			},
			error:function(){
				alert("获取销售机会失败!");
			}
		});
	};
	this.trace_show=function(customer_id){
		this.id = customer_id;
		//console.log(this.id);
		var url = "/crm/customer_trace/show/customer_id/"+customer_id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_sale_chance';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.show_panel(panel,data);
			},
			error:function(){
				alert("获取客户跟踪信息失败!");
			}
		});
	}
}