 let status = true;//这个值代表是否处于批量管理状态；true代表正常浏览状态，false代表批量管理状态


//显示文章内容
$(".knowledgebase_speechcraft_index .speech-list li").click(function(){
    article_info_show($(this).attr("data-craft-id"),this);
});
function article_info_show(id,t){  
    status =  !$(".knowledgebase_speechcraft_index .speech-list li").hasClass("manage-list-model"); 
    if (status) {
        var url = "/knowledgebase/speech_craft/show/id/"+id;
        var panel = 'speech-databasefr';
        loadPage(url,panel);
    }else{

    }
}
function search_article(){
    var key_word = $("input[name='key_word']").val();
    var url = "/knowledgebase/speech_craft/index/key_word/"+key_word;
    var panel = 'speech-databasefr';
    loadPage(url,panel);

}
//三级菜单分类切换
$(".knowledgebase_speechcraft_index header .m-firNav li").click(function(){
    show_class_article($(this).attr("speechcraft_in_column"),$(this).index());
});
function show_class_article(class_id,num){  
    var url = "/knowledgebase/speech_craft/index/class_id/"+class_id;
    var panel = 'speech-databasefr';
    loadPage(url,panel);
    $(".knowledgebase_speechcraft_index .in_column").eq(num).addClass("current").siblings().removeClass("current");
}
//文章编辑
$(".knowledgebase_speechcraft_index .speech-list li .controler .edit3").click(function(){
    console.log(111);
    edit_article_info($(this).attr("data-id"),this);
});
function edit_article_info(id,t){
    var url = "/knowledgebase/speech_craft/add_article/id/"+id;
    var panel = 'speech-databasefr';
    loadPage(url,panel);
}
// 批量管理

let status2 = true;//这个值代表操作全选按钮
$(".knowledgebase_speechcraft_index .batch-manage").click(function(){
    status =  !$(".knowledgebase_speechcraft_index .speech-list li").hasClass("manage-list-model");
    if(status){
        in_batch_manage();
        status = !status;//状态变更
        status2 = true;
        console.log("A");
    }else{
        out_batch_manage();
        status = !status;//状态变更
        status2 = false;
        console.log("B");
    }    
});
function in_batch_manage(){
    $(".knowledgebase_speechcraft_index .batch-manage").children(".out").removeClass("hide");
    $(".knowledgebase_speechcraft_index .speech-list li").addClass("manage-list-model");
    $(".knowledgebase_speechcraft_index .manageNav").removeClass("hide");
    $(".knowledgebase_speechcraft_index .batch-delete").removeClass("hide");
    $(".knowledgebase_speechcraft_index .batch-change").removeClass("hide");
}
function out_batch_manage(){
    $(".knowledgebase_speechcraft_index .batch-manage").children(".out").addClass("hide");
    $(".knowledgebase_speechcraft_index .speech-list li").removeClass("manage-list-model");
    $(".knowledgebase_speechcraft_index .manageNav").addClass("hide");
    $(".knowledgebase_speechcraft_index .batch-delete").addClass("hide");
    $(".knowledgebase_speechcraft_index .batch-change").addClass("hide");
}
//批量管理时分类选择
$(".knowledgebase_speechcraft_index .select_class").change(function(){
        let val = $(this).val();
        let arr = getElementByAttr("li", "speechcraft_in_column", val);
        console.log($(arr[0]).index());

        var url = "/knowledgebase/speech_craft/index/class_id/"+$(this).val();
        var panel = 'speech-databasefr';
        loadPage(url,panel);
        $(".knowledgebase_speechcraft_index .in_column").eq($(arr[0]).index()).addClass("current").siblings().removeClass("current");
        in_batch_manage();
    });
//全选中操作
$(".knowledgebase_speechcraft_index .manageNav input[type='checkbox']").click(function(){
    if(status2){
        $(".knowledgebase_speechcraft_index .speech-list li input[type='checkbox']").attr("checked","checked").prop("checked",true);
        $(this).attr("checked","checked").prop("checked",true);
        status2=!status2;
    }else{
        $(".knowledgebase_speechcraft_index .speech-list li input[type='checkbox']").removeAttr("checked").prop("checked",false);
        $(this).removeAttr("checked").prop("checked",false);
        status2=!status2;
    }
});
//单个删除
$(".knowledgebase_speechcraft_index .speech-list li .controler .delete").click(function(){
    var article_id = $(this).attr('data-id');
    var delete_ids = "ids[]="+article_id;
    $.ajax({
        url: '/knowledgebase/speech_craft/delete',
        type: 'POST',
        dataType: 'json',
        data: delete_ids,
        success:function(data){
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                loadPage("/knowledgebase/speech_craft/index", "speech-databasefr");
            }
        },
        error:function(){
            layer.msg('删除失败!',{icon:2});
        }
    }); 
});
//批量删除
$(".knowledgebase_speechcraft_index .batch-delete").click(function(){
    let pop =  new popLoad(".knowledgebase_speechcraft_index .delete-change-set-container","/knowledgebase/speech_craft/confirmBatchDel");
});
//批量更改分类
$(".knowledgebase_speechcraft_index .batch-change").click(function(){
     let pop =  new popLoad(".knowledgebase_speechcraft_index .delete-change-set-container","/knowledgebase/speech_craft/confirmBatchChange");
});
//删除话术，修改分类，设置置顶
$(".knowledgebase_speechcraft_index .delete-change-set").change(function(){
    let id = $(this).parents().attr("data-craft-id");
    //删除话术
    if($(this).val()==1){
        let pop =  new popLoad(".knowledgebase_speechcraft_index .delete-change-set-container","/knowledgebase/speech_craft/deleteSpeechList");
        $(".delete-change-set-container .deleteCraft").attr("data-craft-id",id);
    }else if($(this).val()==2){//单个修改分类
        let pop =  new popLoad(".knowledgebase_speechcraft_index .delete-change-set-container","/knowledgebase/speech_craft/changeSpeechClass");
        $(".delete-change-set-container .change_class_form").attr("data-id",id);
    }else if($(this).val()==3){//设置置顶
        let pop =  new popLoad(".knowledgebase_speechcraft_index .delete-change-set-container","/knowledgebase/speech_craft/setTop");
        $(".delete-change-set-container .set_top_form").attr("data-id",id);
    }
    $(this).val(0);
});
//删除话术分类
$(".knowledgebase_speechcraft_index .delete-speech-class").click(function(){
    let pop =  new popLoad(".knowledgebase_speechcraft_index .delete-change-set-container","/knowledgebase/speech_craft/deleteSpeechClass");
});
