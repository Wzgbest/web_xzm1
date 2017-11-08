// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

//通过
$(".verification_bill_index_pop").on("click",".approved-page-pop .pop-submit-btn",function(){
    var id = $(this).parents(".verification_bill_index_pop").attr("id");
    var remark = $(".verification_bill_index_pop .u-mark").val();
    var bill_no = $(".verification_bill_index_pop .bill-num").val();
/*        if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
        if (remark==null || remark==""){
            return;
        }
    }*/
    // if($(this).hasClass("bill_no")){
    //     bill_no = $(this).attr("bill_no");
    //     bill_no = prompt("请输入发票号",bill_no);
    //     if (bill_no==null || bill_no==""){
    //         return;
    //     }
    // }
    if(bill_no==""){
        alert("请输入发票号");
        return 0;
    }
    var data = "id="+id+"&bill_no="+bill_no+"&remark="+remark;
    $.ajax({
        url: '/verification/bill/approved',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_bill_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('通过发票申请时发生错误!',{icon:2});
        }
    });
});
//驳回
$(".verification_bill_index_pop").on("click",".rejected-page-pop .pop-submit-btn",function(){
    var id = $(this).parents(".verification_bill_index_pop").attr("id");
    var remark = $(".verification_bill_index_pop .rejected-page-radio:checked").val();
    if(remark=="其他原因"){
        remark = $(".verification_bill_index_pop .u-mark").val();
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/bill/rejected',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_bill_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('驳回发票申请时发生错误!',{icon:2});
        }
    });
});
$("#frames #verification-billfr .verification_bill .u-tabList .u-tabOperation .received").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/bill/received',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_bill_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('驳回发票申请时发生错误!',{icon:2});
        }
    });
});