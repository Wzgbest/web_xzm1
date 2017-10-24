function select_window(container,depOrStf,result_selctor,result_selctor_attr,dep_decration,dep_connector,dep_stf_connector,stf_decration,stf_connector) {
	//最终值
	let result={
		dep:[],
		stf:[],
		depOrStf:2,
		result_selctor:'',
		result_selctor_attr:'',
		dep_decration:'',
		dep_connector:'',
		dep_stf_connector:'',
		stf_decration:'',
		stf_connector:''
	}
	// container:哪个容器来包含该插件
	// depOrStf:0代表只选择部门，1代表只选择员工，2代表都选择
	//result_selctor:最终数据传输到那个选择器
	//result_selctor_attr:最终数据传输到那个选择器的哪个属性
	$(container).load("/index/index/select_window.html",function(){
		
		result.depOrStf=depOrStf;
		depStf(result.depOrStf);
		result.result_selctor=result_selctor;
		result.result_selctor_attr=result_selctor_attr;
		console.log(result);
		if(dep_decration){
			console.log(dep_decration);
			result.dep_decration=dep_decration;
		}
		if (dep_connector) {
			console.log(dep_connector);
			result.dep_connector=dep_connector;
		}
		if (dep_stf_connector) {
			console.log(dep_stf_connector);
			result.dep_stf_connector=dep_stf_connector;
		}
		if (stf_decration) {
			console.log(stf_decration);
			result.stf_decration=stf_decration;
		}
		if (stf_connector) {
			console.log(stf_connector);
			result.stf_connector=stf_connector;
		}

		//开关
		$(".select-window-container dl dt span").click(function() {
			console.log(result.depOrStf);
			$(this).parent().parent().toggleClass("active");
		});

		$(".select-window-container dl dt .fa-myleft").click(function() {
			console.log(result.depOrStf);
			$(this).parent().parent().toggleClass("active");
		});

		//单个选中人员
		$(".selecting-department").siblings("dd").click(function(){
			if(result.depOrStf!=0){
				console.log($(this));
				let arr = getElementByAttr('dd','data-selected-staff-id',$(this).attr("data-selecting-staff-id"));
				if(arr.length==0){
					$(".selected-staff").append('<dd data-selected-staff-id="'+$(this).attr("data-selecting-staff-id")+'">'+$(this).text()+'<i class="fa fa-times-circle-o"></i></dd>');
					$(".selected-staff dt p").text($(".selected-staff dd").length);
					result.stf.push($(this).attr("data-selecting-staff-id"));
				}else if(arr.length==1){
					alert("已存在");
				}else{
					alert("参数错误");
				}
			}
		});

		//单个选中群组
		$(".selecting-department .fa-sitemap").click(function(){
			if (result.depOrStf!=1) {
				let arr = getElementByAttr('dd','data-selected-department-id',$(this).siblings("span").attr("data-selecting-department-id"));
				if(arr.length==0){
					$(".selected-department").append('<dd data-selected-department-id="'+$(this).siblings("span").attr("data-selecting-department-id")+'">'+$(this).siblings("span").text()+'<i class="fa fa-times-circle-o"></i></dd>');
					$(".selected-department dt p").text($(".selected-department dd").length);
					result.dep.push($(this).siblings("span").attr("data-selecting-department-id"));
				}else if(arr.length==1){
					alert("已存在");
				}else{
					alert("参数错误");
				}
			}	
		});

		//添加群组全员
		$(".selecting-department .fa-user").click(function(){
			if(result.depOrStf!=0){
				let arr = $(this).parent().siblings("dd");
				for (var i = 0; i < arr.length; i++) {
					let arr2 = getElementByAttr('dd','data-selected-staff-id',arr.eq(i).attr("data-selecting-staff-id"));
					if(arr2.length==0){
						$(".selected-staff").append('<dd data-selected-staff-id="'+arr.eq(i).attr("data-selecting-staff-id")+'">'+arr.eq(i).text()+'<i class="fa fa-times-circle-o"></i></dd>');
						result.stf.push(arr.eq(i).attr("data-selecting-staff-id"));
					}
				}
				$(".selected-staff dt p").text($(".selected-staff dd").length);
			}		
		});

		//删除--删除部门
		$(".right-content-container dl.selected-department").on("click","dd .fa-times-circle-o",function(){
			$(this).parent().remove();
			result.dep.splice(result.dep.indexOf($(this).parent().attr("data-selected-department-id")),1);
			$(".selected-department dt p").text($(".selected-department dd").length);
		});

		//删除--删除员工
		$(".right-content-container dl.selected-staff").on("click","dd .fa-times-circle-o",function(){
			if(result.depOrStf!=0){
				$(this).parent().remove();
				result.stf.splice(result.stf.indexOf($(this).parent().attr("data-selected-staff-id")),1);
				$(".selected-staff dt p").text($(".selected-staff dd").length);
			}
		});

		//完成
		$(".right-content button.true").click(function(){
			
			var data_val = '';

			var header = result.dep_decration;
			var delimiter = result.dep_connector;
			var dep_arr = result.dep;
			console.log(dep_arr);
			var data_val1 = header+dep_arr.join(delimiter+header);
			// data_val += header+dep_arr.join(delimiter+header);
			console.log(data_val1);


			header = result.stf_decration;
			delimiter = result.stf_connector;
			var stf_arr = result.stf;
			var data_val2 = header+stf_arr.join(delimiter+header);
			// data_val += delimiter+header+stf_arr.join(delimiter+header);
			console.log(data_val2);

			data_val=data_val1+result.dep_stf_connector+data_val2;
			console.log(data_val);

			if(result.depOrStf==0){
				$(result.result_selctor).val("已选择"+result.dep.length+"个部门").attr(result.result_selctor_attr,data_val1);
			}else if (result.depOrStf==1) {
				$(result.result_selctor).val("已选择"+result.stf.length+"个人员").attr(result.result_selctor_attr,data_val2)
			}else if(result.depOrStf==2){
				$(result.result_selctor).val("已选择"+result.dep.length+"个部门"+result.stf.length+"个人员").attr(result.result_selctor_attr,data_val);
			}else{
				alert("参数错误");
			}			
			$(".select-window-container").remove();
		});
		//取消
		$(".right-content button.false").click(function(){
			$(".select-window-container").remove();
		});

		// 控制部门和员工的选择
		function depStf(val){
			console.log(val);
			if(val==0){
				console.log(val);
				$(".select-window-container .left-content .selecting-department i.fa-myleft").remove();
				$(".select-window-container .left-content .selecting-department i.fa-user").remove();
				$(".select-window-container .left-content dl dd").addClass("hide");
				$(".select-window-container .right-content .selected-staff").remove();
			}else if(val==1){
				$(".select-window-container .left-content .selecting-department i.fa-sitemap").remove();
				$(".select-window-container .right-content .selected-department").remove();				
			}
		}

	});
}



