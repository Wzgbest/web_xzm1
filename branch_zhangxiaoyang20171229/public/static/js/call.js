//电话点击
$("#nav-call").click(function(){
    $(this).addClass("current");
	clicker($(this));
	$(".phone-box").removeClass("hide");
    
	phoneWidth = $(".phone-box").width()+10;
	changeFramesSize();
});
$(".phone-box").on("click","i.fa-close",function(){
    $("#nav-call").removeClass("current");
    $(".phone-box").addClass("hide");
    // $(".phone-box").empty();
    phoneWidth = 0;
    $("#phone-number").val("");
    changeFramesSize();
});
$("#frames").on("click","i.fa-phone",function(){
    console.log($(this).parent().text());
    $(".phone-box").removeClass("hide");
    $("#callInput").val($(this).parent().text());
    operation.callBt($(this).parent().text());
});
//按键
var pn = document.getElementById("phone-number");
var number = document.getElementsByClassName("num");
var dele = document.getElementById("num-dele");
/*$(".dial .on").click(function(){
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
        var socket = new WebSocket("ws://192.168.102.50:9002");
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
        console.log("makecall:"+number);
        ws.send("makecall:"+number);
        
//      ws.send('{"user":"phper","pass":123456,"status":1}');
    };

});*/
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



