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
		changeFramesSize();
	});
	$(this.panel_base+" ."+this.from+" .u-tabList li .customer_info_show").click(function(){
		var id = $(this).parent().siblings().children(":checkbox").val();
		self.show(id);
	});
	$(this.panel_base+" ."+this.from+" .u-tabList li .customer_info_edit").click(function(){
		var id = $(this).parent().siblings().children(":checkbox").val();
		self.edit(id);
	});
	this.interal;
	$(this.panel_base).on("click",".sale-chance input[name='visit_place']",function(){
		console.log($(this).val());
		$(self.panel_base+" #mapFrame")[0].contentWindow.searchKey($(this).val());
	});
	this.contract_count = 1;
	this.contract_index = 1;
	$(this.panel_base).on("click",".edit-sale-chance .sale-chance-apply-contract .add",function(){
		var a = $(this).parent().parent();//.sale-chance-apply-contract
		var b = a.html();//
		var a_index = a.attr("index");
		console.log("a_index",a_index);
		self.contract_index++;
		console.log("self.contract_index",self.contract_index);
		//console.log("b0",b);
		b = b.replace("pay_type_"+a_index,"pay_type_"+self.contract_index);
		b = b.replace("pay_type_"+a_index,"pay_type_"+self.contract_index);
		b = b.replace("pay_bank_"+a_index,"pay_bank_"+self.contract_index);
		b = b.replace("pay_bank_"+a_index,"pay_bank_"+self.contract_index);
		b = b.replace("need_bill_"+a_index,"need_bill_"+self.contract_index);
		b = b.replace("need_bill_"+a_index,"need_bill_"+self.contract_index);
		//console.log("b1",b);
		var c = '<div class="sale-chance-apply-contract" index="'+(self.contract_index)+'">'+b+'</div>';
		a.after(c);
		self.contract_count+=1;
	});
	$(this.panel_base).on("click",".edit-sale-chance .sale-chance-apply-contract .delete",function(){
		var a = $(this).parent().parent();//.sale-chance-apply-contract
		if(self.contract_count>1){
			a.remove();
			self.contract_count-=1;
		}else{
			alert("不能再删除了");
		}
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
		$(this.panel_base+" .customer_info_panel").siblings(".customer_manage").removeClass("hide");
		$(this.panel_base+" .customer_info_panel").siblings(".my_customer").removeClass("hide");
		$(this.panel_base+" .customer_info_panel").siblings(".public_pool").removeClass("hide");
		$(this.panel_base+" .customer_info_panel").siblings(".anonymous_pool").removeClass("hide");
        $(this.panel_base+" .customer_info_panel").siblings(".customer_from_panel").removeClass("hide");
		if(this.reload_flg){
			this.list_manage.reload_list();
		}
	};
	this.show_panel=function(panel,data){
		$(panel).html(data);
		$(panel).height(window.innerHeight);
		this.listen_nav_click(panel);
		$(this.panel_base+" .customer_info_panel").addClass("hide");
		$(panel).removeClass("hide").siblings().addClass("hide");
	};
	this.listen_nav_click=function(panel){
		$(panel+" .page-info .m-firNav .back div").html(
			self.in_column_name+"（<span>"+self.list_count+"</span>）"
		);
		$(panel+" .page-info .m-firNav .back").click(function(){
			self.close();
			changeFramesSize();
		});
		$(panel+" .m-pageInfoNav .customer_general_show").click(function(){
			self.general(self.id);
			changeFramesSize();
		});
		$(panel+" .m-pageInfoNav .customer_info_show").click(function(){
			self.show(self.id);
			changeFramesSize();
		});
		$(panel+" .m-pageInfoNav .customer_contact_show").click(function(){
			self.contact_show(self.id);
			changeFramesSize();
		});
		$(panel+" .m-pageInfoNav .customer_sale_chance_show").click(function(){
			self.sale_chance_show(self.id);
			changeFramesSize();
		});
		$(panel+" .m-pageInfoNav .customer_trace_show").click(function(){
			self.trace_show(self.id);
			changeFramesSize();
		});
        changeFramesSize();
	};
	this.general=function(id){
		this.id = id;
		//console.log(this.id);
		var url = "/crm/customer/general/id/"+id+"/fr/"+this.from;
		var panel = this.panel_base+' .customer_general';
		//console.log("panel",panel);
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
				layer.msg('获取客户概要失败!',{icon:2});
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
					changeFramesSize();
				});
			
			},
			error:function(){
                layer.msg('获取客户信息失败!',{icon:2});
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
				changeFramesSize();
			},
			error:function(){
                layer.msg('获取客户信息失败!',{icon:2});
			}
		});
	};

    this.check_form_html5=function(eles){
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
    };

	this.edit_update=function(id){
        if(!this.check_form_html5($(this.panel_base+" .newClientForm").get(0).elements)){
            return;
        }
		var panel = this.panel_base+' .customer_edit';

		var edit_form_data = $(panel+" ."+this.from+"_edit").serialize();
		edit_form_data += "&id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(edit_form_data);
		$.ajax({
			url: '/crm/customer/update',
			type: 'post',
			data: edit_form_data,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.edit_updated_show();
				}
			},
			error: function() {
                layer.msg('保存客户信息时发生错误!',{icon:2});
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
                    $(panel+" .page-info .addClientInfoLinkman").addClass("hide");
				});
				$(panel+" .page-info .linkman .editlinkman").click(function(){
					var edit_id = $(this).children(":input").val();
					self.contact_edit(edit_id);
                    $(".editlinkman").addClass("hide");
				});
			},
			error:function(){
                layer.msg('获取联系人失败!',{icon:2});
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
                    $(panel+" .page-info .addClientInfoLinkman").removeClass("hide");
				});
				$(panel+" .contact_add_panel .customer_contact_add_cancel").click(function(){
					$(panel+" .contact_add_panel").addClass("hide");
                    $(panel+" .page-info .addClientInfoLinkman").removeClass("hide");
				});
			},
			error:function(){
                layer.msg('获取客户信息失败!',{icon:2});
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
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.contact_show(self.id);
				}
			},
			error: function() {
				layer.msg('保存客户信息时发生错误!',{icon:2});
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
                    $(".editlinkman").removeClass("hide");
				});
				$(panel+" .contact_edit_panel .customer_contact_edit_cancel").click(function(){
					$(panel+" .contact_edit_panel_"+id).addClass("hide");
					$(panel+' .'+self.from+'_contact_'+id).removeClass("hide");
                    $(".editlinkman").removeClass("hide");
				});
			},
			error:function(){
                layer.msg('获取客户信息失败!',{icon:2});
			}
		});
	};
	this.contact_edit_update=function(id,customer_id){
		var panel = this.panel_base+' .customer_contact';
		var contact_edit_form = $(panel+" .contact_edit_panel_"+id+" .contact_edit_form").serialize();
		contact_edit_form += "&id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(contact_edit_form);
		$.ajax({
			url: '/crm/customer_contact/update',
			type: 'post',
			data: contact_edit_form,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.contact_show(customer_id);
				}
			},
			error: function() {
                layer.msg('保存客户信息时发生错误!',{icon:2});
			}
		});
	};
	this.sale_chance_show=function(customer_id,open_edit_id,only_show){
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
                    $(sale_chance_panel+" .new-sale-chance").addClass("hide");
				});
                $(sale_chance_panel).on("focus",".create-sale-chance-select-window",function(){
                	console.log("create-sale-chance-select-window");
                    $(sale_chance_panel+" .select-window-container").remove();
                    console.log($(this).siblings(".select-window"));
                    $(this).siblings(".select-window").load("/index/index/select_window.html");
                    // $(sale_chance_panel+" .select-window").load("/index/index/select_window.html");
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
					var contract_item_id = $(this).attr("contract_item_id");
					var pop = new popLoad("#create-bill","/crm/bill/bill_apply/sale_id/"+edit_id+"/contract_item_id/"+contract_item_id);
				});
				$(panel+" .clientInfoSaleChance .sale-chance-record .bill-apply-retract").click(function(){
					var edit_id = $(this).parent().parent().parent().siblings(".z-recordState").children(".edit_id").val();
					self.bill_apply_retract(edit_id,self.id);
				});
				if(open_edit_id&&open_edit_id>0){
					var edit_flg = $(panel+" .clientInfoSaleChance .sale-chance-record-"+open_edit_id+" .sale_chance_edit").length;
					if(edit_flg==0||only_show==1){
                        var top = $(panel+" .clientInfoSaleChance .sale-chance-record-"+open_edit_id).offset().top;
                        console.log("top",top);
                        $(self.panel_base).scrollTop(top-94-94);
					}else{
                        self.sale_chance_edit(open_edit_id,0);
					}
				}
			},
			error:function(){
                layer.msg('获取销售机会失败!',{icon:2});
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
                $(new_sale_chance_panel+" .sale-chance-status_selecter").change(function(){
                    var status = $(new_sale_chance_panel+" .sale-chance-status_selecter").val();
                    // console.log("status",status);
                    $(new_sale_chance_panel+" .sale-chance").addClass("hide");
                    if(status==2){
                        $(new_sale_chance_panel+" .sale-chance-visit").removeClass("hide");
                    }else if(status==4){
                        $(new_sale_chance_panel+" .sale-chance-finish").removeClass("hide");
                    }
                });

                //业务类型
                var business_flow_item_index_json = $(new_sale_chance_panel+" .business_flow_selecter").siblings(".business_flow_item_index").val();
                //console.log(contract_type_name_json);
                if(business_flow_item_index_json){
                    var business_flow_item_index = null;
                    try{
                        business_flow_item_index = JSON.parse(business_flow_item_index_json);
                    }catch (ex){
                        console.log(ex);
                    }
                    if(business_flow_item_index==null){
                        console.log("business_flow_item_index data not found");
                        business_flow_item_index = [];
                    }
                    // console.log("business_flow_item_index",business_flow_item_index);
                    var get_business_flow_item_by_id = function(id){
                        var business_flow_item_string = "";
                        if(id in business_flow_item_index){
                        	console.log("business_flow_item_index[id]",business_flow_item_index[id]);
                        	for(idx in business_flow_item_index[id]){
                                // console.log("business_flow_item",business_flow_item_index[id][idx]);
                                business_flow_item_string += '<option value="'+business_flow_item_index[id][idx]["item_id"]+'">'+business_flow_item_index[id][idx]["item_name"]+'</option>';
							}
                        }
                        return business_flow_item_string;
                    };
                    var set_business_flow_item_by_id = function(target){
                        var business_flow_id = $(target).val();
                        if(!business_flow_id>0){
                            return;
                        }
                        var business_flow_item_string = get_business_flow_item_by_id(business_flow_id);
                        // console.log("business_flow_item_string",business_flow_item_string);
                        // console.log("sale-chance-status_selecter",$(new_sale_chance_panel+" .sale-chance-status_selecter"));
                        $(new_sale_chance_panel+" .sale-chance-status_selecter").html(business_flow_item_string);
                        $(new_sale_chance_panel+" .sale-chance-status_panel").removeClass("hide");
                        $(new_sale_chance_panel+" .sale-chance").addClass("hide");
                    };
                    $(new_sale_chance_panel).on("change",".business_flow_selecter",function(){
                        //console.log("contract_no_selecter");
                        set_business_flow_item_by_id(this);
                    });
                }
                var business_flow_role_index_json = $(new_sale_chance_panel+" .business_flow_selecter").siblings(".business_flow_role_index").val();
                var role_employee_index_json = $(new_sale_chance_panel+" .business_flow_selecter").siblings(".role_employee_index").val();
                //console.log(contract_type_name_json);
                if(business_flow_role_index_json&&role_employee_index_json){
                    var business_flow_role_index = null;
                    var role_employee_index = null;
                    try{
                        business_flow_role_index = JSON.parse(business_flow_role_index_json);
                        role_employee_index = JSON.parse(role_employee_index_json);
                    }catch (ex){
                        console.log(ex);
                    }
                    if(business_flow_role_index==null){
                        console.log("business_flow_role_index data not found");
                        business_flow_role_index = [];
                    }
                    if(role_employee_index==null){
                        console.log("role_employee_index data not found");
                        role_employee_index = [];
                    }
                    // console.log("business_flow_role_index",business_flow_role_index);
                    // console.log("role_employee_index",role_employee_index);
                    var get_handle_by_id = function(id){
                        var role_employee_string = "";
                        if(id in role_employee_index){
                            // console.log("role_employee_index[id]",role_employee_index[id]);
                            for(idx in role_employee_index[id]){
                                // console.log("business_flow_item",role_employee_index[id][idx]);
                                role_employee_string += '<option value="'+role_employee_index[id][idx]["user_id"]+'">'+role_employee_index[id][idx]["truename"]+'</option>';
                            }
                        }
                        return role_employee_string;
                    };
                    var set_handle_by_id = function(target){
                        var status = $(target).val();
                        if(!status>3){
                            return;
                        }
                        var business_flow_id = $(new_sale_chance_panel+" .business_flow_selecter").val();
                        if(!business_flow_id>0){
                            return;
                        }
                        $(new_sale_chance_panel+" .avtiv_handle").addClass("hide");
                        if(!business_flow_id in business_flow_role_index){
                            return;
                        }
                        if(!status in business_flow_role_index[business_flow_id]){
                            return;
                        }
                        var business_flow_role_list = business_flow_role_index[business_flow_id][status];
                        // console.log("business_flow_role_index[business_flow_id][status]",business_flow_role_index[business_flow_id][status]);
                        for(var num=1;num<7;num++){
                        	console.log("business_flow_role_list[handle_"+num+"]",business_flow_role_list["handle_"+num]);
                        	var role_id = business_flow_role_list["handle_"+num];
                        	if(role_id<=0){
                        		return;
							}
                            var handle_string = get_handle_by_id(role_id);
                            // console.log("handle_string_"+num,handle_string);
                            // console.log("sale-chance-status_selecter",$(new_sale_chance_panel+" .sale-chance-status_selecter"));
                            $(new_sale_chance_panel+" .handle_"+num).html(handle_string);
                            $(new_sale_chance_panel+" .cont-min"+num).removeClass("hide");
                            $(new_sale_chance_panel+" .handle_"+num).removeClass("hide");
                        }
                    };
                    $(new_sale_chance_panel).on("change",".sale-chance-status_selecter",function(){
                        //console.log("contract_no_selecter");
                        set_handle_by_id(this);
                    });
                }

                //合同类型
                var contract_type_name_json = $(new_sale_chance_panel+" .contract_no_selecter").siblings(".contract_type_name").val();
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
                        //console.log("contract_type_name",contract_type_name);
                        //console.log("contract_type",$(target).parent().children(".contract_type"));
                        $(target).parent().children(".contract_type").text(contract_type_name);
                    };
                    $(new_sale_chance_panel).on("change",".contract_no_selecter",function(){
                        //console.log("contract_no_selecter");
                        set_contract_type_name_by_id(this);
                    });
                    try{
                        var contracts = $(new_sale_chance_panel+" .sale-chance-apply-contract");
                        var contract_index = 0;
                        $(contracts).each(function(){
                            var contract_id_selecter = $(this).find("select[name='contract_id']");
                            set_contract_type_name_by_id(contract_id_selecter);
                            contract_index++;
                        });
                        self.contract_index = contract_index;
                        self.contract_count = contract_index;
                    }catch (ex){
                        console.log(ex);
                    }
                }

                //合同对应银行
                var contract_bank_name_json = $(new_sale_chance_panel+" .contract_no_selecter").siblings(".contract_bank_name").val();
                //console.log(contract_bank_name_json);
                if(contract_bank_name_json){
                    var contract_bank_name_index = null;
                    try{
                        contract_bank_name_index = JSON.parse(contract_bank_name_json);
                    }catch (ex){
                        console.log(ex);
                    }
                    if(contract_bank_name_index==null){
                        console.log("contract bank name data not found");
                        contract_bank_name_index = [];
                    }
                    //console.log(contract_bank_name_index);
                    var get_contract_bank_name_by_id = function(id){
                        var contract_bank_name = "";
                        if(id in contract_bank_name_index){
                            contract_bank_name = contract_bank_name_index[id];
                        }
                        return contract_bank_name;
                    };
                    var set_contract_bank_name_by_id = function(target){
                        var contract_id = $(target).val();
                        if(!contract_id>0){
                            return;
                        }
                        var contract_bank_name = get_contract_bank_name_by_id(contract_id);
                        //console.log("contract_bank_name",contract_bank_name);
                        var contract_bank_name_arr = contract_bank_name.split(",");
                        //console.log("contract_bank_name_arr",contract_bank_name_arr);
                        var pay_bank_default = $(target).parent().parent().find(".pay_bank_default").val();
                        // console.log("pay_bank_default",pay_bank_default);
                        var contract_bank_name_html = '';
                        for(var i in contract_bank_name_arr){
                            var contract_bank_name_item = contract_bank_name_arr[i];
                            // console.log("contract_bank_name_item",contract_bank_name_item);
                            contract_bank_name_html += "<option value='"+contract_bank_name_item+"'";
                            if(pay_bank_default == contract_bank_name_item){
                                contract_bank_name_html += ' selected="selected"';
                            }
                            contract_bank_name_html += ">"+contract_bank_name_item+"</option>";
                        }
                        //console.log("contract_bank_name_html",contract_bank_name_html);
                        //console.log("contract_bank",$(target).parent().parent().find(".pay_bank"));
                        $(target).parent().parent().find(".pay_bank").html(contract_bank_name_html);
                    };
                    $(new_sale_chance_panel).on("change",".contract_no_selecter",function(){
                        //console.log("contract_no_selecter");
                        set_contract_bank_name_by_id(this);
                    });
                    try{
                        var contracts = $(new_sale_chance_panel+" .sale-chance-apply-contract");
                        var contract_index = 0;
                        $(contracts).each(function(){
                            var contract_id_selecter = $(this).find("select[name='contract_id']");
                            set_contract_bank_name_by_id(contract_id_selecter);
                            contract_index++;
                        });
                        self.contract_index = contract_index;
                        self.contract_count = contract_index;
                    }catch (ex){
                        console.log(ex);
                    }
                }

                $(new_sale_chance_panel+" input[type='radio']").click(function(){
                    var radio_name = $(this).attr("name");
                    $(new_sale_chance_panel+" input[name='"+radio_name+"']").attr("checked",false);
                    $(new_sale_chance_panel+" input[name='"+radio_name+"']").prop("checked",false);
                    $(this).attr("checked","checked");
                    $(this).prop("checked","checked");
                });

                $(new_sale_chance_panel+" .sale_chance_add_save").click(function(){
					self.sale_chance_add_send(self.id);
                    $(".clientInfoSaleChance .new-sale-chance").removeClass("hide");

				});
				$(new_sale_chance_panel+" .sale_chance_add_cancel").click(function(){
					$(new_sale_chance_panel).remove();
                    $(".clientInfoSaleChance .new-sale-chance").removeClass("hide");
				});
			},
			error:function(){
                layer.msg('获取销售机会添加失败!',{icon:2});
			}
		});
	};
	this.sale_chance_add_send=function(customer_id){
		var panel = this.panel_base+' .customer_sale_chance';
		var sale_chance_panel = panel+" .clientInfoSaleChance";
		var new_sale_chance_panel = sale_chance_panel+" .create-sale-chance";
		var sale_chance_add_from = $(new_sale_chance_panel+" .newSaleChanceForm").serialize();
		sale_chance_add_from += "&customer_id="+customer_id+"&fr="+this.from;
        var sale_chance_add_select_associator = $(new_sale_chance_panel+" .create-sale-chance-select-window").attr("data-stf");
        sale_chance_add_from+="&associator_id="+sale_chance_add_select_associator;
        var status = $(new_sale_chance_panel+" .sale-chance-status_selecter").val();
        if(status==4){
            if(confirm("你确定要提交该成单申请吗?")!=true){
                return;
            }
            var contracts = $(new_sale_chance_panel+" .sale-chance-apply-contract");
            var contract_num = contracts.length;
            if(contract_num<=0){
                layer.msg('没有合同信息!',{icon:2});
                return false;
            }
            var contract_arr = [];
            $(contracts).each(function(){
                //console.log('this',$(this));
                var index = $(this).attr("index");
                //console.log('contract_id',$(this).find("select[name='contract_id']"));
                //console.log('need_bill_'+index,$(this).find("input[name='need_bill_"+index+"'][checked='checked']"));
                //console.log('contract_money',$(this).find("input[name='contract_money']"));
                var contract_id = $(this).find("select[name='contract_id']").val();
                var contract_money = $(this).find("input[name='contract_money']").val();
                var pay_money = $(this).find("input[name='pay_money']").val();
                var pay_name = $(this).find("input[name='pay_name']").val();
                var pay_type = $(this).find("input[name='pay_type_"+index+"'][checked='checked']").val();
                // console.log("pay_type",pay_type);
                var due_time = $(this).find("input[name='due_time']").val();
                var need_bill = $(this).find("input[name='need_bill_"+index+"'][checked='checked']").val();
                // console.log("need_bill",need_bill);
                //console.log('pay_bank',$(this).find("select[name='pay_bank_"+index+"']"));
                var pay_bank = $(this).find("select[name='pay_bank_"+index+"']").val();
                var contract_obj = {
                    contract_id:contract_id,
                    contract_money:contract_money,
                    pay_money:pay_money,
                    pay_name:pay_name,
                    pay_type:pay_type,
                    due_time:due_time,
                    need_bill:need_bill,
                    pay_bank:pay_bank
                };
                contract_arr.push(contract_obj);
            });
            //console.log('contract_arr',contract_arr);
            var contract_str = JSON.stringify(contract_arr);
            //console.log('contract_str',contract_str);
            sale_chance_add_from += "&contracts="+contract_str;
        }
        this.reload_flg = 1;
		// console.log("sale_chance_add_from",sale_chance_add_from);
		$.ajax({
			url: '/crm/sale_chance/add',
			type: 'post',
			data: sale_chance_add_from,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
                layer.msg('保存销售机会时发生错误!',{icon:2});
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
				$(panel+" .clientInfoSaleChance .sale-chance-record .sale_chance_edit").addClass("hide");
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

				//合同类型
				var contract_type_name_json = $(edit_sale_chance_panel+" .contract_no_selecter").siblings(".contract_type_name").val();
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
						//console.log("contract_type_name",contract_type_name);
						//console.log("contract_type",$(target).parent().children(".contract_type"));
						$(target).parent().children(".contract_type").text(contract_type_name);
					};
					$(edit_sale_chance_panel).on("change",".contract_no_selecter",function(){
						//console.log("contract_no_selecter");
						set_contract_type_name_by_id(this);
					});
					try{
						var contracts = $(edit_sale_chance_panel+" .editSaleChanceForm .sale-chance-apply-contract");
						var contract_index = 0;
						$(contracts).each(function(){
							var contract_id_selecter = $(this).find("select[name='contract_id']");
							set_contract_type_name_by_id(contract_id_selecter);
							contract_index++;
						});
						self.contract_index = contract_index;
						self.contract_count = contract_index;
					}catch (ex){
						console.log(ex);
					}
				}

				//合同对应银行
				var contract_bank_name_json = $(edit_sale_chance_panel+" .contract_no_selecter").siblings(".contract_bank_name").val();
				//console.log(contract_bank_name_json);
				if(contract_bank_name_json){
					var contract_bank_name_index = null;
					try{
						contract_bank_name_index = JSON.parse(contract_bank_name_json);
					}catch (ex){
						console.log(ex);
					}
					if(contract_bank_name_index==null){
						console.log("contract bank name data not found");
						contract_bank_name_index = [];
					}
					//console.log(contract_bank_name_index);
					var get_contract_bank_name_by_id = function(id){
						var contract_bank_name = "";
						if(id in contract_bank_name_index){
							contract_bank_name = contract_bank_name_index[id];
						}
						return contract_bank_name;
					};
					var set_contract_bank_name_by_id = function(target){
						var contract_id = $(target).val();
						if(!contract_id>0){
							return;
						}
						var contract_bank_name = get_contract_bank_name_by_id(contract_id);
						//console.log("contract_bank_name",contract_bank_name);
						var contract_bank_name_arr = contract_bank_name.split(",");
						//console.log("contract_bank_name_arr",contract_bank_name_arr);
						var pay_bank_default = $(target).parent().parent().find(".pay_bank_default").val();
						console.log("pay_bank_default",pay_bank_default);
						var contract_bank_name_html = '';
						for(var i in contract_bank_name_arr){
							var contract_bank_name_item = contract_bank_name_arr[i];
							console.log("contract_bank_name_item",contract_bank_name_item);
							contract_bank_name_html += "<option value='"+contract_bank_name_item+"'";
							if(pay_bank_default == contract_bank_name_item){
								contract_bank_name_html += ' selected="selected"';
							}
							contract_bank_name_html += ">"+contract_bank_name_item+"</option>";
						}
						//console.log("contract_bank_name_html",contract_bank_name_html);
						//console.log("contract_bank",$(target).parent().parent().find(".pay_bank"));
						$(target).parent().parent().find(".pay_bank").html(contract_bank_name_html);
					};
					$(edit_sale_chance_panel).on("change",".contract_no_selecter",function(){
						//console.log("contract_no_selecter");
						set_contract_bank_name_by_id(this);
					});
					try{
						var contracts = $(edit_sale_chance_panel+" .editSaleChanceForm .sale-chance-apply-contract");
						var contract_index = 0;
						$(contracts).each(function(){
							var contract_id_selecter = $(this).find("select[name='contract_id']");
							set_contract_bank_name_by_id(contract_id_selecter);
							contract_index++;
						});
						self.contract_index = contract_index;
						self.contract_count = contract_index;
					}catch (ex){
						console.log(ex);
					}
				}

				$(edit_sale_chance_panel+" input[type='radio']").click(function(){
					var radio_name = $(this).attr("name");
					$(edit_sale_chance_panel+" input[name='"+radio_name+"']").attr("checked",false);
					$(edit_sale_chance_panel+" input[name='"+radio_name+"']").prop("checked",false);
					$(this).attr("checked","checked");
					$(this).prop("checked","checked");
				});

				$(edit_sale_chance_panel+" .sale_chance_edit_save").click(function(){
					self.sale_chance_edit_send(id,self.id);
				});
				$(edit_sale_chance_panel+" .sale_chance_edit_cancel").click(function(){
					$(sale_chance_panel+" .sale-chance-record-"+id).removeClass("hide");
                    $(".clientInfoSaleChance .sale-chance-record .sale_chance_edit").removeClass("hide");
					$(edit_sale_chance_panel).remove();
				});
			},
			error:function(){
                layer.msg('获取销售机会编辑失败!',{icon:2});
			}
		});
	};
	this.sale_chance_edit_send=function(id,customer_id){
		console.log(id);
		console.log(customer_id);
		var panel = this.panel_base+' .customer_sale_chance';
		var sale_chance_panel = panel+" .clientInfoSaleChance";
		var edit_sale_chance_panel = sale_chance_panel+" .edit-sale-chance-"+id;
		var sale_chance_edit_form = $(edit_sale_chance_panel+" .editSaleChanceForm").serialize();
        var sale_chance_edit_select_associator = $(edit_sale_chance_panel+" .editSaleChanceForm .create-sale-chance-select-window").attr("data-stf");
        sale_chance_edit_form+="&associator_id="+sale_chance_edit_select_associator;
		var status = $(edit_sale_chance_panel+" .editSaleChanceForm .sale-chance-status_selecter").val();
		if(status==4){
			if(confirm("你确定要提交该成单申请吗?")!=true){
				return;
			}
			var contracts = $(edit_sale_chance_panel+" .editSaleChanceForm .sale-chance-apply-contract");
			var contract_num = contracts.length;
			if(contract_num<=0){
				layer.msg('没有合同信息!',{icon:2});
				return false;
			}
			var contract_arr = [];
			$(contracts).each(function(){
				//console.log('this',$(this));
				var index = $(this).attr("index");
				//console.log('contract_id',$(this).find("select[name='contract_id']"));
				//console.log('need_bill_'+index,$(this).find("input[name='need_bill_"+index+"'][checked='checked']"));
				//console.log('contract_money',$(this).find("input[name='contract_money']"));
				var contract_id = $(this).find("select[name='contract_id']").val();
				var contract_money = $(this).find("input[name='contract_money']").val();
				var pay_money = $(this).find("input[name='pay_money']").val();
				var pay_name = $(this).find("input[name='pay_name']").val();
				var pay_type = $(this).find("input[name='pay_type_"+index+"'][checked='checked']").val();
				console.log("pay_type",pay_type);
				var due_time = $(this).find("input[name='due_time']").val();
				var need_bill = $(this).find("input[name='need_bill_"+index+"'][checked='checked']").val();
				console.log("need_bill",need_bill);
				//console.log('pay_bank',$(this).find("select[name='pay_bank_"+index+"']"));
				var pay_bank = $(this).find("select[name='pay_bank_"+index+"']").val();
				var contract_obj = {
					contract_id:contract_id,
					contract_money:contract_money,
					pay_money:pay_money,
					pay_name:pay_name,
					pay_type:pay_type,
					due_time:due_time,
					need_bill:need_bill,
					pay_bank:pay_bank
				};
				contract_arr.push(contract_obj);
			});
			//console.log('contract_arr',contract_arr);
			var contract_str = JSON.stringify(contract_arr);
			//console.log('contract_str',contract_str);
			sale_chance_edit_form += "&contracts="+contract_str;
		}
		sale_chance_edit_form += "&id="+id+"&customer_id="+customer_id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_add_from);
		$.ajax({
			url: '/crm/sale_chance/update',
			type: 'post',
			data: sale_chance_edit_form,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
                layer.msg('保存销售机会时发生错误!',{icon:2});
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
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
                layer.msg('作废商机时发生错误!',{icon:2});
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
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
                layer.msg('撤回成单申请时发生错误!',{icon:2});
			}
		});
	};
	this.bill_apply_retract=function(id,customer_id){
		console.log(id);
		if(!window.confirm("确认撤回这个商机的发票申请吗?")){
			return false;
		}
		var sale_chance_invalid_data = "sale_id="+id+"&fr="+this.from;
		this.reload_flg = 1;
		//console.log(sale_chance_invalid_data);
		$.ajax({
			url: '/crm/bill/retract',
			type: 'post',
			data: sale_chance_invalid_data,
			dataType: 'json',
			success: function(data) {
				//console.log(data);
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.sale_chance_show(customer_id);
				}
			},
			error: function() {
                layer.msg('撤回发票申请时发生错误!',{icon:2});
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
                layer.msg('获取客户跟踪信息失败!',{icon:2});
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
                layer.msg(data.info,{icon:data.status==1?1:2});
				if(data.status) {
					self.trace_show(customer_id);
				}
			},
			error: function() {
                layer.msg('保存客户跟踪信息时发生错误!',{icon:2});
			}
		});
	};
}