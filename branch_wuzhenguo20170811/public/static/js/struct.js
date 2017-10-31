function Structure(dep_stf,dep_dep,stf_name,show_what,str,selector) {
	// body...
	this.d_s = JSON.parse(dep_stf);
	this.d_d = JSON.parse(dep_dep);
	this.s_n = JSON.parse(stf_name);
	this.s_w = show_what;//0只选择部门1只选择员工2都选择
	this.str = str.split(",");
	//选择器
	this.selector = selector;
	//初始数据
	this.data_dep = $(selector).attr("data-dep");
	this.data_stf = $(selector).attr("data-stf");
	
	let self = this;

	//初始化数据
	this.init = function(){
		if(self.data_dep||self.data_stf){
			console.log(self.data_dep,self.data_stf);
			if(self.data_dep){
				let arr_dep = self.data_dep.split(",");
				for(let x in arr_dep){
					$(".select-window-container .right-content .selected-department").append('<li data-selected-department-id="'+arr_dep[x]+'" class="selected-ds">'+self.d_d[arr_dep[x]].name+'<i class="fa fa-times-circle-o"></i></li>')
					$(".selected-department .selected-title p").text($(".selected-department .selected-ds").length);
				}
			}
			if(self.data_stf){
				let arr_stf = self.data_stf.split(",");			
				for(let y in arr_stf){
					// console.log(arr_stf[y],self.s_n[arr_stf[y]]);
					$(".select-window-container .right-content .selected-staff").append('<li data-selected-staff-id="'+arr_stf[y]+'" class="selected-ds">'+self.s_n[arr_stf[y]]+'<i class="fa fa-times-circle-o"></i></li>');
					$(".selected-staff .selected-title p").text($(".selected-staff .selected-ds").length);
				}
			}
			
		}	
	}
	this.init();
	//所有的部门
	this.getAllDep = function(){
		let arr = new Array();
		for(x in self.d_s){
			arr.push(x);
		}
		return arr;
	}
	this.all_dep = this.getAllDep();

	// 部门：人+子部门
	this.divide = function(){
		//存储最终结果
		let obj = new Object();
		//单个部门
		let d_sturcture = {
			peopel:[],
			department:[],
			p_id:0
		}
		//初始化对象
		for(let x in self.d_d){
			obj[x] = self.d_d[x];
			obj[x].peopel = new Array();
			obj[x].department = new Array();
		}		
		//添加子部门
		for(let x in obj){
			let p = obj[x].pid;
			if(p!=0){
				obj[p].department.push(x);
			}
		}
		return obj;
	}
	this.dep_struct = this.divide();
	//显示所有
	this.showAll = function(e){
		let arr1 = self.d_s[e];
		let arr2 = self.dep_struct[e].department;

		let u = getElementByAttr("ul", "data-selecting-department-id", e)[0];
		for(let key in arr1){
			if(self.s_n[arr1[key]]){
				$(u).append('<li class="stf-name" data-selecting-staff-id="'+arr1[key]+'">'+self.s_n[arr1[key]]+'</li>');
			}			
		}
		for(let key in arr2){
			if(arr2.length!=0){
				$(u).append('<li class="dep-struct-li"><ul data-level="2" class="dep-stuct-ul" data-selecting-department-id="'+arr2[key]+'"><li class="dep-name"><i class="fa fa-caret-right fa-myleft"></i><i class="fa fa-caret-down fa-myleft"></i><span>'+self.d_d[arr2[key]].name+'</span><i class="fa fa-user fa-myright" title="添加全员"></i><i class="fa fa-sitemap fa-myright" title="添加部门"></i></li></ul></li>');
				self.showAll(arr2[key]);
			}		
		}
	}
	//只显示部门
	this.showDep = function(e){
		let arr2 = self.dep_struct[e].department;
		let u = getElementByAttr("ul", "data-selecting-department-id", e)[0];
		for(let key in arr2){
			if(arr2.length!=0){
				$(u).append('<li class="dep-struct-li"><ul data-level="2" class="dep-stuct-ul" data-selecting-department-id="'+arr2[key]+'"><li class="dep-name"><i class="fa fa-caret-right fa-myleft"></i><i class="fa fa-caret-down fa-myleft"></i><span>'+self.d_d[arr2[key]].name+'</span><i class="fa fa-user fa-myright" title="添加全员"></i><i class="fa fa-sitemap fa-myright" title="添加部门"></i></li></ul></li>');
				self.showDep(arr2[key]);
			}	
		}
	}

	//显示
	this.show = function(){
		if(self.s_w==0){
			self.showDep(1);
			$(".select-window-container .left-content .dep-name .fa-user").remove();
			$(".select-window-container .right-content .selected-staff").remove();
		}else if(self.s_w==1){
			self.showAll(1);
			$(".select-window-container .left-content .dep-name .fa-sitemap").remove();
			$(".select-window-container .right-content .selected-department").remove();
		}else if(self.s_w==2){
			self.showAll(1);
		}else{
			console.log(self.s_w)
			layer.msg("参数错误");
		}
	}
	this.show();

	//添加单个人
	$(".select-window-container .left-content").on("click","li.stf-name",function(){
		if(getElementByAttr("li","data-selected-staff-id",$(this).attr("data-selecting-staff-id")).length==0){
			$(".select-window-container .right-content .selected-staff").append('<li data-selected-staff-id="'+$(this).attr("data-selecting-staff-id")+'" class="selected-ds">'+$(this).text()+'<i class="fa fa-times-circle-o"></i></li>');
			$(".selected-staff .selected-title p").text($(".selected-staff .selected-ds").length);
		}else{
			layer.msg("已存在");
		}
		
	});
	//添加一群人
	$(".select-window-container .left-content").on("click","li.dep-name .fa-user",function(){
		let arr3 = $(this).parent().siblings("li.stf-name");
		for(let x=0;x<arr3.length;x++){
			console.log($(arr3[x]).attr("data-selecting-staff-id"));
			if(getElementByAttr("li","data-selected-staff-id",$(arr3[x]).attr("data-selecting-staff-id")).length==0){
				$(".select-window-container .right-content .selected-staff").append('<li data-selected-staff-id="'+$(arr3[x]).attr("data-selecting-staff-id")+'" class="selected-ds">'+$(arr3[x]).text()+'<i class="fa fa-times-circle-o"></i></li>');
			}
		}
		$(".selected-staff .selected-title p").text($(".selected-staff .selected-ds").length);
	});
	//删除单个人
	$(".select-window-container .right-content .selected-staff").on("click","li.selected-ds .fa-times-circle-o",function(){
		$(this).parent().remove();
		$(".selected-staff .selected-title p").text($(".selected-staff .selected-ds").length);
	});
	//添加单个部门
	$(".select-window-container .left-content").on("click","li.dep-name .fa-sitemap",function(){
		if(getElementByAttr("li","data-selected-department-id",$(this).parent().parent().attr("data-selecting-department-id")).length==0){
			$(".select-window-container .right-content .selected-department").append('<li data-selected-department-id="'+$(this).parent().parent().attr("data-selecting-department-id")+'" class="selected-ds">'+$(this).siblings("span").text()+'<i class="fa fa-times-circle-o"></i></li>')
			$(".selected-department .selected-title p").text($(".selected-department .selected-ds").length);
		}else{
			layer.msg("已存在");
		}
		
	});
	//删除单个部门
	$(".select-window-container .right-content .selected-department").on("click","li.selected-ds .fa-times-circle-o",function(){
		$(this).parent().remove();
		$(".selected-department .selected-title p").text($(".selected-department .selected-ds").length);
	});
	//取消
	$(".right-content button.false").click(function(){
		$(".select-window-container").remove();
	});
	//确定
	$(".right-content button.true").click(function(){
		let arr1 = $(".right-content .selected-staff .selected-ds");//员工数据
		let arr2 = $(".right-content .selected-department .selected-ds");//部门数据
		let result1=new Array(),result2=new Array();
		for(let x=0;x<arr1.length;x++){
			result1.push($(arr1[x]).attr("data-selected-staff-id"));
		}
		for(let x=0;x<arr2.length;x++){
			result2.push($(arr2[x]).attr("data-selected-department-id"));
		}
		$(self.selector).attr("data-stf",result1).attr("data-dep",result2);
		console.log(result1,result2);


			var data_val = '';
			console.log(self.str);
			var header = self.str[0];//dep修饰符
			console.log(header);
			var delimiter = self.str[1];//dep连接符
			console.log(delimiter);
			var data_val1 = header+result2.join(delimiter+header);
			console.log(data_val1);


			header = self.str[3];//stf修饰符
			console.log(header);
			delimiter = self.str[4];//stf连接符
			console.log(delimiter);
			var data_val2 = header+result1.join(delimiter+header);
			console.log(data_val2);

			data_val=data_val1+self.str[2];+data_val2;
			console.log(data_val);
			console.log(self.s_w)
			if(self.s_w==0){
				console.log(0);
				$(self.selector).attr("data-dep",result2);
				$(self.selector).val("已选择"+result2.length+"个部门").attr("data-result",data_val1);
			}else if (self.s_w==1) {
				console.log(1);
				$(self.selector).attr("data-stf",result1);
				$(self.selector).val("已选择"+result1.length+"个人员").attr("data-result",data_val2)
			}else if(self.s_w==2){
				console.log(2);
				$(self.selector).attr("data-stf",result1).attr("data-dep",result2);
				$(self.selector).val("已选择"+result2.length+"个部门"+result1.length+"个人员").attr("data-result",data_val);
			}else{
				layer.msg("参数错误");
			}			
			$(".select-window-container").remove();
	});
}
