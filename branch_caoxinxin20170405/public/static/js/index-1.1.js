/*更新了左侧边栏小于1280可点击*/
var phoneWidth = 0;
var sideW,sideSwitch;
//初始化
function init(){
	//根据窗口变化获得屏幕可用尺寸
	sideW = window.innerWidth>1280?220:50;
	sideSwitch = window.innerWidth>1280?false:true;
	$("#side").height(window.innerHeight);
    $("#side-right").height(window.innerHeight);
    $("#side-right").width(window.innerWidth - sideW);
	$("#frames").height(window.innerHeight - 80);
    $(".m-3rd-container").height(window.innerHeight - 120);
    $(".m-4th-container").height(window.innerHeight - 176);
    console.log($("#side").height(),window.innerHeight);

	$(".phone-box").height(window.innerHeight - 80);
	//隐藏副标题
	$("aside dl .ddcontent").addClass("hide");

	if(sideSwitch){
		miniWindow();
	}else{
	    //如果存在简报,高亮
	    if($("aside dl dd[data-subid='index']").length>0){
            $("aside dl").eq(0).addClass("dlcurrent").children().children("i").eq(1).removeClass("fa-angle-right").addClass("fa-angle-down");
            $("aside dl").eq(0).children(".ddcontent").removeClass("hide");
            $("aside dl").eq(0).children("dt").addClass("dtcurrent").siblings(".ddcontent").children("dd").eq(0).addClass("ddcurrent");
        }
	}
}

window.onload =function(){
init();
};


//根据屏幕尺寸，设置侧边栏的可用高度
window.onresize = function() {
    changeFramesSize();
};
//大小窗口切换
$("#x-layout").click(function(){
		sideSwitch = !sideSwitch;
		if(sideSwitch){
			miniWindow();
			sideW = 50;
			changeFramesSize(); 
		}else{
			maxWindow();
			sideW = 220;
			changeFramesSize(); 
		}		
});
function miniWindow(){
	
	$("#side").addClass("mini");
	$(".header").addClass("mini");
	$("section#subt").addClass("mini");
	$("#frames").addClass("mini");
	$("aside dl .ddcontent").addClass("hide");
	$("aside dl").removeClass("dlcurrent");
    $("#logo").addClass("mini");
	asideChange();
}
function maxWindow(){
	$("#side").removeClass("mini");
	$(".header").removeClass("mini");
	$("section#subt").removeClass("mini");
	$("#frames").removeClass("mini");
	$("aside dl dt i.fa-angle-down").addClass("fa-angle-right").removeClass("fa-angle-down");
    $("#logo").removeClass("mini");		
	asideChange();
}
function subResize() {
    var wid = $("#subtitle").width();
    var len = subtitleGroup.length;
    if (wid < len * 152) {
        $("#subtitle>div").width(Math.floor(wid / len) - 42);
        $("#subtitle>div>span").width(Math.floor(wid / len) - 42);
    } else {
        $("#subtitle>div").width(110);
        $("#subtitle>div>span").css("width", "auto");
    }
}

function changeFramesSize() {
	if(sideSwitch){
			sideW = 50;
	}else{
			sideW = 220;
		}
    $("#side").height(window.innerHeight);
    $("#side-right").height(window.innerHeight);
    $("#side-right").width(window.innerWidth - sideW);
    $("#frames").height(window.innerHeight - 80);
    $(".m-3rd-container").height(window.innerHeight - 120);
    $(".m-4th-container").height(window.innerHeight - 176);
    $(".phone-box").height(window.innerHeight - 80);
    subResize();
    console.log($("#side").height(),window.innerHeight);
};

//主标题单机事件
$("aside dl dt").click(function() {
    if(1){
        //主标题右侧的小图标切换
        $(this).children("i").eq(1).toggleClass("fa-angle-right").toggleClass("fa-angle-down");
        //副标题的显示与隐藏切换
        if(!sideSwitch){
        	$(this).siblings(".ddcontent").toggleClass("hide");
        	$(this).parent().toggleClass("dlcurrent");
        }       
    }
});
//创建一个存储子标题的数组
var subtitleGroup = ["index"];
//侧边栏点击
$("aside dl dd").click(function() {
	clicker($(this));
    asideChange();//侧边栏当前显示
    changeFramesSize();
});

