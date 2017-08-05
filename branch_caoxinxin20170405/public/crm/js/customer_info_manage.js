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
				$(panel+" .outlineInfo .customer_info_edit").click(function(){
					self.edit(self.id,2);
				});
				$(panel+" .outlineChance .sale_chance_edit").click(function(){
					var sale_chance_id = $(this).attr("sale_chance_id");
					console.log(sale_chance_id);
					self.sale_chance_show(self.id,sale_chance_id);
				});
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
					self.edit_updated_show();
				});
			},
			error:function(){
				alert("获取客户信息失败!");
			}
		});
	};
	this.edit_update=function(id){
		var panel = this.panel_base+' .customer_edit';
		var edit_from_data = $(panel+" ."+this.from+"_edit").serialize();
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
					self.edit_updated_show();
				}
			},
			error: function() {
				alert("保存客户信息时发生错误!");
			}
		});
	};
	this.edit_updated_show=function(){
		if(self.last==1){
			self.last = 0;
			self.show(self.id);
		}else if(self.last==2){
			self.last = 0;
			self.general(self.id);
		}else{
			self.close();
		}
	};
	this.contact_show=function(customer_id){
		this.id = customer_id;
		//console.log(this.id);
		var url = "/crm/customer_contact/show/customer_id/"+this.id+"/fr/"+this.from;
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
		this.id = customer_id;
		var url = "/crm/customer_contact/add_page/customer_id/"+this.id+"/fr/"+this.from;
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
		this.id = customer_id;
		var panel = this.panel_base+' .customer_contact';
		var contact_add_from = $(panel+" .contact_add_from").serialize();
		contact_add_from += "&customer_id="+this.id+"&fr="+this.from;
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
					self.contact_show(self.id);
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
				var html = '<div class="contact_edit_panel contact_edit_panel_'+id+'">';
				html+= data;
				html+= '</div>';
				//console.log($(panel));
				$(panel+' .'+self.from+'_contact_'+id).addClass("hide");
				$(panel+' .'+self.from+'_contact_'+id).before(html);
				$(panel+" .contact_edit_panel .customer_contact_edit_save").click(function(){
					self.contact_edit_update(id,self.id);
				});
				$(panel+" .contact_edit_panel .customer_contact_edit_cancel").click(function(){
					$(panel+" .contact_edit_panel_"+id).addClass("hide");
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
		var contact_edit_from = $(panel+" . contact_edit_panel_"+id+" .contact_edit_from").serialize();
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
	this.sale_chance_show=function(customer_id,open_edit_id){
		this.id = customer_id;
		//console.log(this.id);
		var url = "/crm/sale_chance/show/customer_id/"+this.id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_sale_chance';
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.show_panel(panel,data);
				var sale_chance_panel = panel+" .clientInfoSaleChance";
				$(sale_chance_panel+" .new-sale-chance").click(function(){
					self.sale_chance_add(self.id);
				});
				$(panel+" .clientInfoSaleChance .sale-chance-record .sale_chance_edit").click(function(){
					var edit_id = $(this).parent().siblings(":input").val();
					self.sale_chance_edit(edit_id,0);
				});
				$(panel+" .clientInfoSaleChance .sale-chance-record .sale_chance_invalid").click(function(){
					var edit_id = $(this).parent().siblings(":input").val();
					self.sale_chance_invalid(edit_id,self.id);
				});
				$(panel+" .clientInfoSaleChance .sale-chance-record .sale_chance_refresh").click(function(){
					var edit_id = $(this).parent().siblings(":input").val();
					self.sale_chance_edit(edit_id,1);
				});
				$(panel+" .clientInfoSaleChance .sale-chance-record .sale_chance_reply").click(function(){
					var edit_id = $(this).parent().siblings(":input").val();
					self.sale_chance_reply(edit_id,self.id);
				});
				$(panel+" .clientInfoSaleChance .sale-chance-record .bill-apply").click(function(){
					var edit_id = $(this).parent().parent().parent().siblings(".z-recordState").children(".edit_id").val();
					var pop = new popLoad("#create-bill","/crm/bill/bill_apply/sale_id/"+edit_id);
				});
				$(panel+" .clientInfoSaleChance .sale-chance-record .bill-apply-retract").click(function(){
					var edit_id = $(this).parent().parent().parent().siblings(".z-recordState").children(".edit_id").val();
					self.bill_apply_retract(edit_id,self.id);
				});
				if(open_edit_id&&open_edit_id>0){
					self.sale_chance_edit(open_edit_id,0);
				}
			},
			error:function(){
				alert("获取销售机会失败!");
			}
		});
	};
	this.sale_chance_add=function(customer_id){
		this.id = customer_id;
		var url = "/crm/sale_chance/add_page/customer_id/"+this.id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_sale_chance';
		var sale_chance_panel = panel+" .clientInfoSaleChance";
		var new_sale_chance_panel = sale_chance_panel+" .create-sale-chance";
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(sale_chance_panel+" .new-sale-chance").after(data);
				$(new_sale_chance_panel+" .sale_chance_add_save").click(function(){
					self.sale_chance_add_send(self.id);
				});
				$(new_sale_chance_panel+" .sale_chance_add_cancel").click(function(){
					$(new_sale_chance_panel).remove();
				});
			},
			error:function(){
				alert("获取销售机会添加失败!");
			}
		});
	};
	this.sale_chance_add_send=function(customer_id){
		var panel = this.panel_base+' .customer_sale_chance';
		var sale_chance_panel = panel+" .clientInfoSaleChance";
		var new_sale_chance_panel = sale_chance_panel+" .create-sale-chance";
		var sale_chance_add_from = $(new_sale_chance_panel+" .newSaleChanceForm").serialize();
		sale_chance_add_from += "&customer_id="+customer_id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_add_from);
		$.ajax({
			url: '/crm/sale_chance/add',
			type: 'post',
			data: sale_chance_add_from,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
				alert("保存销售机会时发生错误!");
			}
		});
	};
	this.sale_chance_edit=function(id,refresh){
		console.log(id);
		var url = "/crm/sale_chance/edit_page/id/"+id+"/refresh/"+refresh+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_sale_chance';
		var sale_chance_panel = panel+" .clientInfoSaleChance";
		var edit_sale_chance_panel = sale_chance_panel+" .edit-sale-chance-"+id;
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				$(sale_chance_panel+" .sale-chance-record-"+id).addClass("hide");
				$(sale_chance_panel+" .sale-chance-record-"+id).before(data);
				$(sale_chance_panel+' .edit-sale-chance-'+id+" input:first").focus();
				$(edit_sale_chance_panel+" .sale-chance-status_selecter").change(function(){
					var status = $(edit_sale_chance_panel+" .sale-chance-status_selecter").val();
					console.log(status);
					$(edit_sale_chance_panel+" .sale-chance").addClass("hide");
					if(status==1){
						$(edit_sale_chance_panel+" .sale-chance-intentional").removeClass("hide");
					}else if(status==2){
						$(edit_sale_chance_panel+" .sale-chance-visit").removeClass("hide");
					}else if(status==4){
						$(edit_sale_chance_panel+" .sale-chance-finish").removeClass("hide");
					}
				});
				var contract_type_name_json = $(edit_sale_chance_panel+" .contract_no_selecter").siblings("input").val();
				//console.log(contract_type_name_json);
				if(contract_type_name_json){
					var contract_type_name_index = null;
					try{
						contract_type_name_index = JSON.parse(contract_type_name_json);
					}catch (ex){
						console.log(ex);
					}
					if(contract_type_name_index==null){
						console.log("contract type name data not found");
						contract_type_name_index = [];
					}
					//console.log(contract_type_name_index);
					var get_contract_type_name_by_id = function(id){
						var contract_type_name = "------";
						if(id in contract_type_name_index){
							contract_type_name = contract_type_name_index[id];
						}
						return contract_type_name;
					};
					var set_contract_type_name_by_id = function(target){
						var contract_id = $(target).val();
						if(!contract_id>0){
							return;
						}
						var contract_type_name = get_contract_type_name_by_id(contract_id);
						$(edit_sale_chance_panel+" .contract_type").text(contract_type_name);
					};
					$(edit_sale_chance_panel+" .contract_no_selecter").change(function(){
						set_contract_type_name_by_id(this);
					});
					try{
						set_contract_type_name_by_id($(edit_sale_chance_panel+" .contract_no_selecter"));
					}catch (ex){
						console.log(ex);
					}
				}
				$(edit_sale_chance_panel+" .sale_chance_edit_save").click(function(){
					self.sale_chance_edit_send(id,self.id);
				});
				$(edit_sale_chance_panel+" .sale_chance_edit_cancel").click(function(){
					$(sale_chance_panel+" .sale-chance-record-"+id).removeClass("hide");
					$(edit_sale_chance_panel).remove();
				});
			},
			error:function(){
				alert("获取销售机会编辑失败!");
			}
		});
	};
	this.sale_chance_edit_send=function(id,customer_id){
		console.log(id);
		console.log(customer_id);
		var panel = this.panel_base+' .customer_sale_chance';
		var sale_chance_panel = panel+" .clientInfoSaleChance";
		var edit_sale_chance_panel = sale_chance_panel+" .edit-sale-chance-"+id;
		var sale_chance_edit_from = $(edit_sale_chance_panel+" .editSaleChanceForm").serialize();
		sale_chance_edit_from += "&id="+id+"&customer_id="+customer_id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_add_from);
		$.ajax({
			url: '/crm/sale_chance/update',
			type: 'post',
			data: sale_chance_edit_from,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
				alert("保存销售机会时发生错误!");
			}
		});
	};
	this.sale_chance_invalid=function(id,customer_id){
		console.log(id);
		if(!window.confirm("确认作废这个商机吗?")){
			return false;
		}
		var sale_chance_invalid_data = "id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_invalid_data);
		$.ajax({
			url: '/crm/sale_chance/invalid',
			type: 'post',
			data: sale_chance_invalid_data,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
				alert("作废商机时发生错误!");
			}
		});
	};
	this.sale_chance_reply=function(id,customer_id){
		console.log(id);
		if(!window.confirm("确认撤回这个商机的成单申请吗?")){
			return false;
		}
		var sale_chance_invalid_data = "id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_invalid_data);
		$.ajax({
			url: '/crm/sale_chance/retract',
			type: 'post',
			data: sale_chance_invalid_data,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
				alert("撤回成单申请时发生错误!");
			}
		});
	};
	this.bill_apply_retract=function(id,customer_id){
		console.log(id);
		if(!window.confirm("确认撤回这个商机的发票申请吗?")){
			return false;
		}
		var sale_chance_invalid_data = "id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_invalid_data);
		$.ajax({
			url: '/crm/bill/retract',
			type: 'post',
			data: sale_chance_invalid_data,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
				alert("撤回发票申请时发生错误!");
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
				$(panel+" .clientInfoTrace .new-trace").click(function(){
					$(panel+" .creat-traceRecord").removeClass("hide");
				});
				$(panel+" .creat-traceRecord .new-trace-save").click(function(){
					self.trace_add(self.id);
				});
				$(panel+" .creat-traceRecord .new-trace-cancel").click(function(){
					$(panel+" .creat-traceRecord").addClass("hide");
				});
			},
			error:function(){
				alert("获取客户跟踪信息失败!");
			}
		});
	};
	this.trace_add=function(customer_id){
		var panel = this.panel_base+' .customer_sale_chance';
		var customer_trace_remark = $(panel+" .new-trace-remark").val();
		customer_trace_remark = "remark="+customer_trace_remark+"&customer_id="+customer_id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_add_from);
		$.ajax({
			url: '/crm/customer_trace/add',
			type: 'post',
			data: customer_trace_remark,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
				alert(data.info);
				if(data.status) {
					self.trace_show(customer_id);
				}
			},
			error: function() {
				alert("保存客户跟踪信息时发生错误!");
			}
		});
	};
}