$("#nav-commu").click(function() {
	$(this).toggleClass("current");
	$(".message-box").toggleClass("hide");
});
$(".message-box .message-class h4").click(function(){
	$(this).addClass("current").siblings("h4").removeClass("current");
});
$(".message-box .message-content-footer").click(function(){
	$(".message-content-container li").removeClass("current");
});