function clicker(e){
	//在子标题栏增加新窗口,判断是否已经存在
    var t = e.text();//获取到文字内容
    var v = e.data().subid;//获取到data-subid
    var f = v + "fr";//创建对应frame的id
    var x = subtitleGroup.indexOf(v);//判断data-subid是否已经存在，即是否已经打开
    //当前点击
    if (x == -1) {//不存在
        subtitleGroup.push(v);//数组中添加当前项
        $("#subtitle>div").removeClass("active");//子标题删除当前状态
        //创建新的子标题块div
        var tv = "<div id='" + v + "'class='active' ><span>" + t + "</span><i class='fa fa-close'></i></div>";
        $("#subtitle").append(tv);
        //frame模块隐藏
        $("#frames .once").addClass("hide");
        //创建新的frame块
        var html = '<div id="' + f + '" class="once"></div>';
        $('#frames').append(html); //添加frame块到页面
        var url = e.attr('_src');
        loadPage(url, f);
        $("#frames").height(window.innerHeight - 80);
        //子标题栏长度增加
        subResize();
    } else {//已存在
        //非当前点击
        $("#subtitle>div").removeClass("active");
        $("#"+v).addClass("active");
        frameShow();
    }
}
//侧边栏在当前被删除后的切换
function loadPage(url, panel) {
    $.ajax({
        url: url,
        type: 'get',
        async: false,
        success: function(data) {
            $('#frames #' + panel).html(data);
            changeFramesSize();
        },
        error: function() {
            $('#frames #' + panel).html("页面加载时发生错误!");
        }
    });
}
function loadPagebypost(url, data, panel) {
    $.ajax({
        url: url,
        type: 'post',
        data: data,
        success: function(data) {
            $('#frames #' + panel).html(data);
            changeFramesSize();
        },
        error: function() {
            $('#frames #' + panel).html("页面加载时发生错误!");
        }
    });
    
}
//副标题栏的点击事件
//切换当前的效果
$(document).on('click', '#subtitle>div', function() {
    $(this).addClass("active").siblings().removeClass("active");
    frameShow();
    asideChange();
});
//删除
$(document).on('click', '#subtitle>div i.fa-close', function() {
    //当前删除和非当前删除
    //是否是当前项
    var cla = $(this).parent().attr("class");
    //获取位置
    var id = $(this).parent().attr("id");
    var t = subtitleGroup.indexOf(id);
    //判断位置,添加删除后的active项
    //如果是当前删除，需要判断删除后显示那一个页面
    if (cla == "active") {
        var len = subtitleGroup.length;
        //如果是最后一个位置
        if (t == len - 1) {
            $("#subtitle>div").eq(len - 2).addClass("active");
        } else {
            $("#subtitle>div").eq(t + 1).addClass("active");
        }
    }
    //删除选中项！
    subtitleGroup.splice(t, 1);
    $(this).parent().remove();
    $("#" + $(this).parent().attr("id") + "fr").remove();
    subResize();
    frameShow();
    asideChange();
});
//iframe的展示
function frameShow() {
    //隐藏所有
    $("#frames .once").addClass("hide");
    //根据子标题的当前当前项是谁，判断当前项是谁显示
    document.getElementById($("#subtitle>div.active").attr("id") + "fr").classList.remove("hide");
}
//侧边栏在当前被删除后的切换
function asideChange() {
//  if(window.innerWidth>1280){
	    //隐藏所有
	    $("aside dl dt").removeClass("dtcurrent");
	    $("aside dl dd").removeClass("ddcurrent");
	    //判断当前显示
	    var a = getElementByAttr('dd', 'data-subid', $("#subtitle>div.active").attr("id"))[0];
	    if(a){
	    	a.classList.add("ddcurrent");
	   		var app = a.parentNode.parentNode;
	    	app.getElementsByTagName("dt")[0].classList.add("dtcurrent");	
	    } 
//  }	
      
}
//根据自定义属性 获取元素
function getElementByAttr(tag, attr, value) {
    var aElements = document.getElementsByTagName(tag);
    var aEle = [];
    for (var i = 0; i < aElements.length; i++) {
        if (aElements[i].getAttribute(attr) == value)
            aEle.push(aElements[i]);
    }
    return aEle;
}
$(document).ready(function() {
    var url = "/datacount/index/summary/";
    $.ajax({
        url: url,
        type: 'get',
        async: false,
        success: function(data) {
            $('#indexfr').html(data);
        },
        error: function() {
            layer.msg('简报加载失败!',{icon:2});
        }
    });
});

//删除数据
function delData(options,callback){
    var index=layer.open({
        content: options.title,
        btn: ['确认', '取消'],
        shadeClose: false,
        yes:function(){
            $.ajax({
                type: 'POST',
                url: options.url,
                data: {id:options.id},
                success: function(data) {
                    if (data.status) {
                        layer.msg(data.message, {icon: 1});
                        if(callback){
                            callback();
                        }
                    } else {
                        layer.msg(data.message, {icon: 2});
                    }
                },
                error: function() {
                    console.log('保存失败，未连接到服务器！');
                }
            });
            layer.close(index);
        },
        cancel:function(){
            layer.close(index);
        }
    });
}
