
$(document).on("click",".remark i.fa-pencil",function(){
    $(this).siblings("input").removeAttr("readonly").focus();
    $(this).addClass("hide").siblings(".fa-check").removeClass("hide");
});
$(document).on("click",".remark i.fa-close",function(){
    var that=$(this);
    var id=$(this).siblings("input").attr('data-id')||'';//需要删除的标签id
    if(!id)
    {
        //未输入标签保存到表的可直接移除
        that.parent(".remark").remove();
        return;
    }
    $.ajax({
        url: '/crm/customer_remark/delete',
        type: 'post',
        data: {'id':id},
        success: function(data) {
            if(data.success)
            {
                that.parent(".remark").remove();
            }
            else
            {
                alert(data.msg);
            }
        },
        error: function() {
            alert("申请时发生错误!");
        }
    });

});
$(document).on("click",".remark i.fa-check",function(){
    var that=$(this);
    var id=$(this).siblings("input").attr('data-id')||'';//id有值编辑，未定义则新增
    var title=$(this).siblings("input").val();
    if(!title)
    {
        alert('请输入标签名称');
        that.siblings("input").removeAttr("readonly").focus();
        return;
    }
    that.siblings("input").attr("readonly","readonly");
    $.ajax({
        url: '/crm/customer_remark/edit',
        type: 'post',
        data: {'id':id,'title':title},
        success: function(data) {
            if(data.success)
            {
                that.addClass("hide").siblings(".fa-pencil").removeClass("hide");
                if(data.num)
                {
                    that.siblings("input").attr('data-id',data.num);//新增的将id传回来
                }
            }
            else
            {
                alert(data.msg);
            }
        },
        error: function() {
            alert("申请时发生错误!");
        }
    });
});
$(".remark input").off("click");//解绑点击事件
$(document).on("click",".remark input",function(){
    if($(this).attr("readonly")){
        var tex = $(this).parent(".remark").siblings("textarea[name='remark']");
        tex.val(tex.val()+$(this).val());
    }
});

var txt = '<span class="remark"><input type="text" placeholder="请输入" /><i class="fa fa-pencil hide"></i><i class="fa fa-check"></i><i class="fa fa-close"></i></span>';
$(".u-addRemark").click(function(){
    $(this).before(txt).siblings(".remark").last().children("input").focus();
});

$(document).on("click",".u-addRemark",function(){
    $(this).before(txt).siblings(".remark").last().children("input").focus();
});