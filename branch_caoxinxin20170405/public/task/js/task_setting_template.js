//新模板保存的提交事件
$(".daily-task-template .new-template-pop-footer .pop-submit-btn").click(function () {
	let page = $(this).parents(".once").attr("id");
	console.log(page);
	let task_name = $(".new-template-pop .object .staff").val();
	let task_time = "";
	let public_to_take="";
	let target = new Array();		
	let content = ".daily-task-template .new-template-pop .content ";
	for(let i=1;i<=13;i++){
		if(checkCheckd(i).c===true){
			let obj = {
				"target_type":0,
				"target_num":0
			}
			obj.target_type = i;
			obj.target_num = checkCheckd(i).n;
			target.push(obj);
		}
	}
	let data1 = "task_name="+task_name+"&task_time="+task_time+"&public_to_take="+public_to_take+"&target=";
	let data2 = JSON.stringify(target);
	$.ajax({
        url: "/task/day_task/add",
        data: data1+data2,
        type: 'post',
        dataType:'json',
        success: function(data) {
            layer.msg(data.info,{icon:1});
            $(".daily-task-template").addClass("hide").empty();
            // loadPage("/task/setting/index.html", "setting_taskfr");
            reloadPage(page);
        },
        error: function() {
            layer.msg('保存失败',{icon:2});
        }
    });
});
function checkCheckd(e) {
	let result = new Object();
	if($(".daily-task-template .new-template-pop .content ._type"+e)){
		result.c = $(".daily-task-template .new-template-pop .content ._type"+e+" input[type='checkbox']").prop("checked");
		result.n = $(".daily-task-template .new-template-pop .content ._type"+e+" .number input").val();
		return result;
	}else{
		return null;
	}	
}