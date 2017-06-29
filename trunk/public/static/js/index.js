//根据窗口变化获得屏幕可用尺寸
//var con_wid = window.innerWidth-220;
//console.log(con_wid);
$("#side").height(window.innerHeight);
$("header").width(window.innerWidth-220);
$("section#subt").width(window.innerWidth-220);
$("#subtitle").width(window.innerWidth-220);
$("#frames .once").width(window.innerWidth-220);
//根据屏幕尺寸，设置侧边栏的可用高度
window.onresize=function(){
	changeH();
};
function subResize(){
	var wid = $("#subtitle").width();
	var len =subtitleGroup.length;
	if(wid<len*152){
			$("#subtitle>div").width(Math.floor(wid/len)-42);
			$("#subtitle>div>span").width(Math.floor(wid/len)-42);
		}else{
			$("#subtitle>div").width(110);
			$("#subtitle>div>span").css("width","auto");
		}
}
function changeH(){
	//console.log(window.innerHeight);
//	var wid = window.innerWidth-220;
	$("aside").height(window.innerHeight);
	$("header").width(window.innerWidth-220);
	$("section#subt").width(window.innerWidth-220);
	$("#subtitle").width(window.innerWidth-220);
	$("#frames .once").width(window.innerWidth-220);
	subResize();
};


//初始化
//隐藏副标题
$("aside dl dd").addClass("hide");
//默认事件
$("aside dl").eq(0).addClass("dlcurrent").children().children("i").eq(1).removeClass("fa-angle-right").addClass("fa-angle-down");
$("aside dl").eq(0).children("dd").removeClass("hide");
$("aside dl").eq(0).children("dt").addClass("dtcurrent").siblings("dd").eq(0).addClass("ddcurrent");


//主标题单机事件
$("aside dl dt").click(function(){
	//主标题右侧的小图标切换
	$(this).children("i").eq(1).toggleClass("fa-angle-right").toggleClass("fa-angle-down");
	//副标题的显示与隐藏切换
	$(this).siblings("dd").toggleClass("hide");
	$(this).parent().toggleClass("dlcurrent");
});
//创建一个存储子标题的数组
var subtitleGroup = ["index"];
//侧边栏点击
$("aside dl dd").click(function(){
	$("aside dl dd").removeClass("ddcurrent");
	$("aside dl dt").removeClass("dtcurrent");
	$(this).addClass("ddcurrent");
	$(this).siblings("dt").addClass("dtcurrent");
	//在子标题栏增加新窗口,判断是否已经存在
	var t = $(this).text();
	var v = $(this).data().subid;
	var f = v+"fr";
	var x= subtitleGroup.indexOf(v);
	//console.log(t,v,x,f,subtitleGroup);
	//当前点击
	if(x==-1){
		subtitleGroup.push(v);
		$("#subtitle>div").removeClass("active");
		var tv = "<div id='"+v+"'class='active' ><span>"+t+"</span><i class='fa fa-close'></i></div>";
		$("#subtitle").append(tv);
		//frame
		$("#frames .once").addClass("hid");
		//var fr = "<div src='"+$(this).attr("_src")+"' id='"+f+"' class='once'></div>";
		//$("#frames").append(fr);
		var html = '<div id="'+f+'" class="once"></div>';
		$('#frames').append(html);
		var url = $(this).attr('_src');
		loadPage(url,f);
		$("#frames .once").width(window.innerWidth-220);
		//子标题栏长度增加
		subResize();
	}else{
		//非当前点击
		/*$("#frames .once").addClass("hid");
		document.getElementById(f).classList.remove("hid");*/
		$("#subtitle>div").removeClass("active");
		//document.getElementById(v).setAttribute("class","active");
		document.getElementById(v).classList.add("active");
		frameShow();
	}
});
//侧边栏在当前被删除后的切换
function loadPage(url,panel){
	$.ajax({
		url:url,
		type:'get',
		async:false,
		success:function (data) {
			$('#frames #'+panel).html(data);
		}
	});
}
//副标题栏的点击事件
//切换当前的效果
$(document).on('click','#subtitle>div',function(){
	$(this).addClass("active").siblings().removeClass("active");
	//console.log($(this).attr("id"));
//	$("#frames .once").addClass("hid");
//	document.getElementById($(this).attr("id")+"fr").classList.remove("hid");
	frameShow();
	asideChange();
});
//删除
$(document).on('click','#subtitle>div i.fa-close',function(){
	//当前删除和非当前删除
	//是否是当前项
	var cla = $(this).parent().attr("class");
	//console.log(cla);
	//获取位置
	var id = $(this).parent().attr("id");
	var t = subtitleGroup.indexOf(id);
	//判断位置,添加删除后的active项
	//如果是当前删除，需要判断删除后显示那一个页面
	if(cla=="active"){
		//console.log(22222);
		var len = subtitleGroup.length;
		//如果是最后一个位置
		if(t==len-1){
			$("#subtitle>div").eq(len-2).addClass("active");
		}else{
			$("#subtitle>div").eq(t+1).addClass("active");
		}
	}
	//删除选中项！
	subtitleGroup.splice(t,1);
	//console.log(subtitleGroup);
	$(this).parent().remove();
	//console.log($(this).parent().attr("id"));
	$("#"+$(this).parent().attr("id")+"fr").remove();
	subResize();
	frameShow();
	asideChange();
});
//iframe的展示
function frameShow(){
	//隐藏所有
	$("#frames .once").addClass("hid");
	//判断当前项是谁显示
	document.getElementById($("#subtitle>div.active").attr("id")+"fr").classList.remove("hid");
}
//侧边栏在当前被删除后的切换
function asideChange(){
	//隐藏所有
	$("aside dl dt").removeClass("dtcurrent");
	$("aside dl dd").removeClass("ddcurrent");
	//判断当前显示
	var a = getElementByAttr('dd','data-subid',$("#subtitle>div.active").attr("id"))[0];
	//console.log(a);
/*	a.addClass("ddcurrent");
	a.sibling("dt").addClass("dtcurrent");*/
	a.classList.add("ddcurrent");
	var ap = a.parentNode;
	//console.log(ap.getElementsByTagName("dt")[0]);
	ap.getElementsByTagName("dt")[0].classList.add("dtcurrent");
	
	/*var apc = ap.firstChild;
	//console.log(a);
	//console.log(ap);*/
	//console.log(apc);
	//a.parentNode().firstChild().classList.add("dtcurrent");
	
}
//根据自定义属性 获取元素
function getElementByAttr(tag,attr,value)
{
    var aElements=document.getElementsByTagName(tag);
    var aEle=[];
    for(var i=0;i<aElements.length;i++)
    {
        if(aElements[i].getAttribute(attr)==value)
            aEle.push( aElements[i] );
    }
    return aEle;
}
/*
    (function($){
        $(window).load(function(){
            $(".content").mCustomScrollbar();
        });
    })(jQuery);*/

