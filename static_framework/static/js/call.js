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