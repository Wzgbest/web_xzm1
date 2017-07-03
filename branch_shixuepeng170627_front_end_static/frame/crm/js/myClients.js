/*****************************************************************/
$(".m-firNav li").click(function(){
	$(".m-firNav li").removeClass("current");
	$(this).addClass("current");
	var index = $(this).index();
	if(index){
		$(".table2").removeClass("hide");
		$(".table1").addClass("hide");
	}else{
		$(".table1").removeClass("hide");
		$(".table2").addClass("hide");
	}
});
/*新建*/
function newClient(){
	document.getElementById("newClient").classList.remove("hide");
	blackBgshow();
}
function removeNewClient(){
	document.getElementById("newClient").classList.add("hide");
	blackBghide();
}
/**********************************/
/*客户详情——客户信息——编辑*/
$("#clientInformation .edit i").click(function(){
	$("#clientInformation").addClass("hide");
	$("#clientInformationEdit").removeClass("hide");
});
/*客户详情-联系人-新建联系人*/
$("#new-linkman").click(function(){
	$("#creat-linkman").removeClass("hide");
});
$("#creat-linkman .close").click(function(){
	$("#creat-linkman").addClass("hide");
});
function fun1(){
	console.log($("#fff").val());
	var val=$("#fff").val();
	console.log(val);
	if(val==1){
		$(".sale-chance").addClass("hide");
		$(".sale-chance-intentional").removeClass("hide");
	}else if(val==2){
		$(".sale-chance").addClass("hide");
		$(".sale-chance-visit").removeClass("hide");
	}else if(val==3){
		$(".sale-chance").addClass("hide");
		$(".sale-chance-finish").removeClass("hide");
	}
}
$("#new-sale-chance").click(function(){
	$("#creat-sale-chance").removeClass("hide");
});
/*跟踪记录*/
$("#new-trace").click(function(){
	$("#creat-traceRecord").removeClass("hide");
});
/*新建翻页*/
function next1(){
	$("#form1").addClass("hide");
	$("#form2").removeClass("hide");
}
function next2(){
	$("#form2").addClass("hide");
	$("#form3").removeClass("hide");
}
function pre1(){
	$("#form2").addClass("hide");
	$("#form1").removeClass("hide");
}
function pre2(){
	$("#form3").addClass("hide");
	$("#form2").removeClass("hide");
}
