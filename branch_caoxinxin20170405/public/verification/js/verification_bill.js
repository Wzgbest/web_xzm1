// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

$("#frames #verification-billfr .verification_bill .u-tabList .u-tabOperation .approved").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    var bill_no = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
    }
    if($(this).hasClass("bill_no")){
        bill_no = prompt("请输入发票号","");
    }
    var data = "id="+id+"&bill_no="+bill_no+"&remark="+remark;
    $.ajax({
        url: '/verification/bill/approved',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            alert(data.info);
            if(data.status) {
                verification_bill_list_manage.reload_list();
            }
        },
        error: function() {
            alert("通过发票申请时发生错误!");
        }
    });
});

$("#frames #verification-billfr .verification_bill .u-tabList .u-tabOperation .rejected").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/bill/rejected',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            alert(data.info);
            if(data.status) {
                verification_bill_list_manage.reload_list();
            }
        },
        error: function() {
            alert("驳回发票申请时发生错误!");
        }
    });
});