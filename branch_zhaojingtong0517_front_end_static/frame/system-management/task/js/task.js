$(".nav li").click(function(){
	$(".nav li").removeClass("flow");
	$(this).addClass("flow");
});

var content = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t=10;
for(var i=0;i<t;i++){
	$("#myModalone .table").append(content);	
}


var content = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t=10;
for(var i=0;i<t;i++){
	$("#myModaltwo .table").append(content);	
}