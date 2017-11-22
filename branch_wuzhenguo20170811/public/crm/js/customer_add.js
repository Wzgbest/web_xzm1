function customer_add(from,target,list_manage){
	this.from = from;
	this.target = target;
	this.list_manage = list_manage;
	this.new_customer_id = 0;
	this.new_customer_contact_id = 0;
	this.new_customer_sale_chance_id = 0;
	this.panel_base = '#frames #'+this.target+' .crm_'+this.from;
	var self = this;

	//绑定事件
	//新建按钮
	$(this.panel_base+" ."+this.from+" .m-secNav .showNewClient").click(function(){
		self.new_customer();

	});
    // console.log(this.panel_base);


	//方法
	this.new_customer_show=function(data,panel){
		var html = '<div class="add_customer hide">';
		html += data+'</div>';
		$(panel).append(html);
		// $(panel+" .add_customer .blackBg").height(window.innerHeight);
		$(panel+" .add_customer").removeClass("hide");
		//关闭按钮
		$(panel+" .add_customer .newClient .u-pop-header .close").click(function(){
			self.removeNewClient();
		});
		//表单1按钮
		$(panel+" .add_customer .customer-add-page-footer1 .add_new_customer").click(function(){
			//保存并添加下一位客户
			self.add_customer(0);
		});
		$(panel+" .add_customer .customer-add-page-footer1 .save_customer").click(function(){
			//保存
			self.add_customer(3);
		});
		$(panel+" .add_customer .customer-add-page-footer1 .add_customer_next").click(function(){
			//下一步
			self.add_customer(2);

		});
		$(panel+" .add_customer .customer-add-page-footer1 .add_customer_cancel").click(function(){
			//取消
			self.removeNewClient();
		});
		//表单2按钮
		$(panel+" .add_customer .customer-add-page-footer2 .add_contact_new_customer").click(function(){
			//保存并添加下一位客户
			self.add_contact(0);
		});
		$(panel+" .add_customer .customer-add-page-footer2 .save_customer_contact").click(function(){
			//保存
			self.add_contact(3);
		});
		$(panel+" .add_customer .customer-add-page-footer2 .add_customer_contact_previous").click(function(){
			//上一步
			self.add_contact(1);
		});
		$(panel+" .add_customer .customer-add-page-footer2 .add_customer_contact_next").click(function(){
			//下一步
			self.add_contact(2);
		});
		$(panel+" .add_customer .customer-add-page-footer2 .add_customer_cancel").click(function(){
            //取消
			self.removeNewClient();
		});

        $(this.panel_base).on("focus",".create-sale-chance-select-window",function(){
            console.log("create-sale-chance-select-window");
            $(self.panel_base+" .select-window-container").remove();
            console.log($(this).siblings(".select-window"));
            $(this).siblings(".select-window").load("/index/index/select_window.html");
            // $(sale_chance_panel+" .select-window").load("/index/index/select_window.html");
        });
        var new_sale_chance_panel = this.panel_base+" .newClientSaleChanceForm";
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
                $(new_sale_chance_panel+" .sale-chance").addClass("hide");
                $(new_sale_chance_panel+" .sale-chance-status_panel").removeClass("hide");
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

		//表单3按钮
		$(panel+" .add_customer .customer-add-page-footer3 .add_sale_chance_new_customer").click(function(){
            //保存并添加下一位
			self.add_sale_chance(0);
		});
		$(panel+" .add_customer .customer-add-page-footer3 .save_sale_chance").click(function(){
            //保存
			self.add_sale_chance(3);
		});
		$(panel+" .add_customer .customer-add-page-footer3 .add_sale_chance_previous").click(function(){
            //上一步
			self.add_sale_chance(1);
		});
		$(panel+" .add_customer .customer-add-page-footer3 .add_customer_cancel").click(function(){
            //取消
			self.removeNewClient();
		});
	};
	this.new_customer=function(){
		this.new_customer_id=0;
		this.new_customer_contact_id = 0;
		this.new_customer_sale_chance_id = 0;
		var url = "/crm/customer/add_page/fr/"+this.from;
		$.ajax({
			url:url,
			type:'get',
			async:false,
			success:function (data) {
				self.new_customer_show(data,self.panel_base);
			},
			error:function(){
                layer.msg('加载新建客户界面失败!',{icon:2});
			}
		});
	};
	this.removeNewClient=function(){
		$(this.panel_base+" .add_customer").remove();
		if(this.new_customer_id > 0){
			this.list_manage.reload_list();
		}
	};
	this.new_add_customer=function(){
		this.removeNewClient();
		this.new_customer();
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
	this.add_customer=function(next_status){
		if(!this.check_form_html5($(this.panel_base+" .m-form .newClientInfoForm").get(0).elements)){
			return;
		}
		var add_customer_from_data = $(this.panel_base+" .m-form .newClientInfoForm").serialize();
		var url = '/crm/customer/add';
		if(this.new_customer_id>0){
			url = '/crm/customer/update';
			add_customer_from_data += "&id="+this.new_customer_id;
		}
		//console.log(this.add_customer_from_data);
		$.ajax({
			url: url,
			type: 'post',
			data: add_customer_from_data,
			success: function(data) {
				if(data.status) {
					if(self.new_customer_id==0) {
						self.new_customer_id = data.data;
					}
					if(next_status==0 || next_status==3){
                        layer.msg(data.info,{icon:data.status==1?1:2});
					}
					self.next_status(next_status,self.removeNewClient,self.next_contact);
				}else{
                    layer.msg(data.info,{icon:data.status==1?1:2});
				}
			},
			error: function() {
                layer.msg('保存失败!',{icon:2});
			}
		});
	};
	this.add_contact=function(next_status){
		if(next_status!=1)
		{
            //console.log(add_customer_contact_from_data);
			if(($('.newClientContactForm input[name="contact_name"]').val() || $('.newClientContactForm input[name="phone_first"]').val() || $('.newClientContactForm input[name="phone_second"]').val() || $('.newClientContactForm input[name="phone_third"]').val()) || next_status==0 || next_status==3)
			{
				//填写联系人后的下一步可以后台提交，否则直接跳过。点保存按钮可以后台提交
                if(!this.check_form_html5($(this.panel_base+" .m-form .newClientContactForm").get(0).elements)){
                    return;
                }
                var add_customer_contact_from_data = $(this.panel_base+" .m-form .newClientContactForm").serialize();
                add_customer_contact_from_data += "&customer_id="+this.new_customer_id;
                var url = '/crm/customer_contact/add';
                if(this.new_customer_contact_id>0){
                    url = '/crm/customer_contact/update';
                    add_customer_contact_from_data += "&id="+this.new_customer_contact_id;
                }
                $.ajax({
                    url: url,
                    type: 'post',
                    data: add_customer_contact_from_data,
                    success: function(data) {
                        if(data.status) {
                            if(self.new_customer_contact_id==0) {
                                self.new_customer_contact_id = data.data;
                            }
                            if(next_status==0 || next_status==3){
                                layer.msg('保存成功!',{icon:1});
                            }
                            self.next_status(next_status,self.pre_customer,self.next_sale_chance);
                        }else{
                            layer.msg(data.info,{icon:data.status==1?1:2});
                        }
                    },
                    error: function() {
                        layer.msg('保存失败!',{icon:2});
                    }
                });
			}
			else
			{
                self.next_status(next_status,self.pre_customer,self.next_sale_chance);
			}
		}
		else
		{
            self.next_status(next_status,self.pre_customer,self.next_sale_chance);
		}
	};
	this.add_sale_chance=function(next_status){
		if(next_status!=1)
		{
            //保存当前表单，1时为上一步，0表示保存并添加下一位，3表示保存
            if(!this.check_form_html5($(this.panel_base+" .m-form .newClientSaleChanceForm").get(0).elements)){
                return;
            }
            var add_customer_sale_chance_from_data = $(this.panel_base+" .m-form .newClientSaleChanceForm").serialize();
            console.log(add_customer_sale_chance_from_data);
            add_customer_sale_chance_from_data += "&customer_id="+this.new_customer_id;
            var new_sale_chance_panel = this.panel_base+" .newClientSaleChanceForm";
            var sale_chance_add_select_associator = $(new_sale_chance_panel+" .create-sale-chance-select-window").attr("data-stf");
            add_customer_sale_chance_from_data+="&associator_id="+sale_chance_add_select_associator;
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
                add_customer_sale_chance_from_data += "&contracts="+contract_str;
            }
            var url = '/crm/sale_chance/add';
            if(this.new_customer_sale_chance_id>0){
                url = '/crm/sale_chance/update';
                add_customer_sale_chance_from_data += "&id="+this.new_customer_sale_chance_id;
            }
            //console.log(add_customer_sale_chance_from_data);
            $.ajax({
                url: url,
                type: 'post',
                data: add_customer_sale_chance_from_data,
                success: function(data) {
                    if(data.status) {
                        if(self.new_customer_sale_chance_id==0) {
                            self.new_customer_sale_chance_id = data.data;
                        }
                        if(next_status==0 || next_status==3){
                            layer.msg('保存成功!',{icon:1});
                        }
                        self.next_status(next_status,self.pre_contact,self.removeNewClient);
                    }else{
                        layer.msg(data.info,{icon:data.status==1?1:2});
                    }
                },
                error: function() {
                    layer.msg('保存失败!',{icon:2});
                }
            });
		}
		else
		{
            self.next_status(next_status,self.pre_contact,self.removeNewClient);
		}
	};
	//next_status 0:新建;1:上一页;2:下一页;3:退出;
	//func_previous 上一页的方法
	//func_next 下一页的方法
	this.next_status=function(next_status,func_previous,func_next){
		if(!next_status){
			self.new_add_customer();
		}else if(next_status==1){
			func_previous();
		}else if(next_status==2){
			func_next();
		}else if(next_status==3){
			self.removeNewClient();
		}
	};
	/*新建翻页*/
	this.next_contact=function(){
		$(self.panel_base+" .add_customer .newClient .newClientInfoForm .form1").addClass("hide");
		$(self.panel_base+" .add_customer .newClient .newClientContactForm .form2").removeClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer1").addClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer2").removeClass("hide");
	};
	this.next_sale_chance=function(){
		$(self.panel_base+" .add_customer .newClient .newClientContactForm .form2").addClass("hide");
		$(self.panel_base+" .add_customer .newClient .newClientSaleChanceForm .form3").removeClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer2").addClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer3").removeClass("hide");
	};
	this.pre_customer=function(){
		$(self.panel_base+" .add_customer .newClient .newClientContactForm .form2").addClass("hide");
		$(self.panel_base+" .add_customer .newClient .newClientInfoForm .form1").removeClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer2").addClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer1").removeClass("hide");
	};
	this.pre_contact=function(){
		$(self.panel_base+" .add_customer .newClient .newClientSaleChanceForm .form3").addClass("hide");
		$(self.panel_base+" .add_customer .newClient .newClientContactForm .form2").removeClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer3").addClass("hide");
		$(self.panel_base+" .add_customer .customer-add-page-footer2").removeClass("hide");
	};



    $(self.panel_base).on("click",".remark i.fa-pencil",function(){
        $(this).siblings("input").removeAttr("readonly").focus();
        $(this).addClass("hide").siblings(".fa-check").removeClass("hide");
    });
    $(self.panel_base).on("click",".remark i.fa-close",function(){
        var that=$(this);
        var id=$(this).siblings("input").attr('data-id')||'';//需要删除的标签id
        if(!id)
        {
            //未输入标签保存到表的可直接移除
            that.parent(".remark").remove();
            return;
        }
        $.ajax({
            url: '/crm/customer_remark/delete',
            type: 'post',
            data: {'id':id},
            success: function(data) {
                if(data.success)
                {
                    that.parent(".remark").remove();
                }
                else
                {
                    layer.msg(data.msg,{icon:2});
                }
            },
            error: function() {
                layer.msg('申请时发生错误!',{icon:2});
            }
        });

    });
    $(self.panel_base).on("click",".remark i.fa-check",function(){
        var that=$(this);
        var id=$(this).siblings("input").attr('data-id')||'';//id有值编辑，未定义则新增
        var title=$(this).siblings("input").val();
        if(!title)
        {
            layer.msg('请输入标签名称!',{icon:2});
            that.siblings("input").removeAttr("readonly").focus();
            return;
        }
        that.siblings("input").attr("readonly","readonly");
        $.ajax({
            url: '/crm/customer_remark/edit',
            type: 'post',
            data: {'id':id,'title':title},
            success: function(data) {
                if(data.success)
                {
                    that.addClass("hide").siblings(".fa-pencil").removeClass("hide");
                    if(data.num)
                    {
                        that.siblings("input").attr('data-id',data.num);//新增的将id传回来
                    }
                }
                else
                {
                    layer.msg(data.msg,{icon:2});
                }
            },
            error: function() {
                layer.msg('申请时发生错误!',{icon:2});
            }
        });
    });
    // $(self.now_form+" .remark input").off("click");//解绑点击事件
    $(self.panel_base).on("click",".remark input",function(){
        if($(this).attr("readonly")){
            var tex = $(this).parent(".remark").siblings("textarea[name='remark']");
            tex.val(tex.val()+$(this).val());
        }
    });

    var txt = '<span class="remark"><input type="text" placeholder="请输入" /><i class="fa fa-pencil hide"></i><i class="fa fa-check"></i><i class="fa fa-close"></i></span>';
    $(self.panel_base).on("click",".u-addRemark",function(){
        $(this).before(txt).siblings(".remark").last().children("input").focus();
    });

}