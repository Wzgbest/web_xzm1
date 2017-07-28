var phoneWidth = 0;
//初始化
function init(){
	//根据窗口变化获得屏幕可用尺寸
	$("#side").height(window.innerHeight);
	$("header").width(window.innerWidth - 220);
	$("section#subt").width(window.innerWidth - 220);
	$("#subtitle").width(window.innerWidth - 220);
	$("#frames").width(window.innerWidth - 220);
	$("#frames").height(window.innerHeight - 80);
	$("#frames .once").width(window.innerWidth - 220);
	$("#frames .once").height(window.innerHeight - 80);
	$(".phone-box").height(window.innerHeight - 80);
	//隐藏副标题
	$("aside dl dd").addClass("hide");
	//默认事件
	$("aside dl").eq(0).addClass("dlcurrent").children().children("i").eq(1).removeClass("fa-angle-right").addClass("fa-angle-down");
	$("aside dl").eq(0).children("dd").removeClass("hide");
	$("aside dl").eq(0).children("dt").addClass("dtcurrent").siblings("dd").eq(0).addClass("ddcurrent");
}
init();

//根据屏幕尺寸，设置侧边栏的可用高度
window.onresize = function() {
    changeFramesSize();
};

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
    $("#side").height(window.innerHeight);
    $(".header").width(window.innerWidth - 220);
    $("section#subt").width(window.innerWidth - 220);
    $("#subtitle").width(window.innerWidth - 220);
    $("body").width(window.innerWidth);
    $("body").height(window.innerHeight);
    $("#frames").width(window.innerWidth - 220 -phoneWidth);
    $("#frames").height(window.innerHeight - 80);
    $("#frames .once").width(window.innerWidth - 220-phoneWidth);
    $("#frames .once").height(window.innerHeight - 80);
    $(".phone-box").height(window.innerHeight - 80);
    subResize();
};

//主标题单机事件
$("aside dl dt").click(function() {
    //主标题右侧的小图标切换
    $(this).children("i").eq(1).toggleClass("fa-angle-right").toggleClass("fa-angle-down");
    //副标题的显示与隐藏切换
    $(this).siblings("dd").toggleClass("hide");
    $(this).parent().toggleClass("dlcurrent");
});
//创建一个存储子标题的数组
var subtitleGroup = ["index"];
//侧边栏点击
$("aside dl dd").click(function() {
	clicker($(this));
    asideChange();//侧边栏当前显示
    changeFramesSize()
});
//电话点击
$("#nav-call").click(function(){
	clicker($(this));
	$(".phone-box").removeClass("hide");
	phoneWidth = $(".phone-box").width()+10;
	changeFramesSize();
});
$(".phone-box i.fa-close").click(function(){
	$(".phone-box").addClass("hide");
	phoneWidth = 0;
	$("#phone-number").val("");
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
        $("#frames .once").addClass("hid");
        //创建新的frame块
        var html = '<div id="' + f + '" class="once"></div>';
        $('#frames').append(html); //添加frame块到页面
        var url = e.attr('_src');
        loadPage(url, f);
        $("#frames").width(window.innerWidth - 220);
        $("#frames").height(window.innerHeight - 80);
        $("#frames .once").width(window.innerWidth - 220);
        $("#frames .once").height(window.innerHeight - 80);
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
    $("#frames .once").addClass("hid");
    //根据子标题的当前当前项是谁，判断当前项是谁显示
    document.getElementById($("#subtitle>div.active").attr("id") + "fr").classList.remove("hid");
}
//侧边栏在当前被删除后的切换
function asideChange() {
    //隐藏所有
    $("aside dl dt").removeClass("dtcurrent");
    $("aside dl dd").removeClass("ddcurrent");
    //判断当前显示
    var a = getElementByAttr('dd', 'data-subid', $("#subtitle>div.active").attr("id"))[0];
    if(a){
    	a.classList.add("ddcurrent");
   		var ap = a.parentNode;
    	ap.getElementsByTagName("dt")[0].classList.add("dtcurrent");	
    }   
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
            alert("简报加载失败!");
        }
    });
});
//按键
var pn = document.getElementById("phone-number");
var number = document.getElementsByClassName("num");
var dele = document.getElementById("num-dele");
/*for(var i=0;i<number.length;i++){
	number[i].onclick = function(){
		var txt =document.createTextNode(this.innerHTML);
//		var num = pn.getAttribute("value");
//		pn.setAttribute("value",num+txt);
		pn.appendChild(txt);
	}
}
dele.onclick = function(){
	pn.setAttribute("value","");
}*/
/*pn.onfocus = function(){
		document.onkeydown=function(event){
    	var e = event || window.event || arguments.callee.caller.arguments[0];
    	if(e && e.keyCode==96){ // 按 Esc 
    		console.log(0);
    		var num = pn.getAttribute("value");
			pn.setAttribute("value",num+0);
    	}
    	if(e && e.keyCode==97){ // 按 F2 
            var num = pn.getAttribute("value");
			pn.setAttribute("value",num+1);
    	}            
    	if(e && e.keyCode==98){ // enter 键
             var num = pn.getAttribute("value");
			pn.setAttribute("value",num+2);
    	}
	}; 
}*/
$(".dial .on").click(function(){
	var number = $("#phone-number").val();
	//    html5 websocket implement
    var ws;
//    处理消息
    function handleMessage (mes) {
        if (mes != undefined) {
            mes = JSON.parse(mes);
            if (typeof(mes) == 'string') {
                mes = JSON.parse(mes);
            }
            console.log(mes);
            if (mes.status != undefined) {
                if (mes.status ==1) {
                    console.log('calling...');
                } else if (mes.status == 2) {
                    console.log('hang off...');
                }
            }
        }
    }
//    获取连接
    function getWs () {
        var socket = new WebSocket("ws://webcall.app:8001");
        return socket;
    }
   ws = getWs();

//    接收消息
    ws.onmessage = function(e){
        console.log('reveive rawdata from server');
//        var data = eval("("+e.data+")");
//        console.log(e.data);
        handleMessage(e.data);
    };
//    服务端关闭
    ws.onclose = function (e) {
        console.log('connect closed');
    };
//    连接建立
    ws.onopen = function (e) {
        console.log('connect established');
        ws.send(number)
//      ws.send('{"user":"phper","pass":123456,"status":1}');
    };

});
//打点
$(".click-node .fa-plus").click(function(){
	var li = '<li><span>00:00:61</span>&nbsp;&nbsp;<input type="text"/>&nbsp;&nbsp;<i class="fa fa-check-circle-o"></i><i class="fa fa-pencil hide"></i>&nbsp;&nbsp;<i class="fa fa-times-circle-o"></i></li>';
	$(".nodes").append(li);
});
$(document).on('click', '.nodes .fa-check-circle-o', function() {
	$(this).siblings("input").attr("readonly","readonly");
	$(this).addClass("hide");
	$(".nodes .fa-pencil").removeClass("hide");
});

$(document).on('click', '.nodes .fa-pencil', function() {
	$(this).siblings("input").removeAttr("readonly");
	$(this).addClass("hide");
	$(".nodes .fa-check-circle-o").removeClass("hide");
});
$(document).on('click', '.nodes .fa-times-circle-o', function() {
	$(this).parent().remove();
});