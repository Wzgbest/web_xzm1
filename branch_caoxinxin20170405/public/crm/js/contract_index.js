function list_manager(page,form,array){
	//初始状态
	list_show(form,array[0]);
	//分页切换
	$("."+page+" .m-firNav li").click(function(){
		var num = $(this).attr("in_column");
		list_show(form,array[num]);
		//选中状态
		$(this).addClass("current").siblings("li").removeClass("current");
	});
}
function table_length_change(form,listNum){
	$("."+form).width(listNum*137);
}
function list_show(form,arr){
	table_length_change(form,arr.length);
	$("."+form+" ul li").addClass("hide");
	for(var i=0;i<arr.length;i++){
		if(typeof arr[i]=="number"){
			$("."+form+" .u-tabTitle li").eq(arr[i]).removeClass("hide");
			var len = $("."+form+" .u-tabList").length;
			for(var j=0;j<len;j++){
				$("."+form+" .u-tabList").eq(j).children("li").eq(arr[i]).removeClass("hide");
			}
//			$("."+form).children("u-tabList").children("li").eq(arr[i]).removeClass("hide");
//			$("."+form+" .u-tabList li").eq(arr[i]).removeClass("hide");
		}else{
			//列表名字变更，字段不变
			$("."+form+" .u-tabTitle li").eq(arr[i][0]).html(arr[i][1]+arr[i][2]);
			$("."+form+" .u-tabTitle li").eq(arr[i][0]).removeClass("hide");
//			$("."+form+" .u-tabList li").eq(arr[i][0]).removeClass("hide");
			var len = $("."+form+" .u-tabList").length;
			for(var j=0;j<len;j++){
				$("."+form+" .u-tabList").eq(j).children("li").eq(arr[i][0]).removeClass("hide");
			}
		}
	}
}
