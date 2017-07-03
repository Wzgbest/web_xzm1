/***************************/
var content1 = "<ul class='u-tabList'><li class='u-tabCheckbox'><input type='checkbox'/></li><li class='u-tabCilentName'></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabLinkWay'></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabOperation'><span>详情</span><span>编辑</span><span>释放</span></li><div class='clearfix'></div></ul>";
var content2 = "<ul class='u-tabList'><li class='u-tabCheckbox'><input type='checkbox'/></li><li class='u-tabCilentName'></li><li></li><li></li><li></li><li></li><li></li><li></li><li class='u-tabLinkWay'></li><li></li><li></li><li></li><li class='u-tabOperation'><span>详情</span><span>编辑</span><span>释放</span></li><div class='clearfix'></div></ul>";
/*公海池*/
var content3 = '<ul class="u-tabList"><li class="u-tabCheckbox"><input type="checkbox"/></li><li class="u-tabCilentName"></li><li></li><li></li><li></li><li></li><li></li><li class="u-tabOperation"><span>申领</span><span>删除</span></li><div class="clearfix"></div></ul>';
//公海池-导入记录
var content6 ='<ul class="u-tabList"><li></li><li></li><li></li><li></li><li></li><li></li><li class="u-tabOperation"><span>失败列表下载</span><span>客户详情</span></li><div class="clearfix"></div></ul>';
/*我的合同*/
var content4 = '<ul class="u-tabList"><li></li><li></li><li></li><li></li><li class="u-tabCilentName"></li><li></li><div class="clearfix"></div></ul>'

var t;
t=5;
for(var i=0;i<t;i++){
	$(".table1").append(content1);
	$(".table2").append(content2);
	$(".table3").append(content3);
	$(".table4").append(content4);
	$(".table6").append(content6);
}
function listNumChange(){
	console.log($(".u-tabControlRow select").val());
	t = $(".u-tabControlRow select").val();
	$(".u-tabList").remove();
	for(var i=0;i<t;i++){
		$(".table1").append(content1);
		$(".table2").append(content2);
		$(".table3").append(content3);
		$(".table4").append(content4);
		$(".table6").append(content6);
	}
}
/*黑色遮罩*/
$("#blackBg").height(window.innerHeight);
var blackBg = document.getElementById("blackBg");
function blackBgshow(){
	blackBg.classList.remove("hide");
}
function blackBghide(){
	blackBg.classList.add("hide");
}

/*checkbox全选*/
$(".u-tabTitle input[type='checkbox']").click(function(){
	if($(this).attr("checked")=="checked"){
		$(this).removeAttr("checked");
		$('.u-tabList input[type="checkbox"]').prop("checked",false);
	}else{
		$(this).attr("checked","checked");
		$('.u-tabList input[type="checkbox"]').prop("checked",true);
	}
});