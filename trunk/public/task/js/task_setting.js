//初始化
function task_setting_init() {
	let val = JSON.parse($(".day_task_info_list").val());
	if(Object.keys(val).length!=0){
		let id = $(".task_setting_index .daily-task .panel-name li.current").attr("data-day_task_id");
		task_setting_set(val,id);
	}
}
task_setting_init();
//重置
function task_setting_reset() {
	$(".task_setting_index .daily-task .content .container ul .check input").removeAttr("checked").prop("checked",false);
	$(".task_setting_index .daily-task .content .container ul .number input").val("");
}
//设置
function task_setting_set(val,id) {
	let ele = val[id];
	for( x in ele){
		$(".task_setting_index .daily-task .content .container ul._type"+x+" .check input").attr("checked","checked").prop("checked",true);
		$(".task_setting_index .daily-task .content .container ul._type"+x+" .number input").val(ele[x]);
	}
}
//模板切换
$(".task_setting_index .daily-task .panel-name li").not(".new-template").click(function () {
	$(this).addClass("current").siblings().removeClass("current");
	let id = $(this).attr("data-day_task_id");
	let val = JSON.parse($(".day_task_info_list").val());
	task_setting_reset();
	task_setting_set(val,id);
});
//新模板弹窗事件
$(".task_setting_index .new-template").click(function(){
    let pop =  new popLoad(".daily-task-template","/task/setting/template/");
});
//测试
$(".daily-task-template .new-template-pop .content input[type='checkbox']").click(function () {
	// console.log(checkCheckd(6));
});

//删除模板
$(".task_setting_index .daily-task .panel-name li .fa-close").click(function () {
	// body...
	let id = $(this).parent().attr("data-day_task_id");
	let data = "task_id="+id;
	$.ajax({
        url: "/task/day_task/del",
        data: data,
        type: 'post',
        dataType:'json',
        success: function(data) {
            console.log(data);
            layer.msg(data.info,{icon:1});
            reloadPage("setting_taskfr");
        },
        error: function() {
            layer.msg('删除失败',{icon:2});
        }
    });
});