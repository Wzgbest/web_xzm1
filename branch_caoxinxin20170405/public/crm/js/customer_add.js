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

	//方法
	this.new_customer_show=function(data,panel){
		var html = '<div class="add_customer hide">';
		html += data+'</div>';
		$(panel).append(html);
		$(panel+" .add_customer .blackBg").height(window.innerHeight);
		$(panel+" .add_customer").removeClass("hide");
		//关闭按钮
		$(panel+" .add_customer .newClient header h1 .close").click(function(){
			self.removeNewClient();
		});
		//表单1按钮
		$(panel+" .add_customer .newClient .m-form .newClientInfoForm .add_new_customer").click(function(){
			self.add_customer(0);
		});
		$(panel+" .add_customer .newClient .m-form .newClientInfoForm .save_customer").click(function(){
			self.add_customer(3);
		});
		$(panel+" .add_customer .newClient .m-form .newClientInfoForm .add_customer_next").click(function(){
			self.add_customer(2);
		});
		$(panel+" .add_customer .newClient .m-form .newClientInfoForm .add_customer_cancel").click(function(){
			self.removeNewClient();
		});
		//表单2按钮
		$(panel+" .add_customer .newClient .m-form .newClientContactForm .add_contact_new_customer").click(function(){
			self.add_contact(0);
		});
		$(panel+" .add_customer .newClient .m-form .newClientContactForm .save_customer_contact").click(function(){
			self.add_contact(3);
		});
		$(panel+" .add_customer .newClient .m-form .newClientContactForm .add_customer_contact_previous").click(function(){
			self.add_contact(1);
		});
		$(panel+" .add_customer .newClient .m-form .newClientContactForm .add_customer_contact_next").click(function(){
			self.add_contact(2);
		});
		$(panel+" .add_customer .newClient .m-form .newClientContactForm .add_customer_cancel").click(function(){
			self.removeNewClient();
		});
		//表单3按钮
		$(panel+" .add_customer .newClient .m-form .newClientSaleChanceForm .add_sale_chance_new_customer").click(function(){
			self.add_sale_chance(0);
		});
		$(panel+" .add_customer .newClient .m-form .newClientSaleChanceForm .save_sale_chance").click(function(){
			self.add_sale_chance(3);
		});
		$(panel+" .add_customer .newClient .m-form .newClientSaleChanceForm .add_sale_chance_previous").click(function(){
			self.add_sale_chance(1);
		});
		$(panel+" .add_customer .newClient .m-form .newClientSaleChanceForm .add_customer_cancel").click(function(){
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
				alert("获取联系人失败!");
			}
		});
	};
	this.removeNewClient=function(){
		$(self.panel_base+" .add_customer").remove();
		if(self.new_customer_id > 0){
			self.list_manage.reload_list();
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
						alert(data.info);
					}
					self.next_status(next_status,self.removeNewClient,self.next_contact);
				}else{
					alert(data.info);
				}
			},
			error: function() {
				alert("保存失败!");
			}
		});
	};
	this.add_contact=function(next_status){
		var add_customer_contact_from_data = $(this.panel_base+" .m-form .newClientContactForm").serialize();
		add_customer_contact_from_data += "&customer_id="+this.new_customer_id;
		var url = '/crm/customer_contact/add';
		if(this.new_customer_contact_id>0){
			url = '/crm/customer_contact/update';
			add_customer_contact_from_data += "&id="+this.new_customer_contact_id;
		}
		//console.log(add_customer_contact_from_data);
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
						alert("保存成功!");
					}
					self.next_status(next_status,self.pre_customer,self.next_sale_chance);
				}else{
					alert(data.info);
				}
			},
			error: function() {
				alert("保存失败!");
			}
		});
	};
	this.add_sale_chance=function(next_status){
		var add_customer_sale_chance_from_data = $(this.panel_base+" .m-form .newClientSaleChanceForm").serialize();
		add_customer_sale_chance_from_data += "&customer_id="+this.new_customer_id;
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
						alert("保存成功!");
					}
					self.next_status(next_status,self.pre_contact,self.removeNewClient);
				}else{
					alert(data.info);
				}
			},
			error: function() {
				alert("保存失败!");
			}
		});
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
	};
	this.next_sale_chance=function(){
		$(self.panel_base+" .add_customer .newClient .newClientContactForm .form2").addClass("hide");
		$(self.panel_base+" .add_customer .newClient .newClientSaleChanceForm .form3").removeClass("hide");
	};
	this.pre_customer=function(){
		$(self.panel_base+" .add_customer .newClient .newClientContactForm .form2").addClass("hide");
		$(self.panel_base+" .add_customer .newClient .newClientInfoForm .form1").removeClass("hide");
	};
	this.pre_contact=function(){
		$(self.panel_base+" .add_customer .newClient .newClientSaleChanceForm .form3").addClass("hide");
		$(self.panel_base+" .add_customer .newClient .newClientContactForm .form2").removeClass("hide");
	};
}