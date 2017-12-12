console.log("首页测试");
//  设置
$(".c-target-set").click(function () {
	// body...
	console.log("首页测试");
	loadPage("/datacount/index/target_set","indexfr");
});
$(".datacount_index .back").click(function () {
	// body...
	console.log("回去");
	loadPage("/datacount/index/summary","indexfr");
});