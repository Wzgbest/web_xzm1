
$(document).on("click",".remark i.fa-pencil",function(){
    $(this).siblings("input").removeAttr("readonly").focus().val("");
    $(this).addClass("hide").siblings(".fa-check").removeClass("hide");
});
$(document).on("click",".remark i.fa-close",function(){
    $(this).parent(".remark").remove();
});
$(document).on("click",".remark i.fa-check",function(){
    $(this).siblings("input").attr("readonly","readonly");
    $(this).addClass("hide").siblings(".fa-pencil").removeClass("hide");
});
$(document).on("click",".remark input",function(){
    if($(this).attr("readonly")){
    	let tex = $(this).parent(".remark").siblings("textarea[name='remark']");
        tex.val(tex.val()+$(this).val());
    }
});
var txt = '<span class="remark"><input type="text" placeholder="请输入" /><i class="fa fa-pencil hide"></i><i class="fa fa-check"></i><i class="fa fa-close"></i></span>';
$(document).on("click",".u-addRemark",function(){
    $(this).before(txt).siblings(".remark").last().children("input").focus();
});