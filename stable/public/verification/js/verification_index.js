// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

$("#frames #verification-indexfr .verification_sale_chance .u-tabList .u-tabOperation .approved").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
        if (remark==null || remark==""){
            return;
        }
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/index/approved',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_sale_chance_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('通过成单申请时发生错误!',{icon:2});
        }
    });
});

$("#frames #verification-indexfr .verification_sale_chance .u-tabList .u-tabOperation .rejected").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/index/rejected',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_sale_chance_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('驳回成单申请时发生错误!',{icon:2});
        }
    });
});
//详情展示
$(".verification_sale_chance_index .u-tabList .verification-sale-chance-show").click(function(){
    var id = $(this).siblings(".u-tabCheckbox").children("input").val();
    var ids = $(this).attr("ids");
    console.log("id",id);
    loadPage("/verification/index/detail/id/"+id+"/ids/"+ids, "verification-indexfr");
});
$(".verification_sale_chance_detail header .back").click(function(){
    loadPage("/verification/index/index", "verification-indexfr");
});
$(".verification_sale_chance_detail .detail_panel_footer .next-btn").click(function(){
    var id = $(this).attr("id");
    console.log("id",id);
    var ids = $(".verification_sale_chance_detail .sale-chance-record-ids").val();
    loadPage("/verification/index/detail/id/"+id+"/ids/"+ids, "verification-indexfr");
});
$(".verification_sale_chance_detail .detail_panel_footer .previous-btn").click(function(){
    var id = $(this).attr("id");
    console.log("id",id);
    var ids = $(".verification_sale_chance_detail .sale-chance-record-ids").val();
    loadPage("/verification/index/detail/id/"+id+"/ids/"+ids, "verification-indexfr");
});
//成单申请详情页通过
$(".verification_sale_chance_detail .approved-btn").click(function(){
    let id = $(".verification_sale_chance_detail .sale-chance-record-id").val();
    var remark = $(".verification_sale_chance_detail .u-remark").val();
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/index/approved',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_sale_chance_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('通过成单申请时发生错误!',{icon:2});
        }
    });
});
//成单申请详情页驳回
$(".verification_sale_chance_detail .rejected-btn").click(function(){
    let id = $(".verification_sale_chance_detail .sale-chance-record-id").val();
    var remark = $(".verification_sale_chance_detail .u-remark").val();
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/index/rejected',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_sale_chance_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('驳回成单申请时发生错误!',{icon:2});
        }
    });
});