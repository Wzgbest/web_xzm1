$(".m-firNav li").click(function(){
	$(".m-firNav li").removeClass("current");
	$(this).addClass("current");
});
//var content1 = "<ul class='u-tabList'><li class='u-tabCheckbox'><input type='checkbox'/></li><li class='u-tabCilentName'></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabLinkWay'></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabOperation'><span>详情</span><span>编辑</span><span>释放</span></li><div class='clearfix'></div></ul>";
var content2 = "<ul class='u-tabList'><li class='u-tabCheckbox'><input type='checkbox'/></li><li class='u-tabCilentName'></li><li></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabLinkWay'></li><li></li><li></li><li></li><li class='u-tabOperation'><span>详情</span><span>编辑</span><span>释放</span></li><div class='clearfix'></div></ul>";

var t;
t=5;
for(var i=0;i<t;i++){
	//$(".table1").append(content1);
	$(".table2").append(content2);
}
function listNumChange(p,in_column){
	console.log($(".u-tabControlRow select").val());
	num = $(".u-tabControlRow select").val();
	my_customer_change_page(p,num,in_column);
}
function my_customer_previous_page(p,num,in_column){
	if(p-1<1){
		return;
	}
	my_customer_change_page(p-1,num,in_column);
}
function my_customer_next_page(p,num,max,in_column){
	if(p+1>max){
		return;
	}
	my_customer_change_page(p+1,num,in_column);
}
function my_customer_jump_page(num,max,in_column){
	console.log($(".my_customer_jump_page").val());
	p = $(".my_customer_jump_page").val();
	if(p+1>max || p-1<1){
		return;
	}
	my_customer_change_page(p,num,in_column);
}
function my_customer_change_page(p,num,in_column){
	loadPage("/crm/customer/my_customer/p/"+p+"/num/"+num+"/in_column/"+in_column,"myclietsfr");
}
/***************************/
$("#blackBg").height(window.innerHeight);
/*****************************************************************/
/*新建*/
function newClient(){
	document.getElementById("newClient").classList.remove("hide");
	document.getElementById("blackBg").classList.remove("hide");
//	document.getElementsByTagName("body")[0].classList.add("hiddenY");
}
function removeNewClient(){
	document.getElementById("newClient").classList.add("hide");
	document.getElementById("blackBg").classList.add("hide");
}

/**********************************/
//详情
//$(".page-client").hide();
//	$(".page-info").show();
	
/*$(".page-info").hide();
$(".tab-list .tab-operation").eq(0).click(function(){
	$(".page-client").hide();
	$(".page-info").show();
});*/
/*焦点获取失去的交互*/
/*$(".m-form input").focus(function(){
	$(this).addClass("focus");
});
$(".m-form input").blur(function(){
	$(this).removeClass("focus");
});
$(".m-form textarea").focus(function(){
	$(this).addClass("focus");
});
$(".m-form textarea").blur(function(){
	$(this).removeClass("focus");
});*/
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