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
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/index/approved',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            alert(data.info);
            if(data.status) {
                verification_sale_chance_list_manage.reload_list();
            }
        },
        error: function() {
            alert("通过成单申请时发生错误!");
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
            alert(data.info);
            if(data.status) {
                verification_sale_chance_list_manage.reload_list();
            }
        },
        error: function() {
            alert("驳回成单申请时发生错误!");
        }
    });
});